<?php

use App\Http\Controllers\Wn8Controller;
use App\Http\Controllers\WoTAPIHandler;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request;
});

Route::middleware('auth:api')->get('/wn8', [WoTAPIHandler::class, 'openIDLogin']);
//Route::get('/test', [WoTAPIHandler::class, 'openIDLogin']);
