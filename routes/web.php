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

Route::pattern('book', '[0-9]+');
Route::pattern('chapter', '[0-9]+');
Route::pattern('verse', '[0-9]+');

Route::get('/', function () {return view('welcome');});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/ksiega/{book}/rozdzial/{chapter}', 'ChaptersController@show');
    Route::get('/start', 'StartController@index')->name('start');
    Route::get('/test', 'TestController@index');
    /* JSONs */
    Route::get('/ksiega/{book}/rozdzials', 'ChaptersController@index');
    /* Redirects */
    Route::get('/home', function () { return redirect()->route('start'); } )->name('home');

    /*
     * @TODO The following controllers / methods need to be created
     * Route::get('/ksiega/{book}/rozdzial/{chapter}/wers/{verse}', 'VersesController@show'); // @TODO Created but unfinished
     * Route::get('/ksiega/{book}', 'BooksController@show');
     * Route::get('/ksiega', 'BooksController@index');
     * Probably unnecessary for now
     * Route::get('/ksiega/{book}/rozdzial/{chapter}/wersy', 'VersesController@index');
     * Route::get('/ksiega/{book}/rozdzial/{chapter}/wersy/{from}/do/{to}', 'VersesController@set');
     */
});

Auth::routes();