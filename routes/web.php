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

Route::post('/', 'LineBotController');

Route::post('/sendText', 'LineBotController@sendText');
Route::get('/reply_action/{userId}', 'BindStudentController@certification');

Route::post('/bind', 'BindStudentController@bind')->name('bind');

