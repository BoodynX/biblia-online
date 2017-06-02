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
Route::get('/book/{book}/chapter/{chapter}/verse/{verse}', 'VersesController@show');
Route::get('/book/{book}/chapter/{chapter}', 'ChaptersController@show');


// @TODO The following controllers / methods need to be created

Route::get('/book/{book}/chapters', 'ChaptersController@index');
//Route::get('/book/{book}', 'BooksController@show');
//Route::get('/books', 'BooksController@index');

// Probably unnecessary for now
// Route::get('/book/{book}/chapter/{chapter}/verses', 'VersesController@index');
// Route::get('/book/{book}/chapter/{chapter}/verses/{from}/to/{to}', 'VersesController@set');
Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/test', 'TestController@index');