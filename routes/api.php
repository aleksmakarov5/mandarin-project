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
use App\Http\Controllers\api\ChatController;
use App\Http\Controllers\api\ApplicationController;




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
        ->name('api.troubleshooting.index');
    Route::post('/create', [TroubleshootingController::class, 'store'])
        ->name('api.troubleshooting.create');
    Route::post('/{id}/update', [
        TroubleshootingController::class,
        'update'
    ])->name('api.troubleshooting.update');
    Route::delete('/{id}', [TroubleshootingController::class, 'destroy']);
    // Route::post('/', 'StoreController');
    // Route::put('/{id}', 'UpdateController');
});
Route::group(['namespace' => 'Application', 'prefix' => 'application'], function () {
    // Route::delete('/{id}', [NotificationController::class, 'delete']);
    // Route::get('/{id}', 'ShowController');
    Route::delete('/{id}', [ApplicationController::class, 'destroy']);
    Route::post('/add_filter', [ApplicationController::class, 'add_filter']);
    Route::post('/delete_filter', [ApplicationController::class, 'delete_filter']);
    Route::post('/add_filters', [ApplicationController::class, 'add_filters']);
    Route::post('/delete_filters', [ApplicationController::class, 'delete_filters']);

    // Route::post('/', 'StoreController');
    // Route::put('/{id}', 'UpdateController');
});
Route::group(['namespace' => 'Date', 'prefix' => 'date'], function () {
    // Route::delete('/{id}', [NotificationController::class, 'delete']);
    // Route::get('/{id}', 'ShowController');
    Route::post('/{id}/update', [
        TroubleshootingController::class,
        'update_date'
    ])->name('date.update');
});
Route::group(['namespace' => 'Chat', 'prefix' => 'chat'], function () {

    Route::post('/send', [ChatController::class, 'chat_send'])->name('chat.send');
    Route::post('/readed', [ChatController::class, 'readed'])->name('chat.readed');
    Route::post('/add_work_group', [ChatController::class, 'add_work_group'])->name('chat.add_work_group');
    Route::post('/add_work_groups', [ChatController::class, 'add_work_groups'])->name('chat.add_work_groups');
    Route::post('/delete_work_group', [ChatController::class, 'delete_work_group'])->name('chat.delete_work_group');
    Route::post('/delete_work_groups', [ChatController::class, 'delete_work_groups'])->name('chat.delete_work_groups');
    Route::post('/unreaded', [ChatController::class, 'unreaded'])->name('chat.unreaded');
    Route::delete('/{id}', [ChatController::class, 'destroy']);
    // Route::post('/', 'StoreController');
    // Route::put('/{id}', 'UpdateController');
});

Route::group(['namespace' => 'Estimate', 'prefix' => 'estimate'], function () {
    // Route::delete('/{id}', [NotificationController::class, 'delete']);
    // Route::get('/{id}', 'ShowController');
    Route::get('/{id}', [EstimateController::class, 'show'])
        ->name('estimate.index');
    Route::post('/create', [EstimateController::class, 'store'])
        ->name('estimate.create');
    Route::post('/{id}/update', [
        EstimateController::class,
        'update'
    ])->name('estimate.update');
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
        ->name('api.work.index');
    Route::post('/create', [WorkController::class, 'store'])
        ->name('api.work.create');
    Route::post('/{id}/update', [
        WorkController::class,
        'update'
    ])->name('api.work.update');
    Route::delete('/{id}', [WorkController::class, 'destroy']);
    Route::post('/create_paint', [WorkController::class, 'store_paint'])
        ->name('api.work.create_paint');

    Route::post('/{id}/update_paint', [
        WorkController::class,
        'update_paint'
    ])->name('api.work.update_paint');
    Route::delete('/{id}/paint', [WorkController::class, 'destroy_paint']);
    // Route::post('/', 'StoreController');
    // Route::put('/{id}', 'UpdateController');
});

Route::group(['namespace' => 'SparePart', 'prefix' => 'sparepart'], function () {
    // Route::delete('/{id}', [NotificationController::class, 'delete']);
    // Route::get('/{id}', 'ShowController');
    Route::get('/{id}', [SparePartController::class, 'show'])
        ->name('api.sparepart.index');
    Route::post('/create', [SparePartController::class, 'store'])
        ->name('api.sparepart.create');
    Route::post('/{id}/update', [
        SparePartController::class,
        'update'
    ])->name('api.sparepart.update');
    Route::delete('/{id}', [SparePartController::class, 'destroy']);
    // Route::post('/', 'StoreController');
    // Route::put('/{id}', 'UpdateController');
});

Route::group(['namespace' => 'NotifyCount', 'prefix' => 'notifycount'], function () {

    Route::get('/applications/{user_id}', [NotifyCountController::class, 'applications'])
        ->name('api.notifycount.applications');
    Route::get('/directions/{user_id}', [NotifyCountController::class, 'directions'])
        ->name('api.notifycount.directions');
    Route::get('/waiting/{user_id}', [NotifyCountController::class, 'waiting'])
        ->name('api.notifycount.waiting');
    Route::get('/failures/{user_id}', [NotifyCountController::class, 'failures'])
        ->name('api.notifycount.failures');
    Route::get('/lawyers/{user_id}', [NotifyCountController::class, 'lawyers'])
        ->name('api.notifycount.lawyers');
    Route::get('/archive/{user_id}', [NotifyCountController::class, 'archive'])
        ->name('api.notifycount.archive');
    Route::get('/temp/{user_id}', [NotifyCountController::class, 'temp'])
        ->name('api.notifycount.temp');
    Route::get('/application/{id}/{user_id}', [NotifyCountController::class, 'application'])
        ->name('api.notifycount.application');
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
