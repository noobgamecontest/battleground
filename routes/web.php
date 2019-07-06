<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('matchup', function () {
    /**
     * Configuration
     */
    $numberOfPlayersInTournament = 9;
    $numberOfPlayerByMatch = 2;
    $numberOfWinnerByMatch = 1;

    /**
     * Calcul du nombre de tour nécessaire
     * 0 étant la finale
     * 1 les demis
     * 2 les quarts 
     * ...
     */
    $numberOfRounds = ceil(
        log($numberOfPlayersInTournament / $numberOfPlayerByMatch) / log($numberOfPlayerByMatch / $numberOfWinnerByMatch)
    );

    /**
     * Création des joueurs pour le tournoi
     */
    $players = range(1, $numberOfPlayersInTournament);
    $players = array_map(function ($player) {
        return "Player $player";
    }, $players);
    
    $playersAvailable = $players;

    /**
     * Construction de l'arbre
     */
    $matches = [];

    for ($roundNumber = $numberOfRounds; $roundNumber >= 0; $roundNumber--) {
        $roundName = "Round $roundNumber";
        $matches[$roundName] = [];

        $numberOfPlayersInThisRound = pow($numberOfPlayerByMatch/$numberOfWinnerByMatch, $roundNumber) * $numberOfPlayerByMatch;
        $numberOfMatchesInThisRound = $numberOfPlayersInThisRound / $numberOfPlayerByMatch;

        /**
         *  A corriger car cette liste ne prends pas en compte les gagnants pour les tours suivants
         */
        $playersAvailable = array_pad($players, $numberOfPlayersInThisRound, null);

        /**
         * On programme les matches pour le tour en cours
         */
        for ($matchIndex = 0; $matchIndex < $numberOfMatchesInThisRound; $matchIndex++) {
            $match = [];
            for ($j = 0; $j < $numberOfPlayerByMatch; $j++) {
                $index = rand(0, count($playersAvailable)-1);
                $match[] = $playersAvailable[$index];
                unset($playersAvailable[$index]);
                $playersAvailable = array_values($playersAvailable);
            }

            $matches[$roundName][] = $match;
        }
    }

    dd($matches);
});
