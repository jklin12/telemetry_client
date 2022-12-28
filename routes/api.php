<?php

use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\LoginController;
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

Route::controller(LoginController::class)->group(function(){
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('stationList',[DataController::class,'stationList']);
    Route::get('curentRainFall',[DataController::class,'curentRainFall']);
    Route::get('rainfallByStation',[DataController::class,'rainfallByStation']);
    Route::get('dailyRainFall',[DataController::class,'dailyRainFall']);
    Route::get('waterLevel',[DataController::class,'waterLevel']);
    Route::get('waterLevel/{station_id}',[DataController::class,'waterLevel']);
    Route::get('flow',[DataController::class,'flow']);
    Route::get('flow/{station_id}',[DataController::class,'flow']);
    Route::get('wireVibration',[DataController::class,'wireVibration']);
});