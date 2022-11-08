<?php
 
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
    Route::get('stationList',[App\Http\Controllers\API\DataController::class,'stationList']);
    Route::get('curentRainFall',[App\Http\Controllers\API\DataController::class,'curentRainFall']);
    Route::get('rainfallByStation',[App\Http\Controllers\API\DataController::class,'rainfallByStation']);
    Route::get('dailyRainFall',[App\Http\Controllers\API\DataController::class,'dailyRainFall']);
});