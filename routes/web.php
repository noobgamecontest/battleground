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

Route::get('rumble', function () {
    /**
     * Configuration
     */
    $tournamentName = 'NGC #9';
    $numberOfPlayers = 15;
    $players = range(1, $numberOfPlayers);
    $players = array_map(function ($player) {
        return "Player $player";
    }, $players);
    $numberOfPlayersByMatch = 2;
    $numberOfWinnersByMatch = 1;

    $tournament = app(\App\Services\Tournament\TournamentService::class)->make(
        $tournamentName,
        $players,
        $numberOfPlayersByMatch,
        $numberOfWinnersByMatch
    );
});
