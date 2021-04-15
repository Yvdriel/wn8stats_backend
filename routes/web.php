<?php

use App\Http\Controllers\WoTAPIHandler;
use App\Http\Resources\Wn8Collection;
use Illuminate\Support\Facades\Route;

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
Route::get('/openIDLogin', [WoTAPIHandler::class, 'openIDLogin']);
Route::get('/account', [WoTAPIHandler::class, 'getPlayerPersonalData']);
