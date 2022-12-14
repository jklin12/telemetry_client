<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\FlowController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RainfallController;
use App\Http\Controllers\StationController;
use App\Http\Controllers\UsersController;
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
    return view('pages/login');
});
Route::get('login', function () {
    return view('pages/login');
});
Route::post('login', [LoginController::class,'authenticate'])->name('login');
Route::get('logout', [LoginController::class,'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/portal', [DashboardController::class, 'monitoring'])->name('dashboard.portal');
    Route::get('/dashboard/portalData', [DashboardController::class, 'portalData'])->name('dashboard.portal_data');
    Route::get('/dashboard/alertData', [DashboardController::class, 'alertData'])->name('dashboard.alertData');
    Route::get('/station', [StationController::class, 'index'])->name('station.index');
    Route::get('/station/form/{station_id}', [StationController::class, 'form'])->name('station.form');
    Route::get('/station/formType/{station_id}/', [StationController::class, 'formType'])->name('station.addType');
    Route::get('/station/formType/{station_id}/{type_id}', [StationController::class, 'formType'])->name('station.formType');
    Route::get('/station/form/', [StationController::class, 'form'])->name('station.add');
    Route::post('/station/store/', [StationController::class, 'store'])->name('station.store');
    Route::post('/station/find/', [StationController::class, 'find'])->name('station.find');
    Route::post('/stationType/store/', [StationController::class, 'storeType'])->name('stationType.store');

    Route::get('/rainfall/byStation', [RainfallController::class, 'byStation'])->name('rainfall.byStation');
    Route::get('/rainfall/daily', [RainfallController::class, 'daily'])->name('rainfall.daily');
    Route::get('/rainfall/current', [RainfallController::class, 'current'])->name('rainfall.current');

    Route::get('/water_level/daily', [WaterLevelController::class, 'daily'])->name('water_level.daily');

    Route::get('/wire_vibration/daily', [WireVibrationController::class, 'daily'])->name('wire_vibration.daily');

    Route::get('/flow/daily', [FlowController::class, 'daily'])->name('flow.daily');

    Route::get('/download/index', [DownloadController::class, 'index'])->name('download.index');
    Route::post('/download/store', [DownloadController::class, 'store'])->name('download.store');

    Route::get('/grafik/judment', [GrafikController::class, 'judment'])->name('grafik.judment');
    Route::get('/grafik/hydrograph', [GrafikController::class, 'hydrograph'])->name('grafik.hydrograph');
    Route::get('/grafik/hytrograph', [GrafikController::class, 'hytrograph'])->name('grafik.hytrograph');
    Route::resource('users', UsersController::class);
});
