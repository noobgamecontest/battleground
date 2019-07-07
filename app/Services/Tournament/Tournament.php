<?php

namespace App\Services\Tournament;

class Tournament
{
    /**
     * Construit un tournoi.
     * 
     * @param int $numberOfPlayers
     * @param int $numberOfPlayersByMatch
     * @param int $numberOfWinnersByMatch
     * @return array
     */
    public function buildTree(int $numberOfPlayers, int $numberOfPlayersByMatch, int $numberOfWinnersByMatch) : array
    {
        $tree = [];
        
        $numberOfRounds = $this->getNumberOfRounds(
            $numberOfPlayers,
            $numberOfPlayersByMatch,
            $numberOfWinnersByMatch
        );

        for ($roundIndex = $numberOfRounds; $roundIndex >= 0; $roundIndex--) {
            $tree[$roundIndex] = [];

            $numberOfSlotsInThisRound = $this->getNumberOfSlotsForThisRound(
                $roundIndex,
                $numberOfPlayersByMatch,
                $numberOfWinnersByMatch
            );

            $numberOfMatchesInThisRound = $numberOfSlotsInThisRound / $numberOfPlayersByMatch;

            for ($matchIndex = 0; $matchIndex < $numberOfMatchesInThisRound; $matchIndex++) {
                $match = [
                    'slots' => array_fill(0, $numberOfPlayersByMatch, null),
                ];

                $tree[$roundIndex][] = $match;
            }
        }

        sort($tree);

        return $tree;
    }

    /**
     * Place les joueurs dans les matchs aléatoirement.
     *
     * @param array $players
     * @param int $roundIndex
     * @param int $numberOfPlayersByMatch
     * @param int $numberOfWinnersByMatch
     * @return array
     */
    public function distribPlayersForRound(array $players, int $roundIndex, int $numberOfPlayersByMatch, int $numberOfWinnersByMatch) : array
    {
        $round = [];

        $numberOfSlotsInThisRound = $this->getNumberOfSlotsForThisRound(
            $roundIndex,
            $numberOfPlayersByMatch,
            $numberOfWinnersByMatch
        );

        $numberOfMatchesInThisRound = $numberOfSlotsInThisRound / $numberOfPlayersByMatch;

        $players = array_pad($players, $numberOfSlotsInThisRound, null);

        for ($matchIndex = 0; $matchIndex < $numberOfMatchesInThisRound; $matchIndex++) {
            $match = [];

            for ($j = 0; $j < $numberOfPlayersByMatch; $j++) {
                $index = rand(0, count($players) - 1);
                $match[] = $players[$index];
                unset($players[$index]);
                $players = array_values($players);
            }

            $round[] = $match;
        }

        return $round;
    }

    /**
     * Retourne le nombre de tours en fonction du nombre de joueur dans le tournoi, par match et de gagnant par match.
     * Sachant qu'on a 0 pour 1 tour, 1 pour 2 tours, etc ...
     *
     * @param int $numberOfPlayers
     * @param int $numberOfPlayersByMatch
     * @param int $numberOfWinnersByMatch
     * @return int
     */
    protected function getNumberOfRounds(int $numberOfPlayers, int $numberOfPlayersByMatch, int $numberOfWinnersByMatch) : int
    {
        return ceil(
            log($numberOfPlayers / $numberOfPlayersByMatch) / log($numberOfPlayersByMatch / $numberOfWinnersByMatch)
        );
    }

    /**
     * Retourne le nombre de place disponible pour le tour donné.
     *
     * @param int $roundIndex
     * @param int $numberOfPlayersByMatch
     * @param int $numberOfWinnersByMatch
     * @return int
     */
    protected function getNumberOfSlotsForThisRound(int $roundIndex, int $numberOfPlayersByMatch, int $numberOfWinnersByMatch) : int
    {
        return pow($numberOfPlayersByMatch / $numberOfWinnersByMatch, $roundIndex) * $numberOfPlayersByMatch;
    }    
}
