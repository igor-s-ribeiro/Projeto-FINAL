<?php
use Illuminate\Support\Facades\Route;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


Route::group(['prefix' => '/api'], function () {
    Route::post('/users/login', 'UsersController@emailAndPassword');
    Route::post('/users/create', 'UsersController@create');
    Route::post('/pets/create', 'PetsController@petsCreate');
    Route::post('/pets/consultaPets', 'PetsController@consultaPets');
});
