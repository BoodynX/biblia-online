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
    Route::post('/last', 'ChaptersController@showEnd');
    Route::get('/nastepny_krok', 'ChaptersController@findAndShowNextStep');
    Route::get('/start', 'StartController@index')->name('start');

    /* AJAX */
    Route::post('/chapter/send_question', 'ChaptersController@storeQuestion');
    Route::post('/verse/store_fav', 'VersesController@storeFav');

    /* Redirects */
    Route::get('/home', function () { return redirect()->route('start'); } )->name('home');

    /* DEV ONLY */
    Route::get('/test', 'TestController@index');
});

Auth::routes();