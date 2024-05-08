<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TroubleshootingController;

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
});

Route::group(['namespace' => 'Troubleshooting', 'prefix' => 'troubleshootings'], function () {
    // Route::delete('/{id}', [NotificationController::class, 'delete']);
    // Route::get('/{id}', 'ShowController');
    Route::get('/{id}', [TroubleshootingController::class, 'create'])
        ->name('troubleshooting.index');
    Route::post('/{id}', [TroubleshootingController::class, 'create'])
        ->name('troubleshooting.index');
    Route::post('/{id}/update', [
        TroubleshootingController::class, 'update'
    ])->name('troubleshooting.update');
    // Route::post('/', 'StoreController');
    // Route::put('/{id}', 'UpdateController');
});
  


// Route::middleware('auth:web')->group(
//     function () {
//         Route::prefix('/notifications')->group(
//             function () {
//                 Route::delete('/{id}', [NotificationController::class, 'delete']);
//                 // Route::get('/{id}', 'ShowController');
//                 Route::get('/{id}', [NotificationController::class, 'index']);
//                 // Route::post('/', 'StoreController');
//                 // Route::put('/{id}', 'UpdateController');
//             }
//         );
//     }
// );