<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImapController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\TroubleshootingController;
use App\Http\Controllers\WordController;
use App\Http\Controllers\LawyersController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SparePartController;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\PersonalController;

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
Route::get('/mail_send',  [ImapController::class, 'send'])->name('mail_send');






Route::middleware("can:administrator")->group(
    function () {
        Route::get('/admin',  [AdminController::class, 'index'])->name('admin');
        Route::get('/users',  [AdminController::class, 'users_index'])->name('users');
        Route::get('users/create', [AdminController::class, 'create_user'])
            ->name('users.create');
        Route::post('/user_register',  [AdminController::class, 'user_register'])->name('user_register');
        Route::get('/user/{id}', [AdminController::class, 'user_edit'])->name('user_edit');
        Route::post('/user/{id}/update', [AdminController::class, 'update_user'])->name('user_update');
        Route::post('/role_company/{id}/create', [AdminController::class, 'role_company_create'])->name('role_company_create');
        Route::get('user/{id}/delete', [AdminController::class, 'destroy_user'])
            ->name('user.destroy');
        Route::get('user_role/delete/{role_id}', [AdminController::class, 'destroy_role'])
            ->name('user_role.destroy');
        Route::get('user_city/delete/{role_id}', [AdminController::class, 'destroy_city'])
            ->name('user_city.destroy');
        Route::get('user_company/delete/{id}', [AdminController::class, 'destroy_company'])
            ->name('user_company.destroy');
        Route::prefix('/services')->group(
            function () {
                Route::get('/', [AdminController::class, 'services_index'])->name('services');
                Route::get('/create', [AdminController::class, 'create_service'])
                    ->name('service.create');
                Route::post('/store', [AdminController::class, 'store_service'])
                    ->name('service.store');
                Route::get('/{id}', [AdminController::class, 'service_edit'])
                    ->name('service.edit');
                Route::post('/{id}/update', [AdminController::class, 'update_service'])
                    ->name('service.update');
                Route::get('/{id}/delete', [AdminController::class, 'destroy_service'])
                    ->name('service.destroy');
            }
        );
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

        Route::get('/back',  [MainController::class, 'back'])->name('back');

        Route::get('/message',  [MainController::class, 'message'])->name('message');
        Route::get('/mailbox/{type}/{id}/{message_id}',  [MainController::class, 'mailbox'])->name('mailbox');

        Route::get('/mail_delete/{id}',  [ApplicationController::class, 'delete_message'])->name('delete_message');
        Route::post('/test',  [MainController::class, 'test'])->name('test');

        Route::get('/answer_fail_sel/{id}', [ApplicationController::class, 'answer_fail_sel'])->name('answer_fail_sel');
        Route::post('/answer_fail_save/{id}', [ApplicationController::class, 'answer_fail_save'])->name('answer_fail_save');
        Route::get('/answer_true_sel/{id}', [ApplicationController::class, 'answer_true_sel'])->name('answer_true_sel');
        Route::post('/answer_true_save/{id}', [ApplicationController::class, 'answer_true_save'])->name('answer_true_save');
        Route::get('/mail/{id}',  [ApplicationController::class, 'message'])->name('mail_app');
        Route::get('/chat/{id}',  [ApplicationController::class, 'chat'])->name('chat');
        Route::post('/chat_sent/{id}',  [ApplicationController::class, 'chat_sent'])->name('chat_sent');

        Route::get('/answer_ic_true/{id}',  [ApplicationController::class, 'answer_ic_true'])->name('answer_ic_true');
        Route::post('/answer_ic_true_sent/{id}',  [ApplicationController::class, 'answer_ic_true_sent'])->name('answer_ic_true_sent');
        Route::get('/answer_ic_mrk/{id}',  [ApplicationController::class, 'answer_ic_mrk'])->name('answer_ic_mrk');
        Route::get('/answer_ic_trouble/{id}',  [ApplicationController::class, 'answer_ic_trouble'])->name('answer_ic_trouble');
        Route::post('/answer_ic_mrk_sent/{id}',  [ApplicationController::class, 'answer_ic_mrk_sent'])->name('answer_ic_mrk_sent');
        Route::get('/answer_ic_sch/{id}',  [ApplicationController::class, 'answer_ic_sch'])->name('answer_ic_sch');
        Route::post('/answer_ic_sch_sent/{id}',  [ApplicationController::class, 'answer_ic_sch_sent'])->name('answer_ic_sch_sent');
        Route::get('/answer_ic/{id}',  [ApplicationController::class, 'answer_ic'])->name('answer_ic');
        Route::post('/answer_ic_sent/{id}',  [ApplicationController::class, 'answer_ic_sent'])->name('answer_ic_sent');
        Route::get('/history/{id}',  [ApplicationController::class, 'history'])->name('history');
        Route::get('/logout',  [AuthController::class, 'logout'])->name('logout');
        Route::get('/lawyers',  [ApplicationController::class, 'lawyers'])->name('lawyers');
        Route::get('/archive',  [ApplicationController::class, 'archive'])->name('archive');
        Route::get('/temp',  [ApplicationController::class, 'temp'])->name('temp');
        Route::get('/priem',  [ApplicationController::class, 'priem'])->name('priem');
        Route::get('/failures',  [ApplicationController::class, 'failures'])->name('failures');
        Route::post('/find',  [ApplicationController::class, 'find'])->name('find');

        Route::get('/applications',  [ApplicationController::class, 'applications'])->name('applications');
        Route::get('/directions',  [ApplicationController::class, 'directions'])->name('directions');
        Route::get('/waiting',  [ApplicationController::class, 'waiting'])->name('waiting');
        Route::get('/application/{id}', [ApplicationController::class, 'application'])->name('application');
        Route::get('applications/{id}/edit', [ApplicationController::class, 'edit'])
            ->name('applications.edit');
        Route::get('applications/{id}/send_mrk/{id_MRK}', [ApplicationController::class, 'send_mrk'])
            ->name('applications.send_mrk');
        Route::get('applications/{id}/change_status/{variant}', [ApplicationController::class, 'change_status'])
            ->name('applications.change_status');
        Route::get('applications/{id}/change_st/{status_id}', [ApplicationController::class, 'change_st'])
            ->name('applications.change_st');

        Route::get('applications/{id}/change_status_km/{variant}', [ApplicationController::class, 'change_status_km'])
            ->name('applications.change_status_km');

        Route::get('applications/create', [ApplicationController::class, 'create'])
            ->name('applications.create');

        Route::get('applications/add_mail/{type}/{id}/{message_id}', [ApplicationController::class, 'add_mail'])
            ->name('applications.add_mail');
        Route::get('applications/new_mail/{type}/{id}/{message_id}/{id_application}', [ApplicationController::class, 'new_mail'])
            ->name('applications.new_mail');
        Route::get('applications/move_mail/{id_prev}/{message_id}/{id_next}', [ApplicationController::class, 'move_mail'])
            ->name('applications.move_mail');
        Route::get('applications/move_mail_index/{id_prev}/{id}/{message_id}', [ApplicationController::class, 'move_mail_index'])
            ->name('applications.move_mail_index');
        Route::get('combine/{id_comp}/{id}', [ApplicationController::class, 'combine'])
            ->name('combine');
        Route::get('combine_fin/{id_prev}/{id_next}', [ApplicationController::class, 'combine_fin'])
            ->name('combine_fin');
        Route::get('application/{id}/doc_load', [ApplicationController::class, 'doc_load'])->name('application.doc_load');
        Route::get('application/{id}/tg_load', [ApplicationController::class, 'tg_load'])->name('application.tg_load');
        Route::post('application/{id}/data_load', [ApplicationController::class, 'data_load'])->name('application.data_load');
        Route::post('application/{id}/data_save', [ApplicationController::class, 'data_save'])->name('application.data_save');

        Route::post('applications/store', [ApplicationController::class, 'store'])
            ->name('applications.store');
        Route::post('applications/{id}/update', [ApplicationController::class, 'update'])->name('applications.update');
        Route::get('applications/{id}/delete', [ApplicationController::class, 'destroy'])
            ->name('applications.destroy');
        Route::get('applications/{id}/move_temp', [ApplicationController::class, 'move_temp'])
            ->name('applications.move_temp');
        Route::get('lawyers/create/{id}', [LawyersController::class, 'create'])
            ->name('lawyers.create');
        Route::post('lawyers/store/{id}', [LawyersController::class, 'store'])
            ->name('lawyers.store');



        Route::get('/SparePart/{id}', [SparePartController::class, 'SparePart'])->name('SparePart');
        Route::get('SparePart/{id}/edit', [SparePartController::class, 'edit'])
            ->name('SparePart.edit');
        Route::get('SparePart/{id}/price_select', [SparePartController::class, 'price_select'])
            ->name('SparePart.price_select');
        Route::get('SparePart/{id}/create', [SparePartController::class, 'create'])
            ->name('SparePart.create');
        Route::get('SparePart/{id}/doc_load', [SparePartController::class, 'doc_load'])->name('SparePart.doc_load');
        Route::post('SparePart/{id}/data_load', [SparePartController::class, 'data_load'])->name('SparePart.data_load');
        Route::post('sertificat/{id}', [SparePartController::class, 'sertificat'])->name('SparePart.sertificat');
        Route::post('estimate/{id}', [SparePartController::class, 'estimate'])->name('SparePart.estimate');
        Route::post('emr10/{id}', [SparePartController::class, 'emr10'])->name('SparePart.emr10');

        Route::post('SparePart/{id}/data_save', [SparePartController::class, 'data_save'])->name('SparePart.data_save');

        Route::post('SpareParts/store', [SparePartController::class, 'store'])
            ->name('SparePart.store');
        Route::post('SparePart/{id}/update', [SparePartController::class, 'update'])->name('SparePart.update');
        Route::post('SparePart/{id}/update_price', [SparePartController::class, 'update_price'])->name('SparePart.update_price');
        Route::get('SparePart/{id}/delete', [SparePartController::class, 'destroy'])
            ->name('SparePart.destroy');

        Route::get('/Work/{id}', [WorkController::class, 'Work'])->name('Work');
        Route::get('/work_edit/{id}/{id_work}/{count}', [WorkController::class, 'Work_edit'])->name('Work_edit');
        Route::get('/work_edit_paint/{id}/{id_work}/{count}', [WorkController::class, 'Work_edit_paint'])->name('Work_edit_paint');
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

        Route::get('export_hda/{id}/{status_id}', [WordController::class, 'export_hda'])->name('export_hda');
        Route::get('export/{id}', [WordController::class, 'export'])->name('export');
        Route::get('export_zayav/{id}/{status_id}', [WordController::class, 'export_zayav'])->name('export_zayav');
        Route::get('export_dk/{id}/{status_id}', [WordController::class, 'export_dk'])->name('export_dk');
        Route::get('export_ap/{id}/{status_id}', [WordController::class, 'export_ap'])->name('export_ap');
        Route::get('export_as/{id}/{status_id}', [WordController::class, 'export_as'])->name('export_as');
        Route::get('export_if/{id}/{status_id}', [WordController::class, 'export_if'])->name('export_if');
        Route::get('export_sogl/{id}/{status_id}', [WordController::class, 'export_sogl'])->name('export_sogl');
        Route::get('export_ds/{id}/{status_id}', [WordController::class, 'export_ds'])->name('export_ds');
        Route::get('export_zn/{id}/{status_id}', [WordController::class, 'export_zn'])->name('export_zn');
        Route::get('export_pzn/{id}/{status_id}', [WordController::class, 'export_pzn'])->name('export_pzn');
        Route::get('export_sch/{id}/{status_id}', [WordController::class, 'export_sch'])->name('export_sch');
        Route::get('create_all/{id}/{status_id}', [WordController::class, 'create_all'])->name('create_all');
        Route::prefix('personal')->group(
            function () {
                Route::get('/', [PersonalController::class, 'index'])->name('personal');
                Route::get('/role', [PersonalController::class, 'role'])->name('personal.role');
                Route::get('/telegram', [PersonalController::class, 'telegram'])->name('personal.telegram');
                Route::get('/telegram_delete', [PersonalController::class, 'telegram_delete'])->name('personal.telegram_delete');
                Route::post('/role_update', [PersonalController::class, 'role_update'])->name('personal.role_update');
                Route::post('/telegram_update', [PersonalController::class, 'telegram_update'])->name('personal.telegram_update');
                Route::get('/create', [PersonalController::class, 'create'])->name('personal.create');
                Route::post('/store', [PersonalController::class, 'store'])->name('personal.store');
                Route::post('/update', [PersonalController::class, 'update'])->name('personal.update');
            }
        );
        Route::get('/auth/telegram/callback', [PersonalController::class, 'callback'])->name('telegram.callback');;


        Route::get('/auth/telegram/redirect', function () {
            return Socialite::driver('telegram')->redirect();
        });
    }

);


Route::get('/test/{id}/{id_mes}', [ApplicationController::class, 'test'])->name('test-new');
