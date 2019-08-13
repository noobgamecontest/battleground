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

use App\Models\Team;

Auth::routes();

Route::get('/', 'TournamentsController@index')->name('tournaments.index');
Route::get('/history', 'TournamentsController@history')->name('tournaments.history');

Route::middleware('admin')->group(function () {
    Route::resource('tournaments', 'TournamentsController')->except(['index', 'show']);
});

Route::middleware('auth')->group(function () {
   Route::resource('tournaments', 'TournamentsController')->only('show');
});

Route::get('rumble', function () {

    \DB::beginTransaction();

    $tournament = new \App\Models\Tournament([
        'slots' => 15,
        'opponents_by_match' => 2,
        'winners_by_match' => 1,
    ]);

    $tournament->save();

    $teams = new \Illuminate\Support\Collection(range(1, $tournament->slots));
    $teams->map(function ($player) {
        return new \App\Models\Team(['name' => "Team $player"]);
    })->each(function ($team) use ($tournament) {
        $tournament->teams()->save($team);
    });

    app(\App\Services\Tournament\TournamentService::class)->builTree($tournament);

});
