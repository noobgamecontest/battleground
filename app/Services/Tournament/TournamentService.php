<?php

namespace App\Services\Tournament;

use Carbon\Carbon;
use App\Models\Team;
use App\Models\Match;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\Tournament\SubscribeException;
use App\Exceptions\Tournament\TournamentNotReadyException;
use phpDocumentor\Reflection\Types\Boolean;

class TournamentService
{
    /**
     * Return all availables tournaments
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllAvailables() : Collection
    {
        return Tournament::where('started_at', '>', Carbon::now())
            ->whereNull('ended_at')
            ->get();
    }

    /**
     * Inscriptions d'une équipe à un tournoi
     *
     * @param array $attrs
     * @throws \App\Exceptions\Tournament\SubscribeException
     * @return void
     */
    public function subscribe(array $attrs) : void
    {
        $tournament = Tournament::find($attrs['tournamentId']);
        $nameAvailable = $this->checkNameAvailable($tournament, $attrs['teamName']);

        if (! $nameAvailable) {
            throw new SubscribeException(trans('layouts.tournaments.subscribe.error.name_exists'));
        }

        $slotsAvailable = $this->checkSlotsAvailable($tournament);

        if (! $slotsAvailable) {
            throw new SubscribeException(trans('layouts.tournaments.subscribe.error.max_slots'));
        }

        $team = new Team([
            'name' => $attrs['teamName'],
        ]);

        $tournament->teams()->save($team);
    }

    /**
     * Supprime une équipe d'un tournoi
     *
     * @param $id
     * @return void
     */
    public function unsubscribe($id) : void
    {
        $team = Team::find($id);

        $team->delete();
    }

    /**
     * Démarre un Tournoi
     *
     * @param Tournament $tournament
     * @throws TournamentNotReadyException
     */
    public function launch(Tournament $tournament) : void
    {
        if (! $tournament->readyToLaunch()) {
            throw new TournamentNotReadyException();
        }

        $this->buildTree($tournament);
        $this->distribTeamsForFirstRound($tournament);
    }

    /**
     * Construit l'arbre et les matchs du tournoi.
     *
     * @param Tournament $tournament
     * @return void
     */
    protected function buildTree(Tournament $tournament) : void
    {
        $numberOfRounds = $this->getNumberOfRounds(
            $tournament->teams()->count(),
            $tournament->opponents_by_match,
            $tournament->winners_by_match
        );

        for ($roundIndex = $numberOfRounds; $roundIndex >= 0; $roundIndex--) {
            $numberOfSlotsInThisRound = $this->getNumberOfSlotsForThisRound(
                $roundIndex,
                $tournament->opponents_by_match,
                $tournament->winners_by_match
            );

            $numberOfMatchesInThisRound = $numberOfSlotsInThisRound / $tournament->opponents_by_match;

            for ($matchIndex = 0; $matchIndex < $numberOfMatchesInThisRound; $matchIndex++) {
                $match = new Match([
                    'round' => $roundIndex,
                    'name' => "Match #$matchIndex for round #$roundIndex",
                ]);

                $tournament->matches()->save($match);
            }
        }
    }

    /**
     * Distribue aléatoirement les équipes pour le 1er tound
     *
     * @param Tournament $tournament
     */
    protected function distribTeamsForFirstRound(Tournament $tournament) : void
    {
        $matches = $tournament->matches;

        $firstRoundMatches = $matches->where('round', $matches->max('round'));

        $numberOfSlots = $firstRoundMatches->count() * $tournament->opponents_by_match;

        $teams = $tournament->teams->pad($numberOfSlots, null)->shuffle();

        $teamsChunks = $teams->chunk($tournament->opponents_by_match);

        foreach ($firstRoundMatches as $chunk => $match) {
            $match->teams()->attach($teamsChunks->get($chunk)->pluck('id')->filter());
        }
    }

    /**
     * Retourne le nombre de tours en fonction du nombre de joueur dans le tournoi, par match et de gagnant par match.
     * Sachant qu'on a 0 pour 1 tour, 1 pour 2 tours, etc ...
     *
     * @param int $numberOfPlayers
     * @param int $numberOfTeamsByMatch
     * @param int $numberOfWinnersByMatch
     * @return int
     */
    protected function getNumberOfRounds(int $numberOfPlayers, int $numberOfTeamsByMatch, int $numberOfWinnersByMatch) : int
    {
        return ceil(
            log($numberOfPlayers / $numberOfTeamsByMatch) / log($numberOfTeamsByMatch / $numberOfWinnersByMatch)
        );
    }

    /**
     * Retourne le nombre de place disponible pour le tour donné.
     *
     * @param int $roundIndex
     * @param int $numberOfTeamsByMatch
     * @param int $numberOfWinnersByMatch
     * @return int
     */
    protected function getNumberOfSlotsForThisRound(int $roundIndex, int $numberOfTeamsByMatch, int $numberOfWinnersByMatch) : int
    {
        return pow($numberOfTeamsByMatch / $numberOfWinnersByMatch, $roundIndex) * $numberOfTeamsByMatch;
    }

    /**
     * Check la disponibilité du nom d'équipe
     *
     * @param \App\Models\Tournament $tournament
     * @param string $name
     * @return mixed
     */
    protected function checkNameAvailable(Tournament $tournament, string $name)
    {
        $target = $tournament->teams->where('name', $name);

        return $target->isEmpty();
    }

    /**
     * Check la disponibilité du nom d'équipe
     *
     * @param \App\Models\Tournament $tournament
     * @return mixed
     */
    protected function checkSlotsAvailable(Tournament $tournament)
    {
        $teams = $tournament->teams->all();

        return $tournament->slots > count($teams);
    }
}
