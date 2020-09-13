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

Route::group([
    'middleware' => 'api',
    'prefix' => 'user',
], function ($router) {
    Route::post('login', 'Api\UserController@login')->name('user.login');
    Route::post('logout', 'Api\UserController@logout')->name('user.logout');
    Route::post('refresh', 'Api\UserController@refresh')->name('user.refresh');
});

Route::apiResource('memos', 'Api\MemoController');
