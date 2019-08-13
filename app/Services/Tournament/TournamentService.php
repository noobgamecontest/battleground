<?php

namespace App\Services\Tournament;

use App\Models\Team;
use App\Models\Match;
use App\Models\Tournament;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class TournamentService
{
    /**
     * Construit l'arbre et les matchs du tournoi.
     *
     * @param Tournament $tournament
     * @return void
     */
    public function buildTree(Tournament $tournament) : void
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
     * Construit un tournoi.
     *
     * @param string $name
     * @param array $teamsName
     * @param int $numberOfTeamsByMatch
     * @param int $numberOfWinnersByMatch
     * @return Tournament
     */
//    public function make(string $name, array $teamsName, int $numberOfTeamsByMatch, int $numberOfWinnersByMatch) :  Tournament
//    {
//        DB::beginTransaction();
//
//        $tournament = $this->makeTournament($name);
//
//        $teams = $this->makeTeamsFromName($tournament, $teamsName);
//
//        $matches = $this->buildTree(
//            $tournament,
//            count($teams),
//            $numberOfTeamsByMatch,
//            $numberOfWinnersByMatch
//        );
//
//        $firstRoundMatches = $matches->where(
//            'round',
//            $matches->pluck('round')->max()
//        );
//
//        $this->distribTeamsForRound(
//            $teams,
//            $firstRoundMatches,
//            $numberOfTeamsByMatch
//        );
//
//        foreach ($firstRoundMatches as $match) {
//            dump(
//                sprintf("Match %s, équipes : %s", $match->name,  $match->teams->pluck('name')->join(' vs '))
//            );
//        }
//
//        DB::commit();
//
//        return $tournament;
//    }

    /**
     * Retourne les tours d'un tournoi.
     *
     * @param Tournament $tournament
     * @return array
     */
    public function getRounds(Tournament $tournament) : array
    {
        return $tournament->matches->pluck('round')->unique()->values()->sort(function ($a, $b) {
            return $a < $b;
        })->toArray();
    }

    /**
     * Crée le tournoi.
     *
     * @param string $name
     * @return Tournament
     */
    protected function makeTournament(string $name) : Tournament
    {
        $tournament = new Tournament(['name' => $name]);
        $tournament->save();
        return $tournament;
    }

    /**
     * Crée les équipes depuis leur nom.
     *
     * @param Tournament $tournament
     * @param array $names
     * @return Collection
     */
    protected function makeTeamsFromName(Tournament $tournament, array $names) : Collection
    {
        $teams = new Collection();

        foreach ($names as $name) {
            $team = new Team(['name' => $name]);
            $tournament->teams()->save($team);
            $teams->push($team);
        }

        return $teams;
    }

    /**
     * Construit l'arbre et les matchs du tournoi.
     *
     * @param Tournament $tournament
     * @param int $numberOfPlayers
     * @param int $numberOfTeamsByMatch
     * @param int $numberOfWinnersByMatch
     * @return Collection
     */
//    protected function buildTree(Tournament $tournament, int $numberOfPlayers, int $numberOfTeamsByMatch, int $numberOfWinnersByMatch) : Collection
//    {
//        $matches = new Collection();
//
//        $numberOfRounds = $this->getNumberOfRounds(
//            $numberOfPlayers,
//            $numberOfTeamsByMatch,
//            $numberOfWinnersByMatch
//        );
//
//        for ($roundIndex = $numberOfRounds; $roundIndex >= 0; $roundIndex--) {
//            $numberOfSlotsInThisRound = $this->getNumberOfSlotsForThisRound(
//                $roundIndex,
//                $numberOfTeamsByMatch,
//                $numberOfWinnersByMatch
//            );
//
//            $numberOfMatchesInThisRound = $numberOfSlotsInThisRound / $numberOfTeamsByMatch;
//
//            for ($matchIndex = 0; $matchIndex < $numberOfMatchesInThisRound; $matchIndex++) {
//                $match = new Match([
//                    'round' => $roundIndex,
//                    'name' => "Match #$matchIndex for round #$roundIndex"
//                ]);
//                $tournament->matches()->save($match);
//                $matches->push($match);
//            }
//        }
//
//        return $matches;
//    }

    /**
     * Place les équipes dans les matchs du tour aléatoirement.
     *
     * @param Collection $teams
     * @param Collection $matches
     * @param int $numberOfTeamsByMatch
     * @return void
     */
    protected function distribTeamsForRound(Collection $teams, Collection $matches, int $numberOfTeamsByMatch) : void
    {
        $numberOfSlots = $matches->count() * $numberOfTeamsByMatch;

        $teams = $teams->pad($numberOfSlots, null);

        foreach ($matches as $match) {
            for ($j = 0; $j < $numberOfTeamsByMatch; $j++) {
                $index = rand(0, $teams->count() - 1);
                $team = $teams->pull($index);
                $match->teams()->attach($team);
                $teams = $teams->values();
            }
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
}
