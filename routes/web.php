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
    $numberOfPlayers = 9;
    $players = range(1, $numberOfPlayers);
    $players = array_map(function ($player) {
        return "Player $player";
    }, $players);
    $numberOfPlayersByMatch = 2;
    $numberOfWinnersByMatch = 1;

    /**
     * Rumble
     */
    $matches = app(\App\Services\Tournament\Tournament::class)->build(
        $players,
        $numberOfPlayersByMatch,
        $numberOfWinnersByMatch
    );

    dd($matches);
});
