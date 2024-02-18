<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImapController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\ReviewController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SparePartController;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('home');
// });

// Route::get('/about', function () {
//     return view('about');
// });


Route::get('/', [MainController::class, 'home'])->name('home');

Route::get('/about',  [MainController::class, 'about']);

Route::get('/review',  [MainController::class, 'review'])->name('review');
Route::get('/new_application',  [MainController::class, 'new_application'])->name('new_application');
Route::post('/review/check',  [MainController::class, 'review_check']);
Route::get('/mail_recieve',  [ImapController::class, 'recieve'])->name('mail_recive');






Route::middleware("can:administrator")->group(
    function () {
        Route::get('/admin',  [AdminController::class, 'index'])->name('admin');
        Route::get('/users',  [AdminController::class, 'users_index'])->name('users');
        Route::get('users/create', [AdminController::class, 'create_user'])
            ->name('users.create');
        Route::post('/user_register',  [AdminController::class, 'user_register'])->name('user_register');
        Route::get('/user/{id}', [AdminController::class, 'user_edit'])->name('user_edit');
        Route::post('/user/{id}/update', [AdminController::class, 'update_user'])->name('user_update');
        Route::get('user/{id}/delete', [AdminController::class, 'destroy_user'])
            ->name('user.destroy');

        Route::get('/usergroups',  [AdminController::class, 'usergroups_index'])->name('usergroups');
        Route::get('usergroups/create', [AdminController::class, 'create_usergroup'])
            ->name('usergroups.create');
        Route::post('/usergroup_register',  [AdminController::class, 'usergroup_register'])->name('usergroup_register');
        Route::get('/usergroup/{id}', [AdminController::class, 'usergroup_edit'])->name('usergroup_edit');
        Route::post('/usergroup/{id}/update', [AdminController::class, 'update_usergroup'])->name('usergroup_update');
        Route::get('usergroup/{id}/delete', [AdminController::class, 'destroy_usergroup'])
            ->name('usergroup.destroy');

        Route::get('/statuses',  [AdminController::class, 'statuses_index'])->name('statuses');
        Route::get('/inscompanies',  [AdminController::class, 'inscompanies_index'])->name('inscompanies');
    }
);

Route::middleware("guest:web")->group(
    function () {
        Route::get('/login',  [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login_process',  [AuthController::class, 'login'])->name('login_process');
    }
);


Route::middleware("auth:web")->group(
    function () {

        Route::get('/mail',  [MainController::class, 'message'])->name('mail');
        Route::get('/mail/{id}',  [ApplicationController::class, 'message'])->name('mail_app');
        Route::get('/logout',  [AuthController::class, 'logout'])->name('logout');
        Route::get('/applications',  [MainController::class, 'applications'])->name('applications');
        Route::get('/application/{id}', [MainController::class, 'application'])->name('application');
        Route::get('applications/{id}/edit', [ApplicationController::class, 'edit'])
            ->name('applications.edit');
        Route::get('applications/create', [ApplicationController::class, 'create'])
            ->name('applications.create');
        Route::post('applications/store', [ApplicationController::class, 'store'])
            ->name('applications.store');
        Route::post('applications/{id}/update', [ApplicationController::class, 'update'])->name('applications.update');
        Route::delete('applications/{id}/delete', [ApplicationController::class, 'destroy'])
            ->name('applications.destroy');

        Route::get('/SparePart/{id}', [SparePartController::class, 'SparePart'])->name('SparePart');
        Route::get('SparePart/{id}/edit', [SparePartController::class, 'edit'])
            ->name('SparePart.edit');
        Route::get('SparePart/{id}/create', [SparePartController::class, 'create'])
            ->name('SparePart.create');
        Route::post('SpareParts/store', [SparePartController::class, 'store'])
            ->name('SparePart.store');
        Route::post('SparePart/{id}/update', [SparePartController::class, 'update'])->name('SparePart.update');
        Route::delete('SparePart/{id}/delete', [SparePartController::class, 'destroy'])
            ->name('SparePart.destroy');

        Route::get('/Work/{id}', [WorkController::class, 'Work'])->name('Work');
        Route::get('Work/{id}/edit', [WorkController::class, 'edit'])
            ->name('Work.edit');
        Route::get('Work/{id}/create', [WorkController::class, 'create'])
            ->name('Work.create');
        Route::post('Works/store', [WorkController::class, 'store'])
            ->name('Work.store');
        Route::post('Work/{id}/update', [WorkController::class, 'update'])->name('Work.update');
        Route::delete('Work/{id}/delete', [WorkController::class, 'destroy'])
            ->name('Work.destroy');
        Route::get('/document/{id}', [MainController::class, 'document'])->name('document');
        Route::get('/showdoc/{id}', [MainController::class, 'showdoc'])->name('showdoc');
    }

);
