<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RainfallController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\WaterLevelController;
use App\Http\Controllers\WireVibrationController;
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

Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard.index');
Route::get('/station',[StationController::class,'index'])->name('station.index');

Route::get('/rainfall/byStation',[RainfallController::class,'byStation'])->name('rainfall.byStation');
Route::get('/rainfall/daily',[RainfallController::class,'daily'])->name('rainfall.daily');

Route::get('/water_level/daily',[WaterLevelController::class,'daily'])->name('water_level.daily');

Route::get('/wire_vibration/daily',[WireVibrationController::class,'daily'])->name('wire_vibration.daily');
