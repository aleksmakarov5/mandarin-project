<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImapController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\TroubleshootingController;
use App\Http\Controllers\WordController;

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

        Route::get('/application_statuses',  [AdminController::class, 'application_statuses_index'])->name('application_statuses');
        Route::get('application_statuses/create', [AdminController::class, 'create_application_status'])
            ->name('application_statuses.create');
        Route::post('/application_status_register',  [AdminController::class, 'application_status_register'])->name('application_status_register');
        Route::get('/application_status/{id}', [AdminController::class, 'application_status_edit'])->name('application_status_edit');
        Route::post('/application_status/{id}/update', [AdminController::class, 'update_application_status'])->name('application_status_update');
        Route::get('application_status/{id}/delete', [AdminController::class, 'destroy_application_status'])
            ->name('application_status.destroy');

        Route::get('/insurance_companies',  [AdminController::class, 'insurance_companies_index'])->name('insurance_companies');
        Route::get('insurance_companies/create', [AdminController::class, 'create_insurance_company'])
            ->name('insurance_companies.create');
        Route::post('/insurance_company_register',  [AdminController::class, 'insurance_company_register'])->name('insurance_company_register');
        Route::get('/insurance_company/{id}', [AdminController::class, 'insurance_company_edit'])->name('insurance_company_edit');
        Route::post('/insurance_company/{id}/update', [AdminController::class, 'update_insurance_company'])->name('insurance_company_update');
        Route::get('insurance_company/{id}/delete', [AdminController::class, 'destroy_insurance_company'])
            ->name('insurance_company.destroy');

        Route::get('/fail_var',  [AdminController::class, 'fail_var'])->name('fail_var');
        Route::post('/fail_var_register',  [AdminController::class, 'fail_var_register'])->name('fail_var_register');
        Route::post('/fail_var/{id}', [AdminController::class, 'fail_var_edit'])->name('fail_var_edit');
        Route::get('fail_var/{id}/delete', [AdminController::class, 'destroy_fail_var'])
            ->name('fail_var.destroy');
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
        Route::post('/test',  [MainController::class, 'test'])->name('test');

        Route::get('/answer_fail_sel/{id}', [ApplicationController::class, 'answer_fail_sel'])->name('answer_fail_sel');
        Route::post('/answer_fail_save/{id}', [ApplicationController::class, 'answer_fail_save'])->name('answer_fail_save');
        Route::get('/mail/{id}',  [ApplicationController::class, 'message'])->name('mail_app');
        Route::get('/chat/{id}',  [ApplicationController::class, 'chat'])->name('chat');
        Route::post('/chat_sent/{id}',  [ApplicationController::class, 'chat_sent'])->name('chat_sent');

        Route::get('/answer_ic/{id}',  [ApplicationController::class, 'answer_ic'])->name('answer_ic');
        Route::post('/answer_ic_sent/{id}',  [ApplicationController::class, 'answer_ic_sent'])->name('answer_ic_sent');
        Route::get('/history/{id}',  [ApplicationController::class, 'history'])->name('history');
        Route::get('/logout',  [AuthController::class, 'logout'])->name('logout');
        Route::get('/lawyers',  [ApplicationController::class, 'lawyers'])->name('lawyers');
        Route::get('/archive',  [ApplicationController::class, 'archive'])->name('archive');
        Route::get('/failures',  [ApplicationController::class, 'failures'])->name('failures');
        Route::get('/applications',  [ApplicationController::class, 'applications'])->name('applications');
        Route::get('/directions',  [ApplicationController::class, 'directions'])->name('directions');
        Route::get('/application/{id}', [ApplicationController::class, 'application'])->name('application');
        Route::get('applications/{id}/edit', [ApplicationController::class, 'edit'])
            ->name('applications.edit');
        Route::get('applications/{id}/send_mrk/{id_MRK}', [ApplicationController::class, 'send_mrk'])
            ->name('applications.send_mrk');
        Route::post('applications/{id}/change_status', [ApplicationController::class, 'change_status'])
            ->name('applications.change_status');
        Route::post('applications/{id}/change_status_km', [ApplicationController::class, 'change_status_km'])
            ->name('applications.change_status_km');

        Route::get('applications/create', [ApplicationController::class, 'create'])
            ->name('applications.create');
        Route::get('application/{id}/doc_load', [ApplicationController::class, 'doc_load'])->name('application.doc_load');
        Route::post('application/{id}/data_load', [ApplicationController::class, 'data_load'])->name('application.data_load');
        Route::post('application/{id}/data_save', [ApplicationController::class, 'data_save'])->name('application.data_save');

        Route::post('applications/store', [ApplicationController::class, 'store'])
            ->name('applications.store');
        Route::post('applications/{id}/update', [ApplicationController::class, 'update'])->name('applications.update');
        Route::get('applications/{id}/delete', [ApplicationController::class, 'destroy'])
            ->name('applications.destroy');



        Route::get('/SparePart/{id}', [SparePartController::class, 'SparePart'])->name('SparePart');
        Route::get('SparePart/{id}/edit', [SparePartController::class, 'edit'])
            ->name('SparePart.edit');
        Route::get('SparePart/{id}/price_select', [SparePartController::class, 'price_select'])
            ->name('SparePart.price_select');
        Route::get('SparePart/{id}/create', [SparePartController::class, 'create'])
            ->name('SparePart.create');
        Route::get('SparePart/{id}/doc_load', [SparePartController::class, 'doc_load'])->name('SparePart.doc_load');
        Route::post('SparePart/{id}/data_load', [SparePartController::class, 'data_load'])->name('SparePart.data_load');
        Route::post('SparePart/{id}/data_save', [SparePartController::class, 'data_save'])->name('SparePart.data_save');

        Route::post('SpareParts/store', [SparePartController::class, 'store'])
            ->name('SparePart.store');
        Route::post('SparePart/{id}/update', [SparePartController::class, 'update'])->name('SparePart.update');
        Route::post('SparePart/{id}/update_price', [SparePartController::class, 'update_price'])->name('SparePart.update_price');
        Route::get('SparePart/{id}/delete', [SparePartController::class, 'destroy'])
            ->name('SparePart.destroy');

        Route::get('/Work/{id}', [WorkController::class, 'Work'])->name('Work');
        Route::get('Work/{id}/edit', [WorkController::class, 'edit'])
            ->name('Work.edit');
        Route::get('Work/{id}/create', [WorkController::class, 'create'])
            ->name('Work.create');
        Route::get('Work/{id}/doc_load', [WorkController::class, 'doc_load'])->name('Work.doc_load');
        Route::post('Work/{id}/data_load', [WorkController::class, 'data_load'])->name('Work.data_load');
        Route::post('Work/{id}/data_save', [WorkController::class, 'data_save'])->name('Work.data_save');
        Route::post('Works/store', [WorkController::class, 'store'])
            ->name('Work.store');
        Route::post('Work/{id}/update', [WorkController::class, 'update'])->name('Work.update');
        Route::get('Work/{id}/delete', [WorkController::class, 'destroy'])
            ->name('Work.destroy');

        Route::get('/troubleshooting/{id}', [TroubleshootingController::class, 'troubleshooting'])->name('troubleshooting');
        Route::get('troubleshooting/{id}/edit', [TroubleshootingController::class, 'edit'])
            ->name('troubleshooting.edit');
        Route::get('troubleshooting/{id}/create', [TroubleshootingController::class, 'create'])
            ->name('troubleshooting.create');
        Route::post('troubleshootings/store', [TroubleshootingController::class, 'store'])
            ->name('troubleshooting.store');
        Route::post('troubleshooting/{id}/update', [TroubleshootingController::class, 'update'])->name('troubleshooting.update');
        Route::get('troubleshooting/{id}/delete', [TroubleshootingController::class, 'destroy'])
            ->name('troubleshooting.destroy');


        Route::get('/document/{id}', [MainController::class, 'document'])->name('document');

        Route::get('documents/{id}/doc_load', [MainController::class, 'doc_load'])->name('doc_load');
        Route::post('documents/{id}/doc_add', [MainController::class, 'doc_add'])->name('doc_add');
        Route::post('documents/{id}/priemka_add', [MainController::class, 'priemka_add'])->name('priemka_add');
        Route::get('document/{id}/doc_edit/{doc_id}', [MainController::class, 'doc_edit'])->name('doc_edit');
        Route::get('document/{id}/doc_delete/{doc_id}', [MainController::class, 'doc_delete'])->name('doc_delete');
        Route::post('document/{id}/doc_update/{doc_id}', [MainController::class, 'doc_update'])->name('doc_update');
        Route::get('/gallery/{id}/{location}/{name}', [MainController::class, 'gallery'])->name('gallery');
        Route::get('/gallery/{id}/{location}/{name}', [MainController::class, 'gallery'])->name('gallery');
        Route::post('/photo_destroy', [MainController::class, 'photo_destroy'])->name('photo_destroy');
        Route::post('/doc_status_change', [MainController::class, 'doc_status_change'])->name('doc_status_change');


        Route::get('/showdoc/{id}', [MainController::class, 'showdoc'])->name('showdoc');
        Route::get('/download/{id}/{location}', [MainController::class, 'download'])->name('download');

        Route::get('export_hda/{id}', [WordController::class, 'export_hda'])->name('export_hda');
    }

);
