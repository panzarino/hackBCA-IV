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
Route::get('/api/addsong', 'Music@addSong');
Route::get('/authenticate', 'Music@authenticateWithSpotify');
Route::get('/api/twilio', 'Music@twilio');
Route::get('/api/list', 'Music@getList');
Route::get('/api/upvote', 'Music@upvote');
Route::get('/api/downvote', 'Music@downvote');