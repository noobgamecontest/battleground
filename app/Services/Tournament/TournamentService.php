<?php

namespace App\Services\Tournament;

use App\Models\Match;
use App\Models\Tournament;

class TournamentService
{
    /**
     * Démarre un Tournoi
     *
     * @param Tournament $tournament
     */
    public function launch(Tournament $tournament) : void
    {
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
}
