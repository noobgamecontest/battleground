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

Auth::routes();

Route::namespace('Tournament')->group(function () {
    Route::get('/', 'TournamentsController@index')->name('tournaments.index');
    Route::get('/history', 'TournamentsController@history')->name('tournaments.history');
});

Route::middleware('admin')->namespace('Tournament')->group(function () {
    Route::resource('tournaments', 'TournamentsController')->except(['index', 'show']);
    Route::patch('tournaments/{tournament}/launch', 'TournamentsController@launch')->name('tournaments.launch');
    Route::patch('tournaments/{tournament}/unsubscribe/{team}', 'TournamentsController@unsubscribe')->name('tournaments.unsubscribe');
});

Route::middleware('auth')->namespace('Tournament')->group(function () {
    Route::resource('tournaments', 'TournamentsController')->only('show');
    Route::post('tournaments/{tournament}/subscribe', 'TournamentsController@subscribe')->name('tournaments.subscribe');
});
