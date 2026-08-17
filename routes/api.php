<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrokersController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\PropertyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('broker')->group(function () {
    Route::get('/brokers', [BrokersController::class, 'getAllBrokers']);
    Route::get('/properties', [PropertyController::class, 'getAllProperties']);
    Route::get('/{id}/broker', [BrokersController::class, 'getBrokerUsingBrokerId']);
});

Route::prefix('property')->group(function () {
    Route::get('/properties', [PropertyController::class, 'getAllProperties']);
    Route::get('/{id}/property', [PropertyController::class, 'getSingleProperty']);
});

Route::prefix('v1')->group(function () {
    Route::get('/health', fn () => response()->json([
        'status' => 'success',
        'data' => ['service' => 'hummingbed-property-api', 'timestamp' => now()->toISOString()],
    ]));

    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::get('/properties', [PropertyController::class, 'getAllProperties']);
    Route::get('/properties/{id}', [PropertyController::class, 'getSingleProperty']);
    Route::post('/properties/{property}/inquiries', [MarketplaceController::class, 'createInquiry']);
    Route::get('/amenities', [MarketplaceController::class, 'amenities']);

    Route::get('/brokers', [BrokersController::class, 'getAllBrokers']);
    Route::get('/brokers/{id}', [BrokersController::class, 'getBrokerUsingBrokerId']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::post('/brokers', [BrokersController::class, 'addBroker']);
        Route::match(['put', 'patch'], '/brokers/{id}', [BrokersController::class, 'updateBroker']);
        Route::delete('/brokers/{id}', [BrokersController::class, 'deleteBroker']);

        Route::post('/properties', [PropertyController::class, 'storeProperty']);
        Route::match(['put', 'patch'], '/properties/{id}', [PropertyController::class, 'updateProperty']);
        Route::delete('/properties/{id}', [PropertyController::class, 'deleteProperty']);
        Route::post('/properties/{property}/images', [MarketplaceController::class, 'addImage']);
        Route::delete('/properties/{property}/images/{image}', [MarketplaceController::class, 'deleteImage']);
        Route::put('/properties/{property}/amenities', [MarketplaceController::class, 'syncAmenities']);

        Route::get('/favorites', [MarketplaceController::class, 'favorites']);
        Route::post('/properties/{property}/favorite', [MarketplaceController::class, 'addFavorite']);
        Route::delete('/properties/{property}/favorite', [MarketplaceController::class, 'removeFavorite']);

        Route::get('/inquiries', [MarketplaceController::class, 'inquiries']);
        Route::patch('/inquiries/{inquiry}', [MarketplaceController::class, 'updateInquiry']);
        Route::get('/appointments', [MarketplaceController::class, 'appointments']);
        Route::post('/properties/{property}/appointments', [MarketplaceController::class, 'createAppointment']);
        Route::patch('/appointments/{appointment}/cancel', [MarketplaceController::class, 'cancelAppointment']);
        Route::get('/broker/appointments', [MarketplaceController::class, 'brokerAppointments']);
        Route::patch('/broker/appointments/{appointment}', [MarketplaceController::class, 'updateAppointment']);
        Route::post('/amenities', [MarketplaceController::class, 'createAmenity']);
    });
});
