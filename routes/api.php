<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {

    $api->get('/hello', function(){
        return 'Hello DingoApi';
    });

    $api->post('/user/signup','\App\Http\Controllers\UserController@store');
    $api->post('/user/login','\App\Http\Controllers\Auth\AuthController@login');

    $api->group(['middleware' => 'api', 'prefix' => 'auth'], function($api) {
        $api->post('/token/refresh', 'App\Http\Controllers\Auth\AuthController@refresh');
        $api->post('/logout', 'App\Http\Controllers\Auth\AuthController@logout');
        $api->post('/me', 'App\Http\Controllers\Auth\AuthController@me');
    });
});
