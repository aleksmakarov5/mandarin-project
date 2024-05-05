<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

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

Route::group(['namespace' => 'Notification', 'prefix' => 'notifications'], function () {
    Route::delete('/{id}', [NotificationController::class, 'delete']);
    // Route::get('/{id}', 'ShowController');
    Route::get('/{id}', [NotificationController::class, 'index']);
    // Route::post('/', 'StoreController');
    // Route::put('/{id}', 'UpdateController');
})->middleware('auth:web');
