<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\api\TroubleshootingController;
use App\Http\Controllers\api\EstimateController;
use App\Http\Controllers\NotifyCountController;
use App\Http\Controllers\api\FactWorkController;
use App\Http\Controllers\api\WorkController;
use App\Http\Controllers\api\SparePartController;



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
    Route::get('/{id}', [TroubleshootingController::class, 'show'])
        ->name('troubleshooting.index');
    Route::post('/create', [TroubleshootingController::class, 'store'])
        ->name('troubleshooting.create');
    Route::post('/{id}/update', [
        TroubleshootingController::class,
        'update'
    ])->name('troubleshooting.update');
    Route::delete('/{id}', [TroubleshootingController::class, 'destroy']);
    // Route::post('/', 'StoreController');
    // Route::put('/{id}', 'UpdateController');
});
Route::group(['namespace' => 'Estimate', 'prefix' => 'estimate'], function () {
    // Route::delete('/{id}', [NotificationController::class, 'delete']);
    // Route::get('/{id}', 'ShowController');
    Route::get('/{id}', [EstimateController::class, 'show'])
        ->name('troubleshooting.index');
    Route::post('/create', [EstimateController::class, 'store'])
        ->name('troubleshooting.create');
    Route::post('/{id}/update', [
        EstimateController::class,
        'update'
    ])->name('troubleshooting.update');
    Route::delete('/{id}', [EstimateController::class, 'destroy']);
    // Route::post('/', 'StoreController');
    // Route::put('/{id}', 'UpdateController');
});
Route::group(['namespace' => 'FactWork', 'prefix' => 'factwork'], function () {
    // Route::delete('/{id}', [NotificationController::class, 'delete']);
    // Route::get('/{id}', 'ShowController');
    Route::get('/{id}', [FactWorkController::class, 'show'])
        ->name('work.index');
    Route::post('/create', [FactWorkController::class, 'store'])
        ->name('work.create');
    Route::post('/{id}/update', [
        FactWorkController::class,
        'update'
    ])->name('work.update');
    Route::delete('/{id}', [FactWorkController::class, 'destroy']);
    // Route::post('/', 'StoreController');
    // Route::put('/{id}', 'UpdateController');
});

Route::group(['namespace' => 'Work', 'prefix' => 'work'], function () {
    // Route::delete('/{id}', [NotificationController::class, 'delete']);
    // Route::get('/{id}', 'ShowController');
    Route::get('/{id}', [WorkController::class, 'show'])
        ->name('work.index');
    Route::post('/create', [WorkController::class, 'store'])
        ->name('work.create');
    Route::post('/{id}/update', [
        WorkController::class,
        'update'
    ])->name('work.update');
    Route::delete('/{id}', [WorkController::class, 'destroy']);
    // Route::post('/', 'StoreController');
    // Route::put('/{id}', 'UpdateController');
});

Route::group(['namespace' => 'SparePart', 'prefix' => 'sparepart'], function () {
    // Route::delete('/{id}', [NotificationController::class, 'delete']);
    // Route::get('/{id}', 'ShowController');
    Route::get('/{id}', [SparePartController::class, 'show'])
        ->name('sparepart.index');
    Route::post('/create', [SparePartController::class, 'store'])
        ->name('sparepart.create');
    Route::post('/{id}/update', [
        SparePartController::class,
        'update'
    ])->name('sparepart.update');
    Route::delete('/{id}', [SparePartController::class, 'destroy']);
    // Route::post('/', 'StoreController');
    // Route::put('/{id}', 'UpdateController');
});

Route::group(['namespace' => 'NotifyCount', 'prefix' => 'notifycount'], function () {

    Route::get('/applications/{user_id}', [NotifyCountController::class, 'applications'])
        ->name('notifycount.applications');
    Route::get('/directions/{user_id}', [NotifyCountController::class, 'directions'])
        ->name('notifycount.directions');
    Route::get('/waiting/{user_id}', [NotifyCountController::class, 'waiting'])
        ->name('notifycount.waiting');
    Route::get('/failures/{user_id}', [NotifyCountController::class, 'failures'])
        ->name('notifycount.failures');
    Route::get('/lawyers/{user_id}', [NotifyCountController::class, 'lawyers'])
        ->name('notifycount.lawyers');
    Route::get('/archive/{user_id}', [NotifyCountController::class, 'archive'])
        ->name('notifycount.archive');
    Route::get('/application/{id}/{user_id}', [NotifyCountController::class, 'application'])
        ->name('notifycount.application');
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