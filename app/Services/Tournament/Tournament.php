<?php

namespace App\Services\Tournament;

class Tournament
{
    /**
     * Construit un tournoi.
     * 
     * @param array $players
     * @param int $numberOfPlayersByMatch
     * @param int $numberOfWinnersByMatch
     * @return array
     */
    public function build(array $players, int $numberOfPlayersByMatch, int $numberOfWinnersByMatch) : array
    {
        $matches = [];
        
        $numberOfRounds = $this->getNumberOfRounds(
            count($players),
            $numberOfPlayersByMatch,
            $numberOfWinnersByMatch
        );

        for ($roundIndex = $numberOfRounds; $roundIndex >= 0; $roundIndex--) {
            $matches[$roundIndex] = [];

            $numberOfSlotsInThisRound = $this->getNumberOfSlotsForThisRound(
                $roundIndex,
                $numberOfPlayersByMatch,
                $numberOfWinnersByMatch
            );

            $numberOfMatchesInThisRound = $numberOfSlotsInThisRound / $numberOfPlayersByMatch;

            // TODO : prendre en considération les gagnants pour les tours suivants
            $playersAvailable = array_pad($players, $numberOfSlotsInThisRound, null);

            for ($matchIndex = 0; $matchIndex < $numberOfMatchesInThisRound; $matchIndex++) {
                $match = [];
                for ($j = 0; $j < $numberOfPlayersByMatch; $j++) {
                    $index = rand(0, count($playersAvailable) - 1);
                    $match[] = $playersAvailable[$index];
                    unset($playersAvailable[$index]);
                    $playersAvailable = array_values($playersAvailable);
                }

                $matches[$roundIndex][] = $match;
            }
        }

        return $matches;
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
