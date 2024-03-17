<?php

use App\Http\Controllers\PropertyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrokersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('user')->group(function () {
    Route::get('/data', [UserController::class, 'getUsers']);

});

Route::prefix('broker')->group(function () {
    Route::get('/brokers', [BrokersController::class, 'getAllBrokers']);
    Route::get('/properties', [PropertyController::class, 'getAllProperties']);
    Route::post('/save-broker', [BrokersController::class, 'addBroker']);
    Route::get('/{id}/broker', [BrokersController::class, 'getBrokerUsingBrokerId']);
    Route::put('/{id}/update-broker', [BrokersController::class, 'updateBroker']);
    Route::delete('/{id}/delete-broker', [BrokersController::class,'deleteBroker']);
});

Route::prefix('property')->group(function () {
    Route::get('/properties', [PropertyController::class, 'getAllProperties']);
    Route::post('/save-property', [PropertyController::class, 'storeProperty']);
    Route::get('/{id}/property', [PropertyController::class, 'getSingleProperty']);
});
