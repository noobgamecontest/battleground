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

Route::get('/', 'TournamentsController@index')->name('tournaments.index');
Route::get('/history', 'TournamentsController@history')->name('tournaments.history');

Route::middleware('admin')->group(function () {
    Route::resource('tournaments', 'TournamentsController')->except(['index', 'show']);
});

Route::middleware('auth')->group(function () {
   Route::resource('tournaments', 'TournamentsController')->only('show');
});

