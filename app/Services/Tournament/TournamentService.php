<?php

namespace App\Services\Tournament;

use Exception;
use App\Models\Team;
use App\Models\Match;
use App\Models\Tournament;
use Illuminate\Support\Collection;

class TournamentService
{
    /**
     * Inscriptions d'une équipe à un tournoi
     *
     * @param \App\Models\Tournament $tournament
     * @param string $name
     * @return void
     * @throws \App\Services\Tournament\SubscribeException
     */
    public function subscribe(Tournament $tournament, string $name) : void
    {
        $nameAvailable = $this->checkNameAvailable($tournament, $name);

        if (! $nameAvailable) {
            throw new SubscribeException(trans('layouts.tournaments.subscribe.error.name_exists'));
        }

        $slotsAvailable = $this->checkSlotsAvailable($tournament);

        if (! $slotsAvailable) {
            throw new SubscribeException(trans('layouts.tournaments.subscribe.error.max_slots'));
        }

        $team = new Team([
            'name' => $name,
        ]);

        $tournament->teams()->save($team);
    }

    /**
     * Supprime une équipe d'un tournoi
     *
     * @param \App\Models\Tournament $tournament
     * @param \App\Models\Team $team
     * @return void
     */
    public function unsubscribe(Tournament $tournament, Team $team) : void
    {
        $tournament->teams()->where('id', $team->id)->delete();
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
     * @param \App\Models\Tournament $tournament
     * @return mixed
     */
    public function getMatchs(Tournament $tournament)
    {
        $matchesByRound = $tournament->matches->groupBy('round');

        return $matchesByRound->map(function ($round) {
            $matchList = $this->getMatchesFromRound($round);

            return [
                'complete' => $this->roundIsComplete($matchList),
                'matches' => $matchList,
            ];
        });
    }

    /**
     * @param array $data
     * @param \App\Models\Match $match
     * @throws \Exception
     */
    public function setScores(array $data, Match $match)
    {
        $match->load('teams');

        foreach ($match->teams as $team) {
            if ($this->teamNotExistForMatch($team, $data)) {
                throw new Exception('Score team not found');
            }

            $match->teams()->updateExistingPivot($team, ['score' => (int) $data['teams'][$team->id]]);
        }

        $this->setCompleteMatch($match);
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
     * @return boolean
     */
    protected function checkNameAvailable(Tournament $tournament, string $name) : bool
    {
        $target = $tournament->teams->where('name', $name);

        return $target->isEmpty();
    }

    /**
     * Check la disponibilité du nom d'équipe
     *
     * @param \App\Models\Tournament $tournament
     * @return boolean
     */
    protected function checkSlotsAvailable(Tournament $tournament) : bool
    {
        $teams = $tournament->teams->all();

        return $tournament->slots > count($teams);
    }

    /**
     * @param \Illuminate\Support\Collection $matches
     * @return bool
     */
    protected function roundIsComplete(Collection $matches): bool
    {
        foreach ($matches as $match) {
            if ($match->isComplete()) {
                continue;
            }

            return false;
        }

        return true;
    }

    /**
     * @param \App\Models\Match $match
     */
    protected function setCompleteMatch(Match $match): void
    {
        $match->update([
            'status' => 'complete',
        ]);
    }

    /**
     * @param \Illuminate\Support\Collection $round
     * @return \Illuminate\Support\Collection
     */
    protected function getMatchesFromRound(Collection $round): Collection
    {
        return $round->map(function ($match) {
            $match->load('teams');
            return $match;
        });
    }

    /**
     * @param \App\Models\Team $team
     * @param array $data
     * @return bool
     */
    protected function teamNotExistForMatch(Team $team, array $data)
    {
        return ! in_array($team->id, array_keys($data['teams']));
    }
}
