<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::post('login','Auth\AuthController@login');
Route::post('register','Auth\AuthController@register');


Route::resource('user', 'UserController', ['except' => ['create']]);

Route::resource('quiz', 'QuizController');