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
Route::group(['middleware' => 'log'], function () {
    Route::get('', 'UserController@getIndex');

    Route::post('auth_token', 'Auth\AuthController@login');

    Route::resource('user', 'UserController', ['except' => ['create', 'edit']]);

    Route::get('user/{id}/quizzes', 'QuizController@userQuizzes');
    Route::resource('quiz', 'QuizController', ['except' => ['create', 'edit']]);
});