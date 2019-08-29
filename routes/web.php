<?php
auth()->loginUsingId(1);
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

Route::get('/', 'TournamentsController@index')->name('tournaments.index');
Route::get('/history', 'TournamentsController@history')->name('tournaments.history');

Route::middleware('admin')->group(function () {
    Route::resource('tournaments', 'TournamentsController')->except(['index', 'show']);
    Route::patch('tournaments/{tournament}/launch', 'TournamentsController@launch')->name('tournaments.launch');
    Route::get('tournaments/{tournament}/results', 'ResultsController@getResults')->name('results.form');
    Route::post('matches/{match}', 'ResultsController@setResults')->name('results.post');
});

Route::middleware('auth')->group(function () {
   Route::resource('tournaments', 'TournamentsController')->only('show');
});
