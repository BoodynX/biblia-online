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
    Route::post('/ksiega/{book}/rozdzial/{chapter}', 'ChaptersController@showNext');
    Route::post('/koniec', 'ChaptersController@showEnd');
    Route::get('/start', 'StartController@index')->name('start');

    /* Redirects */
    Route::get('/home', function () { return redirect()->route('start'); } )->name('home');

    /* DEV ONLY */
    Route::get('/test', 'TestController@index');
});

Auth::routes();