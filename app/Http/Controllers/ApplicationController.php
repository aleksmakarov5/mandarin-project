<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use App\Models\application;
use App\Models\chat_attachment;
use App\Models\work;
use RtfHtmlPhp\Document;
use App\Jobs\QueueSenderChat;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use App\Models\User;
use App\Models\chat_rec;
use App\Models\InsuranceCompany;
use App\Models\estimate;
use App\Models\history;
use App\Models\chat;
use App\Models\attachment;
use App\Models\failure;
use App\Models\ApplicationStatus;
use App\Models\tru;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\message;
use App\Models\address;
use App\Models\service;
use App\Models\notify;
use App\Models\SparePart;
use App\Models\user_role;
use App\Models\troubleshooting;
use App\Models\OurCompany;
use App\Models\status_application;
use App\Mail\user\mrknotify;
use App\Mail\user\newmessage;
use App\Mail\user\ic_answer;
use Carbon\Carbon;
use App\Events\MessageSent;
use Tochka\Calendar\WorkCalendar;
use App\Models\status_chat;
use App\Models\status_message;
use App\Models\telegram_action;
use App\Models\city;
use App\Models\user_filter;
use Illuminate\Support\Number;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use DefStudio\Telegraph\Keyboard\ReplyButton;

// use App\Exports\UsersExport;
// use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Validation\Rules\Can;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Psy\Command\HistoryCommand;
use Illuminate\Support\Facades\Mail as Mail_send;
use Jenssegers\Date\Date;

class ApplicationController extends Controller
{

    public function configure($applications)
    {
        $Spareparts = SparePart::get()->groupBy('id_application');
        $Estimates = estimate::get()->groupBy('id_application');
        $Works = work::get()->groupBy('id_application');
        foreach ($applications as $application) {
            if (isset($Spareparts[$application->id]))
                $spareparts = $Spareparts[$application->id];
            else
                $spareparts = $application->spareparts;
            if (isset($Estimates[$application->id])) {
                $estimates = $Estimates[$application->id][0];
                if ($application->ub_type)
                    $estimates->price_repair_full = $estimates->price_repair_half;
            } else
                $estimates = $application->estimates;
            if (isset($Works[$application->id]))
                $Works_serv = $Works[$application->id];
            else
                $Works_serv = $application->works;
            $summ_service = 0;
            $summ_service_ic = 0;
            foreach ($Works_serv as $work) {
                $summ_service = $summ_service + (float) str_replace(' ', '', $work->price_service);
                $summ_service_ic = $summ_service_ic + (float) str_replace(' ', '', $work->summ);
            }
            // dd($estimates);

            // dd($rewiew->all()) ;
            $summ_ic_full = 0;
            $summ_ic_half = 0;
            $summ_order = 0;
            $eco_delta_full = 0;
            foreach ($spareparts as $i => $sparepart) {
                $summ_ic_full = $summ_ic_full + (float) str_replace(' ', '', $sparepart->price_ic_full);
                $summ_ic_half = $summ_ic_half + (float) str_replace(' ', '', $sparepart->price_ic_half);
                switch ($sparepart->selected_price) {
                    case 1:
                        $summ_order = $summ_order + (float) str_replace(' ', '', $sparepart->price_original);
                        break;
                    case 2:
                        $summ_order = $summ_order + (float) str_replace(' ', '', $sparepart->price_analogue);
                        break;
                    case 3:
                        $summ_order = $summ_order + (float) str_replace(' ', '', $sparepart->price_used);
                        break;
                }
            }

            $proc = 0;
            if (isset($estimates->price_repair_full)) {
                if ($estimates->emr10) {
                    $eco_delta_full =
                        (float) str_replace(' ', '', $estimates->price_repair_full) * 1.1 -
                        $summ_order -
                        $summ_service -
                        $application->sertificat;
                    if (((float) str_replace(' ', '', $estimates->price_repair_full)) != 0)
                        $proc = round(
                            ($eco_delta_full / ((float) str_replace(' ', '', $estimates->price_repair_full) * 1.1)) * 100,
                        );
                } else {
                    $eco_delta_full =
                        (float) str_replace(' ', '', $estimates->price_repair_full) -
                        $summ_order -
                        $summ_service -
                        $application->sertificat;
                    // dd($estimates);
                    if (((float) str_replace(' ', '', $estimates->price_repair_full)) != 0)
                        $proc = round(($eco_delta_full / ((float) str_replace(' ', '', $estimates->price_repair_full))) * 100);
                }
            } else {
                $eco_delta_full = $summ_ic_full + $summ_service_ic - $summ_order - $summ_service - $application->sertificat;
                if ($summ_ic_full + $summ_service_ic != 0) {
                    $proc = round(($eco_delta_full / ($summ_ic_full + $summ_service_ic)) * 100);
                }
            }
            if (isset($estimates->price_repair_half)) {
                $eco_delta_half = (float) str_replace(' ', '', $estimates->price_repair_half) - $summ_order - $summ_service;
            } else {
                $eco_delta_half = $summ_ic_half + $summ_service_ic - $summ_order - $summ_service;
            }
            $count = ['application' => 0, 'chat' => 0, 'message' => 0];

            $count['application'] = $application->status_applications->count();

            $count['chat'] = $application->status_chats->count();

            $count['message'] = $application->status_messages->count();
            $application->count = $count;
            $application->eco_delta_full = Number::Currency($eco_delta_full, 'RUR', 'ru');
            $application->summ_order = Number::Currency($summ_order, 'RUR', 'ru');
            $application->summ_ic_full = Number::Currency(isset($estimates->price_repair_full) ? (float) $estimates->price_repair_full : $summ_ic_full, 'RUR', 'ru');
            $application->summ_service = Number::Currency($summ_service, 'RUR', 'ru');
            $application->summ_full =
                Number::Currency($summ_service + $summ_order, 'RUR', 'ru');
            $application->proc = $proc;
            // dd($application, $estimates->price_repair_full);
        }
        return $applications;
    }
    public function applications(Request $request)
    {
        // Excel::store(new UsersExport, 'public/files/users.xlsx');
        // exec('libreoffice --headless --convert-to pdf "storage/files/users.xlsx" --outdir storage/files/');
        // // $mass = json_decode('[{"id":"AgACAgIAAxkBAAIhpGcnFRdukvw3OjtKvHtuxYzi4ASlAAKy4jEbt8w4SZl9KUmv94ciAQADAgADcwADNgQ","width":90,"height":76,"filesize":1576},{"id":"AgACAgIAAxkBAAIhpGcnFRdukvw3OjtKvHtuxYzi4ASlAAKy4jEbt8w4SZl9KUmv94ciAQADAgADbQADNgQ","width":320,"height":272,"filesize":25227},{"id":"AgACAgIAAxkBAAIhpGcnFRdukvw3OjtKvHtuxYzi4ASlAAKy4jEbt8w4SZl9KUmv94ciAQADAgADeAADNgQ","width":800,"height":679,"filesize":95924},{"id":"AgACAgIAAxkBAAIhpGcnFRdukvw3OjtKvHtuxYzi4ASlAAKy4jEbt8w4SZl9KUmv94ciAQADAgADeQADNgQ","width":1244,"height":1056,"filesize":172992}]');
        // $array = json_decode('{"ok":true,"result":{"message_id":9382,"from":{"id":6552669400,"is_bot":true,"first_name":"\u041c\u0430\u043d\u0434\u0430\u0440\u0438\u043d \u041f\u043e\u0440\u0442\u0430\u043b","username":"mandarin_portal_bot"},"chat":{"id":938497694,"first_name":"\u0410\u043b\u0435\u043a\u0441\u0435\u0439","last_name":"\u041c\u0430\u043a\u0430\u0440\u043e\u0432","username":"aleksey_makaro","type":"private"},"date":1730658033,"text":"\u041f\u043e\u043b\u0443\u0447\u0435\u043d\u043e \u0438\u0437\u043e\u0431\u0440\u0430\u0436\u0435\u043d\u0438\u0439: 115"}}');
        // dd($array->result->message_id);
        $only_me = 0;
        $only_me = $request->get('only_me');

        if (Gate::allows('administrator')) {
            $applications = application::where(function ($query) {
                $query
                    ->where('applications.id_status', '<', '12')
                    ->where('applications.id_status', '<>', '6')
                    ->where('applications.id_status', '<>', '7');
            })

                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('attachments', function ($join) {
                    $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
                })

                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
                ->with('status_chats', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_applications', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_messages', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                });
            $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

            if (isset($user_filters[0]->id_city)) {
                $applications->where(function ($query) use ($user_filters) {
                    $query->where('id_city', '=', $user_filters[0]->id_city);
                    for ($i = 1; $i < $user_filters->count(); $i++) {
                        $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                    }
                    if ($user_filters->count() == (city::all()->count()))
                        $query->orWhere('id_city', '=', 0);
                });
            }
            $applications = $applications->select(
                'applications.*',
                'insurance_companies.name',
                'insurance_companies.insurance_companies_theme',
                'services.service_name',
                'services.services_theme',
                'lawyers.lawyer_name',
                'lawyers.color as lawyers_color',
                'application_statuses.StatusName',
                'application_statuses.status_theme',
                'application_statuses.current_group',
                'application_statuses.time',
                'application_statuses.day',
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city',
                'attachments.id_status as doc'




            )->orderBy('applications.id', 'desc')->paginate(2000);
        } elseif (Gate::allows('admin') || Gate::allows('km')) {


            $applications = application::where(function ($query) {
                $query
                    ->where('applications.id_status', '<', '12')
                    ->where('applications.id_status', '<>', '6')
                    ->where('applications.id_status', '<>', '7');
            })
                ->join('user_roles', function ($join) {
                    $join->where(function ($query) {
                        $query
                            ->where('user_roles.id_group', '=', 2)
                            ->orWhere('user_roles.id_group', '=', 8)
                            ->orWhere('user_roles.id_group', '=', 3);
                    })->where('user_roles.id_user', '=', Auth::user()->id);
                })
                ->join('role_companies', function ($join) {
                    $join->on('role_companies.id_company', '=', 'applications.our_company')->on('role_companies.id_role', '=', 'user_roles.id');
                })
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('attachments', function ($join) {
                    $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
                })
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
                ->with('status_chats', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_applications', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_messages', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                });
            $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

            if (isset($user_filters[0]->id_city)) {
                $applications->where(function ($query) use ($user_filters) {
                    $query->where('id_city', '=', $user_filters[0]->id_city);
                    for ($i = 1; $i < $user_filters->count(); $i++) {
                        $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                    }
                    if ($user_filters->count() == (city::all()->count()))
                        $query->orWhere('id_city', '=', 0);
                });
            }
            $applications = $applications



                ->select(
                    'applications.*',
                    'insurance_companies.name',
                    'insurance_companies.insurance_companies_theme',
                    'services.service_name',
                    'services.services_theme',
                    'lawyers.lawyer_name',
                    'lawyers.color as lawyers_color',
                    'application_statuses.StatusName',
                    'application_statuses.status_theme',
                    'application_statuses.current_group',
                    'application_statuses.time',
                    'application_statuses.day',
                    'our_companies.our_name',
                    'users.name as MRK_name',
                    'cities.name as city',
                    'attachments.id_status as doc'
                )->orderBy('applications.id', 'desc')
                ->paginate(300);
        } elseif ($only_me) {


            $applications = application::where(function ($query) {
                $query
                    ->where('applications.id_status', '<', '12')
                    ->where('applications.id_status', '<>', '6')
                    ->where('applications.id_status', '<>', '7');
            })
                ->join('user_roles', function ($join) {
                    $join->where(function ($query) {
                        $query
                            ->where('user_roles.id_group', '=', 5);
                    })->where('user_roles.id_user', '=', Auth::user()->id);
                })
                ->join('role_companies', function ($join) {
                    $join->on('role_companies.id_company', '=', 'applications.our_company')->on('role_companies.id_role', '=', 'user_roles.id');
                })
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('attachments', function ($join) {
                    $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
                })
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
                ->with('status_chats', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_applications', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_messages', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                });
            $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

            if (isset($user_filters[0]->id_city)) {
                $applications->where(function ($query) use ($user_filters) {
                    $query->where('id_city', '=', $user_filters[0]->id_city);
                    for ($i = 1; $i < $user_filters->count(); $i++) {
                        $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                    }
                    if ($user_filters->count() == (city::all()->count()))
                        $query->orWhere('id_city', '=', 0);
                });
            }
            $applications = $applications->select(
                'applications.*',
                'insurance_companies.name',
                'insurance_companies.insurance_companies_theme',
                'services.service_name',
                'services.services_theme',
                'lawyers.lawyer_name',
                'lawyers.color as lawyers_color',
                'application_statuses.StatusName',
                'application_statuses.status_theme',
                'application_statuses.current_group',
                'application_statuses.time',
                'application_statuses.day',
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city',
                'attachments.id_status as doc'
            )->orderBy('applications.id', 'desc')
                ->paginate(300);
        } elseif (Gate::allows('service')) {
            $applications = application::where(function ($query) {
                $query
                    ->where('applications.id_status', '<', '12')
                    ->where('applications.id_status', '<>', '6')
                    ->where('applications.id_status', '<>', '7');;
            })
                ->where('applications.id_service', '=', service::where('id_user', '=', Auth::user()->id)->first()->id)
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
                ->leftjoin('attachments', function ($join) {
                    $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
                })
                ->with('status_chats', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_applications', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_messages', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                });
            $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

            if (isset($user_filters[0]->id_city)) {
                $applications->where(function ($query) use ($user_filters) {
                    $query->where('id_city', '=', $user_filters[0]->id_city);
                    for ($i = 1; $i < $user_filters->count(); $i++) {
                        $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                    }
                    if ($user_filters->count() == (city::all()->count()))
                        $query->orWhere('id_city', '=', 0);
                });
            }
            $applications = $applications->select(
                'applications.*',
                'insurance_companies.name',
                'insurance_companies.insurance_companies_theme',
                'services.service_name',
                'services.services_theme',
                'lawyers.lawyer_name',
                'lawyers.color as lawyers_color',
                'application_statuses.StatusName',
                'application_statuses.status_theme',
                'application_statuses.current_group',
                'application_statuses.time',
                'application_statuses.day',
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city',
                'attachments.id_status as doc'
            )->orderBy('applications.id', 'desc')->paginate(300);
        } elseif (Gate::allows('mrk')) {
            $applications = application::where(function ($query) {
                $query

                    ->where('applications.id_status', '<', '12')
                    ->where('applications.id_status', '<>', '6')
                    ->where('applications.id_status', '<>', '7');;
            })

                ->join('user_roles', function ($join) {
                    $join
                        ->where('user_roles.id_group', '=', 5)
                        ->where('user_roles.id_user', '=', Auth::user()->id);
                })
                ->join('role_companies', function ($join) {
                    $join->on('role_companies.id_company', '=', 'applications.our_company')->on('role_companies.id_role', '=', 'user_roles.id');
                })
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('attachments', function ($join) {
                    $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
                })
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
                ->with('status_chats', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_applications', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_messages', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                });
            $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

            if (isset($user_filters[0]->id_city)) {
                $applications->where(function ($query) use ($user_filters) {
                    $query->where('id_city', '=', $user_filters[0]->id_city);
                    for ($i = 1; $i < $user_filters->count(); $i++) {
                        $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                    }
                    if ($user_filters->count() == (city::all()->count()))
                        $query->orWhere('id_city', '=', 0);
                });
            }
            $applications = $applications->select(
                'applications.*',
                'insurance_companies.name',
                'insurance_companies.insurance_companies_theme',
                'services.service_name',
                'services.services_theme',
                'lawyers.lawyer_name',
                'lawyers.color as lawyers_color',
                'application_statuses.StatusName',
                'application_statuses.status_theme',
                'application_statuses.current_group',
                'application_statuses.time',
                'application_statuses.day',

                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city',
                'attachments.id_status as doc'
            )->orderBy('applications.id', 'desc')->paginate(300);
            // dd($applications);
        } else {
            $applications = application::where(function ($query) {
                $query

                    ->where('applications.id_status', '<', '12')
                    ->where('applications.id_status', '<>', '6')
                    ->where('applications.id_status', '<>', '7');;
            })
                ->where(function ($query) {
                    $query
                        ->Where('applications.id_spm', '=', Auth::user()->id);
                })

                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('attachments', function ($join) {
                    $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
                })

                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
                ->with('status_chats', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_applications', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_messages', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                });
            $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

            if (isset($user_filters[0]->id_city)) {
                $applications->where(function ($query) use ($user_filters) {
                    $query->where('id_city', '=', $user_filters[0]->id_city);
                    for ($i = 1; $i < $user_filters->count(); $i++) {
                        $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                    }
                    if ($user_filters->count() == (city::all()->count()))
                        $query->orWhere('id_city', '=', 0);
                });
            }
            $applications = $applications->select(
                'applications.*',
                'insurance_companies.name',
                'insurance_companies.insurance_companies_theme',
                'services.service_name',
                'services.services_theme',
                'lawyers.lawyer_name',
                'lawyers.color as lawyers_color',
                'application_statuses.StatusName',
                'application_statuses.status_theme',
                'application_statuses.current_group',
                'application_statuses.time',
                'application_statuses.day',
                'application_statuses.time',
                'application_statuses.day',
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city',
                'attachments.id_status as doc'
            )->orderBy('applications.id', 'desc')->paginate(300);
        }
        $current = carbon::now();
        foreach ($applications as $application) {
            // if (isset($application->ic_answer_date)) {
            //     $application->count_hour = 48 - $current->diffInHours($application->ic_answer_date);
            //     $application->max_hour = 48;
            //     $application->StatusName = "Ожидается ответ СК";
            //     $application->count_five = 11;
            // } else
            if ($application->id_status < 11) {

                $end_date = WorkCalendar::create($application->created_at);
                $end_date->addWorkDays($application->time);
                // dd($end_date);
                $start = carbon::create($application->created_at);
                $end = carbon::create($end_date);
                // dd($end);
                $application->max_hour = $start->diffInHours($end);
                // dd($application->max_hour);
                // dd($current);
                //  dd($end);
                $application->count_hour = $application->max_hour - $current->diffInHours($start);
                // dd($application->count_hour);
                $application->created = $application->created_at->toFormattedDateString();
                $application->count_five = 5 - $current->diffInHours($application->check_date);
            }
            if ($application->id_status == 11) {

                $end_date = WorkCalendar::create($application->check_date);
                $end_date->addWorkDays($application->time);

                $start = WorkCalendar::create($application->check_date);
                $application->max_hour = $start->diffInHours($end_date);
                $application->count_hour =  $application->max_hour - $current->diffInHours($start);
                $application->created = $application->created_at->toFormattedDateString();
                $application->count_five = 5 - $current->diffInHours($application->check_date);
            }

            // $doc = attachment::where('id_application', '=', $application->id)->where('id_status', '=', 3)->first();
            // if (isset($doc->id_status))
            //     // $application->doc = 0;
            // $application->doc = $doc->id_status;
            // else
            //     $application->doc = 0;
        }
        // dd($applications);
        // $applications = $this->configure($applications);
        foreach ($applications as $application) {
            $count = ['application' => 0, 'chat' => 0, 'message' => 0];

            $count['application'] = $application->status_applications->count();

            $count['chat'] = $application->status_chats->count();

            $count['message'] = $application->status_messages->count();
            $application->count = $count;
        }
        $city = city::all();
        session(['rout' => 'applications']);
        return view('applications', ['applications' => $applications, 'only_me' => $only_me, 'city' => $city, 'filter' => user_filter::where('id_user', '=', auth::user()->id)->get()]);
    }

    // public function failures()
    // {
    //     if (Gate::allows('administrator')) {
    //         $applications = application::where('id_status', '=', '7')
    //             ->orwhere('id_status', '=', '6')
    //             ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
    //             ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
    //             ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
    //             ->leftjoin('services', 'applications.id_service', '=', 'services.id')
    //             ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
    //             ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')

    //             ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
    //             ->with('status_chats', function ($query) {
    //                 $query->where('id_user', '=', auth::user()->id)
    //                     ->where('is_readed', '=', '0');
    //             })
    //             ->with('status_applications', function ($query) {
    //                 $query->where('id_user', '=', auth::user()->id)
    //                     ->where('is_readed', '=', '0');
    //             })
    //             ->with('status_messages', function ($query) {
    //                 $query->where('id_user', '=', auth::user()->id)
    //                     ->where('is_readed', '=', '0');
    //             })
    //             ->with('spareparts')
    //             ->with('works')
    //             ->with('estimate');
    //         $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

    //         if (isset($user_filters[0]->id_city)) {
    //             $applications->where(function ($query) use ($user_filters) {
    //                 $query->where('id_city', '=', $user_filters[0]->id_city);
    //                 for ($i = 1; $i < $user_filters->count(); $i++) {
    //                     $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
    //                 }
    //                 if ($user_filters->count() == (city::all()->count()))
    //                     $query->orWhere('id_city', '=', 0);
    //             });
    //         }
    //         $applications = $applications->select(
    //             'applications.*',
    //             'insurance_companies.name',
    //             'insurance_companies.insurance_companies_theme',
    //             'services.service_name',
    //             'services.services_theme',
    //             'lawyers.lawyer_name',
    //             'lawyers.color as lawyers_color',
    //             'application_statuses.StatusName',
    //             'application_statuses.status_theme',
    //             'application_statuses.current_group',
    //             'our_companies.our_name',
    //             'users.name as MRK_name',
    //             'cities.name as city'
    //         )->orderBy('applications.id', 'desc')
    //             ->paginate(300);
    //     } else {
    //         $applications = application::where('id_status', '=', '7')
    //             ->orwhere('id_status', '=', '6')
    //             ->join('user_roles', function ($join) {
    //                 $join
    //                     ->where('user_roles.id_group', '=', 8)
    //                     ->where('user_roles.id_user', '=', Auth::user()->id);
    //             })
    //             ->join('role_companies', function ($join) {
    //                 $join->on('role_companies.id_company', '=', 'applications.our_company')->on('role_companies.id_role', '=', 'user_roles.id');
    //             })
    //             ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
    //             ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

    //             ->leftjoin('services', 'applications.id_service', '=', 'services.id')
    //             ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
    //             ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
    //             ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')

    //             ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
    //             ->with('status_chats', function ($query) {
    //                 $query->where('id_user', '=', auth::user()->id)
    //                     ->where('is_readed', '=', '0');
    //             })
    //             ->with('status_applications', function ($query) {
    //                 $query->where('id_user', '=', auth::user()->id)
    //                     ->where('is_readed', '=', '0');
    //             })
    //             ->with('status_messages', function ($query) {
    //                 $query->where('id_user', '=', auth::user()->id)
    //                     ->where('is_readed', '=', '0');
    //             })
    //             ->with('spareparts')
    //             ->with('works')
    //             ->with('estimate');
    //         $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

    //         if (isset($user_filters[0]->id_city)) {
    //             $applications->where(function ($query) use ($user_filters) {
    //                 $query->where('id_city', '=', $user_filters[0]->id_city);
    //                 for ($i = 1; $i < $user_filters->count(); $i++) {
    //                     $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
    //                 }
    //                 if ($user_filters->count() == (city::all()->count()))
    //                     $query->orWhere('id_city', '=', 0);
    //             });
    //         }
    //         $applications = $applications->select(
    //             'applications.*',
    //             'insurance_companies.name',
    //             'insurance_companies.insurance_companies_theme',
    //             'services.service_name',
    //             'services.services_theme',
    //             'lawyers.lawyer_name',
    //             'lawyers.color as lawyers_color',
    //             'application_statuses.StatusName',
    //             'application_statuses.status_theme',
    //             'application_statuses.current_group',
    //             'our_companies.our_name',
    //             'users.name as MRK_name',
    //             'cities.name as city'
    //         )->orderBy('applications.id', 'desc')
    //             ->paginate(300);
    //     }
    //     // dd($applications);
    //     $current = carbon::now();
    //     foreach ($applications as $application) {
    //         $application->count_hour = 48 - $current->diffInHours($application->created_at);
    //         $application->max_hour = 48;
    //         $doc = attachment::where('id_application', '=', $application->id)->where('id_status', '=', 2)->first();
    //         if (isset($doc->id_status))
    //             $application->doc = $doc->id_status;
    //         else
    //             $application->doc = 0;
    //     }
    //     $applications = $this->configure($applications);
    //     $city = city::all();
    //     session(['rout' => 'failures']);


    //     return view('applications', ['applications' => $applications, 'city' => $city, 'filter' => user_filter::where('id_user', '=', auth::user()->id)->get()],);
    // }

    public function find(Request $request)
    {

        $data = $this->validate($request, [
            'find' => 'required',
        ]);
        $data['find'] = str_replace(' ', '', $data['find']);
        $applications = application::where('gnz', 'LIKE', '%' . $data['find'] . '%')
            ->orwhere('numberInsuranceCompanyApplication', 'LIKE', '%' . $data['find'] . '%')
            ->orwhere('ClientName', 'LIKE', '%' . $data['find'] . '%')
            ->orwhere('CarModel', 'LIKE', '%' . $data['find'] . '%')
            ->orwhere('ClientPhone', 'LIKE', '%' . $data['find'] . '%')
            ->orwhere('applications.id', 'LIKE', '%' . $data['find'] . '%')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

            ->join('finance_statuses', 'finance_statuses.id', '=', 'applications.finance_statuses_id')
            ->leftjoin('services', 'applications.id_service', '=', 'services.id')
            ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
            ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')

            ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
            ->leftjoin('attachments', function ($join) {
                $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
            })

            ->with('status_chats', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('status_applications', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('status_messages', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('spareparts')
            ->with('works')
            ->with('estimate')
            ->select(
                'applications.*',
                'insurance_companies.name',
                'finance_statuses.FinStatusName',
                'finance_statuses.fin_time',
                'finance_statuses.fin_day',
                'finance_statuses.FinStatusTheme',
                'insurance_companies.insurance_companies_theme',
                'services.service_name',
                'services.services_theme',
                'lawyers.lawyer_name',
                'lawyers.color as lawyers_color',
                'application_statuses.StatusName',
                'application_statuses.status_theme',
                'application_statuses.current_group',
                'application_statuses.time',
                'application_statuses.day',
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city',
                'attachments.id_status as doc'
            )->paginate(200);
        // dd($applications);
        $current = carbon::now();
        foreach ($applications as $application) {
            $application->count_hour = 48 - $current->diffInHours($application->created_at);
            $application->max_hour = 48;

            $application->count_five = 5 - $current->diffInHours($application->check_date);
            // $doc = attachment::where('id_application', '=', $application->id)->where('id_status', '=', 2)->first();
            // if (isset($doc->id_status))
            //     $application->doc = $doc->id_status;
            // else
            //     $application->doc = 0;
        }
        $current = carbon::now();
        foreach ($applications as $application) { {

                // if (isset($application->check_date)) {
                //     $end_date = WorkCalendar::create($application->check_date);
                //     $end_date->addWorkDays(1);
                //     dd($end_date, $application->id);
                // }
                if ($application->id_status < 11) {

                    $end_date = WorkCalendar::create($application->created_at);
                    $end_date->addWorkDays($application->time);
                    // dd($end_date);
                    $start = carbon::create($application->created_at);
                    $end = carbon::create($end_date);
                    // dd($end);
                    $application->max_hour = $start->diffInHours($end);
                    // dd($application->max_hour);
                    // dd($current);
                    //  dd($end);
                    $application->count_hour = $application->max_hour - $current->diffInHours($start);
                    // dd($application->count_hour);
                    $application->created = $application->created_at->toFormattedDateString();
                    $application->count_five = 5 - $current->diffInHours($application->check_date);
                } elseif ($application->id_status == 11) {

                    $end_date = WorkCalendar::create($application->check_date);
                    $end_date->addWorkDays($application->time);

                    $start = WorkCalendar::create($application->check_date);
                    $application->max_hour = $start->diffInHours($end_date);
                    $application->count_hour =  $application->max_hour - $current->diffInHours($start);
                    $application->created = $application->created_at->toFormattedDateString();
                    $application->count_five = 5 - $current->diffInHours($application->check_date);
                } elseif ($application->time != 0) {
                    if (!$application->day) {
                        $end_date = WorkCalendar::create($application->check_date);
                        $end_date->addWorkDays($application->time);

                        $start = WorkCalendar::create($application->check_date);
                        $application->max_hour = $start->diffInHours($end_date);
                        $application->count_hour =  $application->max_hour - $current->diffInHours($start);
                    } else {
                        $end_date = WorkCalendar::create($application->check_date);
                        $end_date->addWorkDays($application->time);

                        $start = WorkCalendar::create($application->check_date);
                        $application->max_hour = $start->diffInDays($end_date);
                        $application->count_hour =  $application->max_hour - $current->diffInDays($start);
                    }
                } else {
                    if ($application->id_status == 17) {

                        $start = isset($application->start_date) ? carbon::create($application->start_date) : carbon::create($application->napr_date);

                        //     $application->max_hour = $start->diffInDays($application->end_date)
                        $application->max_hour = $start->diffInDays($application->end_date);
                        $application->count_hour = $application->max_hour - $current->diffInDays($start);
                    }
                    if ($application->id_status == 13) {
                        if ($application->count_hour = $current->diffInHours($application->view_date) < 24) {

                            $start = carbon::create($application->check_date);

                            //     $application->max_hour = $start->diffInDays($application->end_date)
                            $application->max_hour = $start->diffInHours($application->view_date);
                            $application->count_hour = $application->max_hour - $current->diffInHours($start);
                            $application->day = 0;
                        } else {
                            $start = carbon::create($application->check_date);

                            //     $application->max_hour = $start->diffInDays($application->end_date)
                            $application->max_hour = $start->diffInDays($application->view_date);
                            $application->count_hour = $application->max_hour - $current->diffInDays($start);
                        }
                        // dd($application);
                    }
                    if ($application->id_status == 15) {
                        $start = carbon::create($application->check_date);;

                        //     $application->max_hour = $start->diffInDays($application->end_date)
                        $application->max_hour = $start->diffInDays($application->sp_date);
                        $application->count_hour = $application->max_hour - $current->diffInDays($start);
                        // dd($start, $application);
                    }
                }
                if ($application->ic_answer_date != NULL) {
                    $end_date = WorkCalendar::create($application->ic_answer_date);

                    $start = WorkCalendar::create($application->ic_answer_date);
                } else {
                    $end_date = WorkCalendar::now();

                    $start = WorkCalendar::now();
                }

                $end_date->addWorkDays($application->fin_time);

                $application->max_hour_f = $start->diffInHours($end_date);
                $application->count_hour_f =  $application->max_hour_f - $current->diffInHours($start);


                // if ($application->id_status < 11) {
                //     $application->count_hour = 48 - $current->diffInHours($application->created_at);
                //     $application->max_hour = 48;
                // }
                // if ($application->id_status == 11) {
                //     $application->count_hour = 168 - $current->diffInHours($application->check_date);
                //     $application->max_hour = 168;
                // }
                // if ($application->id_status == 19) {
                //     $application->count_hour = 72 - $current->diffInHours($application->check_date);
                //     $application->max_hour = 72;
                // }
                // if ($application->id_status == 22) {
                //     $application->count_hour = 42 - $current->diffInDays($application->check_date);
                //     $application->max_hour = 42;
                // }
                // if ($application->id_status > 10 && $application->id_status != 18 && $application->id_status != 21) {
                //     $application->count_hour = $current->diffInDays($application->end_date);

                //     $start = isset($application->start_date) ? carbon::create($application->start_date) : carbon::create($application->napr_date);

                //     $application->max_hour = $start->diffInDays($application->end_date);
                // }
                // dd($application);
            }
            // $doc = attachment::where('id_application', '=', $application->id)->where('id_status', '=', 3)->first();
            // if (isset($doc->id_status))
            //     $application->doc = $doc->id_status;
            // else
            //     $application->doc = 0;
        }
        // foreach ($applications as $application) {
        // if (isset($application->ic_answer_date)) {
        //     $application->count_hour = 48 - $current->diffInHours($application->ic_answer_date);
        //     $application->max_hour = 48;
        //     $application->StatusName = "Ожидается ответ СК";
        //     $application->count_five = 11;
        // } else


        // $doc = attachment::where('id_application', '=', $application->id)->where('id_status', '=', 3)->first();
        // if (isset($doc->id_status))
        //     $application->doc = $doc->id_status;
        // else
        //     $application->doc = 0;
        // }
        // $applications = $this->configure($applications);
        session(['rout' => '']);

        $city = city::all();
        return view('applications', ['applications' => $applications, 'city' => $city, 'filter' => user_filter::where('id_user', '=', auth::user()->id)->get()],);
    }

    // public function lawyers()
    // {


    //     $applications = Application
    //         ->where('id_status', '=', '26')
    //         ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
    //         ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

    //         ->leftjoin('services', 'applications.id_service', '=', 'services.id')
    //         ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
    //         ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
    //         ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
    //
    //             ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
    //  ->with('status_chats', function ($query) {
    //                     $query->where('id_user', '=', auth::user()->id)
    //                         ->where('is_readed', '=', '0');
    //                 })
    //                 ->with('status_applications', function ($query) {
    //                     $query->where('id_user', '=', auth::user()->id)
    //                         ->where('is_readed', '=', '0');
    //                 })
    //                 ->with('status_messages', function ($query) {
    //                     $query->where('id_user', '=', auth::user()->id)
    //                         ->where('is_readed', '=', '0');
    //                 })
    //                 ->with('spareparts')
    //                 ->with('works')
    //                 ->with('estimate')
    //         ->select(
    //             'applications.*',
    //             'insurance_companies.name',
    //             'insurance_companies.insurance_companies_theme',
    //             'services.service_name',
    //             'services.services_theme',
    //             'lawyers.lawyer_name',
    //             'lawyers.color as lawyers_color',
    //             'application_statuses.StatusName',
    //             'application_statuses.status_theme',
    //             'application_statuses.current_group',
    //             'our_companies.our_name',
    //             'users.name as MRK_name',
    //             'cities.name as city'
    //         )->get();
    //     // dd($applications);

    //     session(['rout' => 'lawyers']);
    //     $current = carbon::now();
    //     foreach ($applications as $application) {
    //         $application->count_hour = 48 - $current->diffInHours($application->created_at);
    //         $application->max_hour = 48;
    //         $doc = attachment::where('id_application', '=', $application->id)->where('id_status', '=', 2)->first();
    //         if (isset($doc->id_status))
    //             $application->doc = $doc->id_status;
    //         else
    //             $application->doc = 0;
    //     }
    //     $applications = $this->configure($applications);
    //     return view('applications', ['applications' => $applications],);
    // }

    public function archive()
    {

        $applications = application::where('applications.id_status', '=', '25')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
            ->leftjoin('attachments', function ($join) {
                $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
            })
            ->leftjoin('services', 'applications.id_service', '=', 'services.id')
            ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
            ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')

            ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
            ->with('status_chats', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('status_applications', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('status_messages', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('spareparts')
            ->with('works')
            ->with('estimate');
        $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

        if (isset($user_filters[0]->id_city)) {
            $applications->where(function ($query) use ($user_filters) {
                $query->where('id_city', '=', $user_filters[0]->id_city);
                for ($i = 1; $i < $user_filters->count(); $i++) {
                    $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                }
                if ($user_filters->count() == (city::all()->count()))
                    $query->orWhere('id_city', '=', 0);
            });
        }
        $applications = $applications->select(
            'applications.*',
            'insurance_companies.name',
            'insurance_companies.insurance_companies_theme',
            'services.service_name',
            'services.services_theme',
            'lawyers.lawyer_name',
            'lawyers.color as lawyers_color',
            'application_statuses.StatusName',
            'application_statuses.status_theme',
            'application_statuses.current_group',
            'our_companies.our_name',
            'users.name as MRK_name',
            'cities.name as city',
            'attachments.id_status as doc'
        )->orderBy('applications.id', 'desc')
            ->paginate(300);
        // dd($applications);

        session(['rout' => 'archive']);

        $current = carbon::now();
        foreach ($applications as $application) {
            $application->count_hour = 1;
            $application->max_hour = 1000;
        }
        $applications = $this->configure($applications);
        $city = city::all();
        return view('applications', ['applications' => $applications, 'city' => $city, 'filter' => user_filter::where('id_user', '=', auth::user()->id)->get()],);
    }

    public function temp()
    {
        $applications = application::where('applications.id_status', '=', '27')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->leftjoin('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

            ->leftjoin('services', 'applications.id_service', '=', 'services.id')
            ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
            ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
            ->leftjoin('attachments', function ($join) {
                $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
            })
            ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
            ->with('status_chats', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('status_applications', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('status_messages', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('spareparts')
            ->with('works')
            ->with('estimate');
        $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

        if (isset($user_filters[0]->id_city)) {
            $applications->where(function ($query) use ($user_filters) {
                $query->where('id_city', '=', $user_filters[0]->id_city);
                for ($i = 1; $i < $user_filters->count(); $i++) {
                    $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                }
                if ($user_filters->count() == (city::all()->count()))
                    $query->orWhere('id_city', '=', 0);
            });
        }
        $applications = $applications->select(
            'applications.*',
            'insurance_companies.name',
            'insurance_companies.insurance_companies_theme',
            'services.service_name',
            'services.services_theme',
            'lawyers.lawyer_name',
            'lawyers.color as lawyers_color',
            'application_statuses.StatusName',
            'application_statuses.status_theme',
            'application_statuses.current_group',
            'our_companies.our_name',
            'users.name as MRK_name',
            'cities.name as city',
            'attachments.id_status as doc'
        )->orderBy('applications.id', 'desc')
            ->paginate(300);
        // dd($applications);

        session(['rout' => 'temp']);

        $current = carbon::now();
        foreach ($applications as $application) {
            $application->count_hour = 1;
            $application->max_hour = 1000;
        }
        // $applications = $this->configure($applications);
        $city = city::all();
        return view('applications', ['applications' => $applications, 'city' => $city, 'filter' => user_filter::where('id_user', '=', auth::user()->id)->get()],);
    }
    public function priem()
    {
        $applications = application::where('applications.id_status', '=', '28')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->leftjoin('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

            ->leftjoin('services', 'applications.id_service', '=', 'services.id')
            ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
            ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
            ->leftjoin('attachments', function ($join) {
                $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
            })
            ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
            ->with('status_chats', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('status_applications', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('status_messages', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('spareparts')
            ->with('works')
            ->with('estimate');
        $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

        if (isset($user_filters[0]->id_city)) {
            $applications->where(function ($query) use ($user_filters) {
                $query->where('id_city', '=', $user_filters[0]->id_city);
                for ($i = 1; $i < $user_filters->count(); $i++) {
                    $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                }
                if ($user_filters->count() == (city::all()->count()))
                    $query->orWhere('id_city', '=', 0);
            });
        }
        $applications = $applications->select(
            'applications.*',
            'insurance_companies.name',
            'insurance_companies.insurance_companies_theme',
            'services.service_name',
            'services.services_theme',
            'lawyers.lawyer_name',
            'lawyers.color as lawyers_color',
            'application_statuses.StatusName',
            'application_statuses.status_theme',
            'application_statuses.current_group',
            'our_companies.our_name',
            'users.name as MRK_name',
            'cities.name as city',
            'attachments.id_status as doc'
        )->orderBy('applications.id', 'desc')
            ->paginate(300);
        // dd($applications);

        session(['rout' => 'priem']);

        $current = carbon::now();
        foreach ($applications as $application) {
            $application->count_hour = 1;
            $application->max_hour = 1000;
        }
        $applications = $this->configure($applications);
        $city = city::all();
        return view('applications', ['applications' => $applications, 'city' => $city, 'filter' => user_filter::where('id_user', '=', auth::user()->id)->get()],);
    }

    public function directions(Request $request)
    {
        $only_me = 0;
        $only_me = $request->get('only_me');

        if (Gate::allows('administrator')) {
            $applications = application::where(function ($query) {
                $query
                    ->where('applications.id_status', '>', '11')
                    ->where('applications.id_status', '<', '25');
            })

                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('finance_statuses', 'finance_statuses.id', '=', 'applications.finance_statuses_id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('attachments', function ($join) {
                    $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
                })
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
                ->with('status_chats', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_applications', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_messages', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('spareparts')
                ->with('works')
                ->with('estimate');
            $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

            if (isset($user_filters[0]->id_city)) {
                $applications->where(function ($query) use ($user_filters) {
                    $query->where('id_city', '=', $user_filters[0]->id_city);
                    for ($i = 1; $i < $user_filters->count(); $i++) {
                        $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                    }
                    if ($user_filters->count() == (city::all()->count()))
                        $query->orWhere('id_city', '=', 0);
                });
            }
            $applications = $applications->select(
                'applications.*',
                'finance_statuses.FinStatusName',
                'finance_statuses.fin_time',
                'finance_statuses.fin_day',
                'finance_statuses.FinStatusTheme',
                'insurance_companies.name',
                'insurance_companies.insurance_companies_theme',
                'services.service_name',
                'services.services_theme',
                'lawyers.lawyer_name',
                'lawyers.color as lawyers_color',
                'application_statuses.StatusName',
                'application_statuses.status_theme',
                'application_statuses.current_group',
                'application_statuses.time',
                'application_statuses.day',
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city',
                'attachments.id_status as doc'
            )
                ->orderBy('applications.id', 'desc')
                ->paginate(300);
        } elseif (Gate::allows('admin') || Gate::allows('km') || Gate::allows('buh')) {


            $applications = application::where(function ($query) {
                $query
                    ->where('applications.id_status', '>', '11')
                    ->where('applications.id_status', '<', '25');
            })
                ->join('user_roles', function ($join) {
                    $join->where(function ($query) {
                        $query
                            ->where('user_roles.id_group', '=', 2)
                            ->orWhere('user_roles.id_group', '=', 8)
                            ->orWhere('user_roles.id_group', '=', 3);
                    })->where('user_roles.id_user', '=', Auth::user()->id);
                })
                ->join('role_companies', function ($join) {
                    $join->on('role_companies.id_company', '=', 'applications.our_company')->on('role_companies.id_role', '=', 'user_roles.id');
                })
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

                ->join('finance_statuses', 'finance_statuses.id', '=', 'applications.finance_statuses_id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('attachments', function ($join) {
                    $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
                })
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
                ->with('status_chats', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_applications', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_messages', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('spareparts')
                ->with('works')
                ->with('estimate');
            $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

            if (isset($user_filters[0]->id_city)) {
                $applications->where(function ($query) use ($user_filters) {
                    $query->where('id_city', '=', $user_filters[0]->id_city);
                    for ($i = 1; $i < $user_filters->count(); $i++) {
                        $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                    }
                    if ($user_filters->count() == (city::all()->count()))
                        $query->orWhere('id_city', '=', 0);
                });
            }
            $applications = $applications->select(
                'applications.*',
                'finance_statuses.FinStatusName',
                'finance_statuses.fin_time',
                'finance_statuses.fin_day',
                'finance_statuses.FinStatusTheme',
                'insurance_companies.name',
                'insurance_companies.insurance_companies_theme',
                'services.service_name',
                'services.services_theme',
                'lawyers.lawyer_name',
                'lawyers.color as lawyers_color',
                'application_statuses.StatusName',
                'application_statuses.status_theme',
                'application_statuses.current_group',
                'application_statuses.time',
                'application_statuses.day',
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city',
                'attachments.id_status as doc'
            )
                ->orderBy('applications.id', 'desc')
                ->paginate(300);
        } elseif ($only_me) {


            $applications = application::where(function ($query) {
                $query
                    ->where('applications.id_status', '>', '11')
                    ->where('applications.id_status', '<', '25');
            })
                ->join('user_roles', function ($join) {
                    $join->where(function ($query) {
                        $query
                            ->where('user_roles.id_group', '=', 5);
                    })->where('user_roles.id_user', '=', Auth::user()->id);
                })
                ->join('role_companies', function ($join) {
                    $join->on('role_companies.id_company', '=', 'applications.our_company')->on('role_companies.id_role', '=', 'user_roles.id');
                })
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('finance_statuses', 'finance_statuses.id', '=', 'applications.finance_statuses_id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('attachments', function ($join) {
                    $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
                })
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
                ->with('status_chats', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_applications', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_messages', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('spareparts')
                ->with('works')
                ->with('estimate');
            $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

            if (isset($user_filters[0]->id_city)) {
                $applications->where(function ($query) use ($user_filters) {
                    $query->where('id_city', '=', $user_filters[0]->id_city);
                    for ($i = 1; $i < $user_filters->count(); $i++) {
                        $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                    }
                    if ($user_filters->count() == (city::all()->count()))
                        $query->orWhere('id_city', '=', 0);
                });
            }
            $applications = $applications->select(
                'applications.*',
                'finance_statuses.FinStatusName',
                'finance_statuses.fin_time',
                'finance_statuses.fin_day',
                'finance_statuses.FinStatusTheme',
                'insurance_companies.name',
                'insurance_companies.insurance_companies_theme',
                'services.service_name',
                'services.services_theme',
                'lawyers.lawyer_name',
                'lawyers.color as lawyers_color',
                'application_statuses.StatusName',
                'application_statuses.status_theme',
                'application_statuses.current_group',
                'application_statuses.time',
                'application_statuses.day',
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city',
                'attachments.id_status as doc'
            )
                ->orderBy('applications.id', 'desc')
                ->paginate(300);
        } elseif (Gate::allows('service')) {
            $applications = application::where(function ($query) {
                $query
                    ->where('applications.id_status', '>', '11')
                    ->where('applications.id_status', '<', '25');
            })
                ->where('applications.id_service', '=', service::where('id_user', '=', Auth::user()->id)->first()->id)
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('finance_statuses', 'finance_statuses.id', '=', 'applications.finance_statuses_id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('attachments', function ($join) {
                    $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
                })
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
                ->with('status_chats', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_applications', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_messages', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('spareparts')
                ->with('works')
                ->with('estimate');
            $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

            if (isset($user_filters[0]->id_city)) {
                $applications->where(function ($query) use ($user_filters) {
                    $query->where('id_city', '=', $user_filters[0]->id_city);
                    for ($i = 1; $i < $user_filters->count(); $i++) {
                        $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                    }
                    if ($user_filters->count() == (city::all()->count()))
                        $query->orWhere('id_city', '=', 0);
                });
            }
            $applications = $applications->select(
                'applications.*',
                'finance_statuses.FinStatusName',
                'finance_statuses.fin_time',
                'finance_statuses.fin_day',
                'finance_statuses.FinStatusTheme',
                'insurance_companies.name',
                'insurance_companies.insurance_companies_theme',
                'services.service_name',
                'services.services_theme',
                'lawyers.lawyer_name',
                'lawyers.color as lawyers_color',
                'application_statuses.StatusName',
                'application_statuses.status_theme',
                'application_statuses.current_group',
                'application_statuses.time',
                'application_statuses.day',
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city',
                'attachments.id_status as doc'
            )->orderBy('applications.id', 'desc')
                ->paginate(300);
        } elseif (Gate::allows('mrk')) {
            $applications = application::where(function ($query) {
                $query

                    ->where('applications.id_status', '>', '11')
                    ->where('applications.id_status', '<', '25');
            })
                ->where('applications.id_mrk', '=', Auth::user()->id)
                ->join('user_roles', function ($join) {
                    $join
                        ->where('user_roles.id_group', '=', 5)
                        ->where('user_roles.id_user', '=', Auth::user()->id);
                })
                ->join('role_companies', function ($join) {
                    $join->on('role_companies.id_company', '=', 'applications.our_company')->on('role_companies.id_role', '=', 'user_roles.id');
                })
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('finance_statuses', 'finance_statuses.id', '=', 'applications.finance_statuses_id')

                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('attachments', function ($join) {
                    $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
                })
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
                ->with('status_chats', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_applications', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_messages', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('spareparts')
                ->with('works')
                ->with('estimate');
            $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

            if (isset($user_filters[0]->id_city)) {
                $applications->where(function ($query) use ($user_filters) {
                    $query->where('id_city', '=', $user_filters[0]->id_city);
                    for ($i = 1; $i < $user_filters->count(); $i++) {
                        $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                    }
                    if ($user_filters->count() == (city::all()->count()))
                        $query->orWhere('id_city', '=', 0);
                });
            }
            $applications = $applications->select(
                'applications.*',
                'finance_statuses.FinStatusName',
                'finance_statuses.fin_time',
                'finance_statuses.fin_day',
                'finance_statuses.FinStatusTheme',
                'insurance_companies.name',
                'insurance_companies.insurance_companies_theme',
                'services.service_name',
                'services.services_theme',
                'lawyers.lawyer_name',
                'lawyers.color as lawyers_color',
                'application_statuses.StatusName',
                'application_statuses.status_theme',
                'application_statuses.current_group',
                'application_statuses.time',
                'application_statuses.day',
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city',
                'attachments.id_status as doc'
            )->orderBy('applications.id', 'desc')
                ->paginate(300);
            // dd($applications);
        } else {
            $applications = application::where(function ($query) {
                $query

                    ->where('applications.id_status', '>', '11')
                    ->where('applications.id_status', '<', '25');
            })
                ->where('applications.id_spm', '=', Auth::user()->id)

                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('finance_statuses', 'finance_statuses.id', '=', 'applications.finance_statuses_id')

                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('attachments', function ($join) {
                    $join->on('attachments.id_application', '=', 'applications.id')->where('attachments.id_status', '=', '3');
                })
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
                ->with('status_chats', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_applications', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('status_messages', function ($query) {
                    $query->where('id_user', '=', auth::user()->id)
                        ->where('is_readed', '=', '0');
                })
                ->with('spareparts')
                ->with('works')
                ->with('estimate');
            $user_filters = user_filter::where('id_user', '=', auth::user()->id)->get();

            if (isset($user_filters[0]->id_city)) {
                $applications->where(function ($query) use ($user_filters) {
                    $query->where('id_city', '=', $user_filters[0]->id_city);
                    for ($i = 1; $i < $user_filters->count(); $i++) {
                        $query->orWhere('id_city', '=', $user_filters[$i]->id_city);
                    }
                    if ($user_filters->count() == (city::all()->count()))
                        $query->orWhere('id_city', '=', 0);
                });
            }
            $applications = $applications->select(
                'applications.*',
                'finance_statuses.FinStatusName',
                'finance_statuses.fin_time',
                'finance_statuses.fin_day',
                'finance_statuses.FinStatusTheme',
                'insurance_companies.name',
                'insurance_companies.insurance_companies_theme',
                'services.service_name',
                'services.services_theme',
                'lawyers.lawyer_name',
                'lawyers.color as lawyers_color',
                'application_statuses.StatusName',
                'application_statuses.status_theme',
                'application_statuses.current_group',
                'application_statuses.time',
                'application_statuses.day',
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city',
                'attachments.id_status as doc'
            )->orderBy('applications.id', 'desc')
                ->paginate(300);
        }
        $applications = $this->configure($applications);
        session(['rout' => 'directions']);
        //echo (session('rout'));



        $current = carbon::now();
        foreach ($applications as $application) { {

                // if (isset($application->check_date)) {
                //     $end_date = WorkCalendar::create($application->check_date);
                //     $end_date->addWorkDays(1);
                //     dd($end_date, $application->id);
                // }
                if ($application->time != 0) {
                    if (!$application->day) {
                        $end_date = WorkCalendar::create($application->check_date);
                        $end_date->addWorkDays($application->time);

                        $start = WorkCalendar::create($application->check_date);
                        $application->max_hour = $start->diffInHours($end_date);
                        $application->count_hour =  $application->max_hour - $current->diffInHours($start);
                    } else {
                        $end_date = WorkCalendar::create($application->check_date);
                        $end_date->addWorkDays($application->time);

                        $start = WorkCalendar::create($application->check_date);
                        $application->max_hour = $start->diffInDays($end_date);
                        $application->count_hour =  $application->max_hour - $current->diffInDays($start);
                    }
                } else {
                    if ($application->id_status == 17) {

                        $start = isset($application->start_date) ? carbon::create($application->start_date) : carbon::create($application->napr_date);

                        //     $application->max_hour = $start->diffInDays($application->end_date)
                        $application->max_hour = $start->diffInDays($application->end_date);
                        $application->count_hour = $application->max_hour - $current->diffInDays($start);
                    }
                    if ($application->id_status == 13) {
                        if ($application->count_hour = $current->diffInHours($application->view_date) < 24) {

                            $start = carbon::create($application->check_date);

                            //     $application->max_hour = $start->diffInDays($application->end_date)
                            $application->max_hour = $start->diffInHours($application->view_date);
                            $application->count_hour = $application->max_hour - $current->diffInHours($start);
                            $application->day = 0;
                        } else {
                            $start = carbon::create($application->check_date);

                            //     $application->max_hour = $start->diffInDays($application->end_date)
                            $application->max_hour = $start->diffInDays($application->view_date);
                            $application->count_hour = $application->max_hour - $current->diffInDays($start);
                        }
                        // dd($application);
                    }
                    if ($application->id_status == 15) {
                        $start = carbon::create($application->check_date);;

                        //     $application->max_hour = $start->diffInDays($application->end_date)
                        $application->max_hour = $start->diffInDays($application->sp_date);
                        $application->count_hour = $application->max_hour - $current->diffInDays($start);
                        // dd($start, $application);
                    }
                }
                if ($application->ic_answer_date != NULL) {
                    $end_date = WorkCalendar::create($application->ic_answer_date);

                    $start = WorkCalendar::create($application->ic_answer_date);
                } else {
                    $end_date = WorkCalendar::now();

                    $start = WorkCalendar::now();
                }

                $end_date->addWorkDays($application->fin_time);

                $application->max_hour_f = $start->diffInHours($end_date);
                $application->count_hour_f =  $application->max_hour_f - $current->diffInHours($start);


                // if ($application->id_status < 11) {
                //     $application->count_hour = 48 - $current->diffInHours($application->created_at);
                //     $application->max_hour = 48;
                // }
                // if ($application->id_status == 11) {
                //     $application->count_hour = 168 - $current->diffInHours($application->check_date);
                //     $application->max_hour = 168;
                // }
                // if ($application->id_status == 19) {
                //     $application->count_hour = 72 - $current->diffInHours($application->check_date);
                //     $application->max_hour = 72;
                // }
                // if ($application->id_status == 22) {
                //     $application->count_hour = 42 - $current->diffInDays($application->check_date);
                //     $application->max_hour = 42;
                // }
                // if ($application->id_status > 10 && $application->id_status != 18 && $application->id_status != 21) {
                //     $application->count_hour = $current->diffInDays($application->end_date);

                //     $start = isset($application->start_date) ? carbon::create($application->start_date) : carbon::create($application->napr_date);

                //     $application->max_hour = $start->diffInDays($application->end_date);
                // }
                // dd($application);
            }
        }
        $city = city::all();
        // dd($applications);
        return view('applications', ['applications' => $applications, 'only_me' => $only_me, 'city' => $city, 'filter' => user_filter::where('id_user', '=', auth::user()->id)->get()],);
    }
    // public function waiting(Request $request)
    // {
    //     $only_me = 0;
    //     $only_me = $request->get('only_me');

    //     if (Gate::allows('admin') || $only_me) {

    //         $applications = application::where(function ($query) {
    //             $query->where('id_status', '>', '7')
    //                 ->where('id_status', '<', '12');
    //         })

    //             ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
    //             ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
    //             ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
    //             ->leftjoin('services', 'applications.id_service', '=', 'services.id')
    //             ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
    //             ->select(
    //                 'applications.*',
    //                 'insurance_companies.name',
    //                 'insurance_companies.insurance_companies_theme',
    //                 'services.service_name',
    //                 'services.services_theme',
    //                 'lawyers.lawyer_name',
    //                 'lawyers.color as lawyers_color',
    //                 'application_statuses.StatusName',
    //                 'application_statuses.status_theme',
    //                 'application_statuses.current_group',
    //                 'our_companies.our_name'
    //             )->get();
    //     } else if (Auth::user()->GroupId == 9) {
    //         $applications = application::where(function ($query) {
    //             $query->where('id_status', '>', '7')
    //                 ->where('id_status', '<', '12');
    //         })
    //             ->where(function ($query) {
    //                 $query->Where('applications.id_service', '=', service::where('id_user', '=', Auth::user()->id)->first()->id);
    //             })
    //             ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
    //             ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
    //             ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
    //             ->leftjoin('services', 'applications.id_service', '=', 'services.id')
    //             ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
    //             ->select(
    //                 'applications.*',
    //                 'insurance_companies.name',
    //                 'insurance_companies.insurance_companies_theme',
    //                 'services.service_name',
    //                 'services.services_theme',
    //                 'lawyers.lawyer_name',
    //                 'lawyers.color as lawyers_color',
    //                 'application_statuses.StatusName',
    //                 'application_statuses.status_theme',
    //                 'application_statuses.current_group',
    //                 'our_companies.our_name'
    //             )->get();
    //     } else {
    //         $applications = application::where(function ($query) {
    //             $query->where('id_status', '>', '7')
    //                 ->where('id_status', '<', '12');
    //         })
    //             ->where(function ($query) {
    //                 $query->where('applications.id_MRK', '=', Auth::user()->id)
    //                     ->orWhere('applications.id_spm', '=', Auth::user()->id);
    //             })
    //             ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
    //             ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
    //             ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
    //             ->leftjoin('services', 'applications.id_service', '=', 'services.id')
    //             ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
    //             ->select(
    //                 'applications.*',
    //                 'insurance_companies.name',
    //                 'insurance_companies.insurance_companies_theme',
    //                 'services.service_name',
    //                 'services.services_theme',
    //                 'lawyers.lawyer_name',
    //                 'lawyers.color as lawyers_color',
    //                 'application_statuses.StatusName',
    //                 'application_statuses.status_theme',
    //                 'application_statuses.current_group',
    //                 'our_companies.our_name'
    //             )->get();

    //         // dd($applications);
    //     }
    //     $applications = $this->configure($applications);
    //     session(['rout' => 'waiting']);
    //     //echo (session('rout'));



    //     $current = carbon::now();
    //     foreach ($applications as $application) {
    //         if ($application->id_status < 11) {
    //             $application->count_hour = 48 - $current->diffInHours($application->created_at);
    //             $application->max_hour = 48;
    //         }
    //         if ($application->id_status == 11) {
    //             $application->count_hour = 168 - $current->diffInHours($application->check_date);
    //             $application->max_hour = 168;
    //         }
    //         if ($application->id_status == 19) {
    //             $application->count_hour = 72 - $current->diffInHours($application->check_date);
    //             $application->max_hour = 72;
    //         }
    //         if ($application->id_status == 22) {
    //             $application->count_hour = 42 - $current->diffInDays($application->check_date);
    //             $application->max_hour = 42;
    //         }
    //         if ($application->id_status > 10 && $application->id_status != 18 && $application->id_status != 21) {
    //             $application->count_hour = $current->diffInDays($application->end_date);
    //             $start = carbon::create($application->napr_date);
    //             $application->max_hour = $start->diffInDays($application->end_date);
    //         }
    //     }
    //     return view('applications', ['applications' => $applications, 'only_me' => $only_me],);
    // }






    public function application(Request $request, $id)
    {
        // $app = application::find($id);
        // $today = WorkCalendar::now();
        // $today->addWorkDays(10);
        // $app->view_date = $today;
        // $app->save();
        // $app = application::find($id);
        // dd($app);

        session(['current' => $id]);
        $hide_menu = $request->get('hide_menu');
        // echo (session('rout'));
        $application = application::join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')

            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
            ->leftjoin('services', 'applications.id_service', '=', 'services.id')
            ->leftjoin('users as spms', 'applications.id_spm', '=', 'spms.id')
            ->leftjoin('users as mrks', 'applications.id_mrk', '=', 'mrks.id')

            ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
            ->with('status_chats', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('status_applications', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('status_messages', function ($query) {
                $query->where('id_user', '=', auth::user()->id)
                    ->where('is_readed', '=', '0');
            })
            ->with('spareparts')
            ->with('works')
            ->with('estimate')
            ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
            ->select(
                'applications.*',
                'insurance_companies.name',
                'application_statuses.StatusName',
                'services.service_name',
                'spms.name as spm',
                'mrks.name as mrk',
                'cities.name as city',
                'lawyers.lawyer_name'
            )
            ->where('applications.id', '=', $id)
            ->first();
        //   dd($application);
        try {
            $mrk = DB::table('users')
                ->where('id', '=', $application->id_MRK)
                ->first();
        } catch (\Exception $errors) {
            return redirect()->route('applications');
        }

        $empty = 0;
        foreach ($application as $key => $value) {
            if ($key != 'ClientPasport')
                if ($value == null) {
                    $empty = 1;
                }
        }
        $status_application = status_application::where('id_user', '=', Auth::user()->id)->where('id_application', '=', $application->id)
            ->where('is_readed', '=', '0')->get();
        foreach ($status_application as $status) {
            $status->is_readed = 1;
            $status->save();
        }
        $notify = notify::where('id_application', '=', $id)
            ->where('id_user', '=', Auth::user()->id)
            ->where('id_text', '=', '3')->get()->all();
        foreach ($notify as $notif) {
            if ($notif->id_message) {
                $user = User::where('id', '=', Auth::user()->id)->first();
                $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                $ch = $new_chat->deleteMessage($notif->id_message)->send();
            }
            $notif->delete();
        }
        if ($application->id_status < 12 && $application->id_status != 6 && $application->id_status != 7)
            session(['rout' => 'applications']);
        if ($application->id_status == 6 || $application->id_status == 7)
            session(['rout' => 'failures']);
        if ($application->id_status > 11 && $application->id_status < 25)
            session(['rout' => 'directions']);
        if ($application->id_status == 25)
            session(['rout' => 'archive']);
        if ($application->id_status == 27)
            session(['rout' => 'temp']);
        if ($application->id_status == 28)
            session(['rout' => 'priem']);


        return view('application.index', ['application' => $application, 'id' => $id, 'empty' => $empty, 'hide_menu' => $hide_menu]);
    }
    //
    public function edit($id)
    {



        $services = DB::table('services')->get();
        $lawyers = DB::table('lawyers')->get();

        $InsuranceCompany = new InsuranceCompany;
        $application = application::join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->select('applications.*', 'insurance_companies.name')
            ->where('applications.id', '=', $id)
            ->first();
        $spms = DB::table('users')

            ->join('user_roles', function ($join) {
                $join
                    ->on('user_roles.id_user', '=', 'users.id')
                    ->where('user_roles.id_group', '=', 6);
            })
            ->join('role_companies', function ($join) use ($application) {
                $join->on('role_companies.id_role', '=', 'user_roles.id')->where('role_companies.id_company', '=', $application->our_company);
            })
            ->select('users.*')
            ->get();
        $mrks = DB::table('users')
            ->join('user_roles', function ($join) {
                $join
                    ->on('user_roles.id_user', '=', 'users.id')
                    ->where('user_roles.id_group', '=', 5);
            })
            ->join('role_companies', function ($join) use ($application) {
                $join->where('role_companies.id_company', '=', $application->our_company)->on('role_companies.id_role', '=', 'user_roles.id');
            })

            ->select('users.*', 'users.id as user_id')
            ->get();
        if (Auth::user()->id == 2) {
            $cities = DB::table('cities')
                ->get();
        } else {
            $cities = DB::table('cities')
                ->join('city_companies', 'cities.id', '=', 'city_companies.id_city')
                ->where('city_companies.id_company', '=', $application->our_company)
                ->select('cities.*', 'city_companies.id_city as city_companies_id')
                ->get();
        }
        // dd($cities);
        return view(
            'application.edit',
            [
                'application' => $application,
                'InsuranceCompany' => $InsuranceCompany->all(),

                'mrks' => $mrks,
                'services' => $services,
                'spms' => $spms,
                'lawyers' => $lawyers,
                'cities' => $cities,
                'id' => $id
            ]
        );
    }
    public function update(Request $request, $id)
    {
        $application = application::findOrFail($id);
        $history = new history;
        $history->id_application = $id;
        $history->id_user = Auth::user()->id;
        if ($request->ur) {
            $application->id_MRK = null;
            $application->id_spm = null;
            $application->id_status = 26;
            $history->id_status = 26;
            $history->save();
        } else {
            if ($application->id_status < 2 || $application->id_status == 25) {
                $application->id_status = 2;
                $history->id_status = 2;
                $history->save();
            }
            $data = $this->validate($request, [
                'id_insuranceCompany' => 'required',
                'numberInsuranceCompanyApplication' => 'required',
                'CarModel' => 'nullable|string',
                'gnz' => 'nullable|string',
                'vin' => 'nullable|string',
                'ClientName' => 'nullable|string',
                'ClientPhone' => 'nullable|string',
                'id_MRK' => 'nullable|integer',
                'id_spm' => 'nullable|integer',
                'id_service' => 'nullable|integer',
                'id_lawyer' => 'nullable|integer',
                'mileage' => 'nullable|integer',
                'vehicle_passport' => 'nullable|string',
                'Color' => 'nullable|string',
                'ClientPasport' => 'nullable|string',
                'DTPdate' => 'nullable|date',
                'ub_number' => 'nullable|string',
                'mnf_year' => 'nullable|string',
                'dogovor' => 'nullable|string',
                'def' => 'nullable|integer',
                'start_date' => 'nullable|date',
                'view_date' => 'nullable|date',
                'napr_date' => 'nullable|date',
                'sp_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'wait_sp' => 'nullable|string',
                'ClientPasport_number' => 'nullable|string',
                'ClientPasport_struct' => 'nullable|string',
                'ClientPasport_date' => 'nullable|date',
                'id_city' => 'nullable|integer',
                'pred' => 'nullable|string',
                'pred_phone' => 'nullable|string',
                'ub_type' => 'nullable',
                'gender' => 'nullable'

            ]);
            // dd($data);
            if (isset($data['start_date'])) {
                $end_date = WorkCalendar::create($data['start_date']);
                $end_date->addWorkDays(30);
                $data['end_date'] = $end_date;
            }
            if (isset($data['ClientPhone'])) {
                $phone = $data['ClientPhone'];
                $phone = preg_replace('/[^0-9]/', '', $phone);
                $start = mb_strpos($phone, '9');
                $phone = mb_substr($phone, $start);
                $phone = '+7 (' . $phone[0] . $phone[1] . $phone[2] . ') ' . $phone[3] . $phone[4] . $phone[5] . '-' . $phone[6] . $phone[7] . '-' . $phone[8] . $phone[9];
                $data['ClientPhone'] = $phone;
            }
            if (isset($data['pred_phone'])) {
                $pred_phone = $data['pred_phone'];
                $pred_phone = preg_replace('/[^0-9]/', '', $pred_phone);
                $start = mb_strpos($pred_phone, '9');
                $pred_phone = mb_substr($pred_phone, $start);
                $pred_phone = '+7 (' . $pred_phone[0] . $pred_phone[1] . $pred_phone[2] . ') ' . $pred_phone[3] . $pred_phone[4] . $pred_phone[5] . '-' . $pred_phone[6] . $pred_phone[7] . '-' . $pred_phone[8] . $pred_phone[9];
                $data['pred_phone'] = $pred_phone;
            }
            $vehicle_passport
                = $data['vehicle_passport'];
            $vehicle_passport = preg_replace('/[^0-9]/', '', $vehicle_passport);
            $vehicle_passport = substr($vehicle_passport, 0, 4) . ' ' . substr($vehicle_passport, 4);
            $data['vehicle_passport'] = $vehicle_passport;

            if (!isset($data['CarModel']))
                $data['CarModel'] = '--';
            // dd($data);
            foreach ($data as $key => $value) {
                $application->$key = $value;
            }
        }
        $application->save();
        return redirect()
            ->route('application', $application->id);
    }

    public function create()
    {
        $InsuranceCompany = new InsuranceCompany;

        $OurCompany = new OurCompany;

        // $user_role = user_role::where('id_user', '=', Auth::user()->id)->first();
        // $OurCompany = OurCompany::join('role_companies', 'role_companies.id_company', '=', 'our_companies.id')
        //     ->where('role_companies.id_role', '=', $user_role->id)->select('our_companies.*')
        //     ->get();


        return view('application.create', ['InsuranceCompany' => $InsuranceCompany->all(), 'OurCompany' => $OurCompany->all()]);
    }



    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'id_insuranceCompany' => 'required',
            'numberInsuranceCompanyApplication' => 'required',
            'CarModel' => 'required|string',
            'gnz' => 'nullable|string',
            'vin' => 'nullable|string',
            'ClientName' => 'nullable|string',
            'ClientPhone' => 'nullable|string',
            'id_MRK' => 'nullable|integer',
            'id_spm' => 'nullable|integer',
            'id_service' => 'nullable|integer',
            'id_lawyer' => 'nullable|integer',
            'mileage' => 'nullable|integer',
            'vehicle_passport' => 'nullable|string',
            'Color' => 'nullable|string',
            'ClientPassport' => 'nullable|string',
            'DTPdate' => 'nullable|date',
            'our_company' => 'nullable|string',

        ]);
        $application = new application;
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_status = 2;

        if (session('rout') == 'priem')
            $application->id_status = 28;
        else
            $application->id_status = 2;



        $phone = $data['ClientPhone'];
        if (!$phone)
            $phone = 79000000000;
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $start = mb_strpos($phone, '9');
        $phone = mb_substr($phone, $start);
        $phone = '+7 (' . $phone[0] . $phone[1] . $phone[2] . ') ' . $phone[3] . $phone[4] . $phone[5] . '-' . $phone[6] . $phone[7] . '-' . $phone[8] . $phone[9];
        $data['ClientPhone'] = $phone;

        foreach ($data as $key => $value) {
            $application->$key = $value;
        }

        $application->save();
        try {
            $estimate = estimate::find('id_application', '=', $application->id);
        } catch (\Exception $e) {
            $estimate = estimate::firstorcreate(
                ['id_application' => $application->id],
                [
                    'id_application' => $application->id,
                    'price_sp_full' => 0,
                    'price_sp_half' => 0,
                    'price_repair' => 0,
                    'price_paint' => 0,
                    'price_zip' => 0,
                    'price_work' => 0,
                    'price_repair_full' => 0,
                    'price_repair_half' => 0
                ]
            );
        }
        $history->id_application = $application->id;
        $dir = 'storage/files/' . $application->id;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $history->save();
        return redirect()
            ->route('application', $application->id);
    }
    public function destroy($id)
    {
        try {
            $application = application::findOrFail($id);
            $application->delete();
        } catch (\Exception $error) {
            return redirect()->route('applications')->with('error', 'Ошибка при удалении заявки');
        }
        $notifications = notify::where('id_application', '=', $id)->get();
        foreach ($notifications as $notification) {
            $notification->delete();
        }
        $status_applications = status_application::where('id_application', '=', $id)->get();
        foreach ($status_applications as $status) {
            $status->delete();
        }
        $status_chats = status_chat::where('id_application', '=', $id)->get();
        foreach ($status_chats as $status) {
            $status->delete();
        }
        $status_messages = status_message::where('id_application', '=', $id)->get();
        foreach ($status_messages as $status) {
            $status->delete();
        }
        $attachments = attachment::where('id_application', '=', $id)->get();
        foreach ($attachments as $attachment) {
            $attachment->delete();
        }
        $chats = chat::where('id_application', '=', $id)->get();
        foreach ($chats as $chat) {
            $chat->delete();
        }
        $estimates = estimate::where('id_application', '=', $id)->get();
        foreach ($estimates as $estimate) {
            $estimate->delete();
        }
        $failures = failure::where('id_application', '=', $id)->get();
        foreach ($failures as $failure) {
            $failure->delete();
        }
        $histories =
            history::where('id_application', '=', $id)->get();
        foreach ($histories as $history) {
            $history->delete();
        }
        $messages = message::where('application_id', '=', $id)->get();
        foreach ($messages as $message) {
            $message->delete();
        }
        $spare_parts = SparePart::where('id_application', '=', $id);
        foreach ($spare_parts as $spare_part) {
            $spare_part->delete();
        }
        $troubleshootings =
            troubleshooting::where('id_application', '=', $id)->get();
        foreach ($troubleshootings as $troubleshooting) {
            $troubleshooting->delete();
        }
        $trus =
            tru::where('id_application', '=', $id)->get();
        foreach ($trus as $tru) {
            $tru->delete();
        }
        $works =
            work::where('id_application', '=', $id)->get();
        foreach ($works as $work) {
            $work->delete();
        }








        // Delete all files in directory (including subdirectories)
        $dir = 'storage/files/' . $id;
        if (is_dir($dir))
            exec('rm -fr ' . $dir);


        return redirect()
            ->route('applications');
    }
    public function history($id)
    {
        $histories = DB::table('histories')
            ->where('id_application', '=', $id)
            ->join('users', 'histories.id_user', '=', 'users.id')
            ->select('histories.*', 'users.name')
            ->get();
        foreach ($histories as $history) {
            if ($history->is_fin)
                $history->StatusName = DB::table('finance_statuses')
                    ->where('id', '=', $history->id_status)->first()->FinStatusName;
            else
                $history->StatusName = DB::table('application_statuses')
                    ->where('id', '=', $history->id_status)->first()->StatusName;
        }
        // dd($rewiew->all()) ;
        // dd($histories);
        return view('application.history', ['histories' => $histories, 'id' => $id]);
    }


    public function message($id)
    {
        $application = application::find($id);

        $sign = OurCompany::where('id', '=', $application->our_company)->first()->sign;

        $notify = notify::where('id_application', '=', $id)
            ->where('id_user', '=', Auth::user()->id)
            ->where('id_text', '=', 1)
            ->get()->all();
        foreach ($notify as $notif) {
            if ($notif->id_message) {

                // dd($notif);
                $user = User::where('id', '=', Auth::user()->id)->first();
                $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                $ch = $new_chat->deleteMessage($notif->id_message)->send();
            }
            $notif->delete();
        }

        $status_message = status_message::where('id_user', '=', Auth::user()->id)->where('id_application', '=', $id)
            ->where('is_readed', '=', '0')->get();
        foreach ($status_message as $status) {
            $status->is_readed = 1;
            $status->save();
        }

        $messages = DB::table('messages')
            ->where('messages.application_id', '=', $id)
            ->select('messages.*')
            ->orderBy('messages.id', 'desc')
            // ->leftJoin('attachments', 'attachments.message_id', '=', 'messages.id')
            // ->select('messages.*', 'attachments.file_name', 'attachments.file_size', 'attachments.location')

            ->get();
        $adresses_to = DB::table('addresses')
            ->leftjoin('messages', 'addresses.message_id', '=', 'messages.id')
            ->where('messages.application_id', '=', $id)
            ->where('addresses.type', '=', 'to')
            ->select('messages.id', 'addresses.email')
            // ->orderBy('messages.id', 'desc')
            // ->leftJoin('attachments', 'attachments.message_id', '=', 'messages.id')
            // ->select('messages.*', 'attachments.file_name', 'attachments.file_size', 'attachments.location')

            ->get()
            ->groupBy('id');
        $adresses_from = DB::table('addresses')
            ->leftjoin('messages', 'addresses.message_id', '=', 'messages.id')
            ->where('messages.application_id', '=', $id)
            ->where('addresses.type', '=', 'from')
            ->select('messages.id', 'addresses.email',)
            // ->orderBy('messages.id', 'desc')
            // ->leftJoin('attachments', 'attachments.message_id', '=', 'messages.id')
            // ->select('messages.*', 'attachments.file_name', 'attachments.file_size', 'attachments.location')

            ->get()
            ->groupBy('id');
        $attacments = DB::table('attachments')
            ->leftjoin('messages', 'attachments.message_id', '=', 'messages.id')
            ->where('messages.application_id', '=', $id)
            ->select('attachments.*')
            // ->orderBy('messages.id', 'desc')
            // ->leftJoin('attachments', 'attachments.message_id', '=', 'messages.id')
            // ->select('messages.*', 'attachments.file_name', 'attachments.file_size', 'attachments.location')

            ->get()
            ->groupBy('message_id');
        foreach ($messages as $message) {
            $message->to = [];
            if (isset($attacments[$message->id]))
                $message->attachment = $attacments[$message->id];
            else
                $message->attachment = [];

            if (isset($adresses_from[$message->id]))
                $message->from = $adresses_from[$message->id][0];
            else
                $message->from = '';


            if (isset($adresses_to[$message->id]))
                $message->to = $adresses_to[$message->id];
            else
                $message->to = [];
        }
        // dd($messages[0]->attachment[1]);
        // foreach ($messages as $message) {
        //     $message->attachment = DB::table('attachments')
        //         ->where('message_id', '=', $message->id)
        //         ->get();
        //     $message->from = DB::table('addresses')
        //         ->where('message_id', '=', $message->id)
        //         ->where('type', '=', 'from')
        //         ->first();
        //     $message->to = DB::table('addresses')
        //         ->where('message_id', '=', $message->id)
        //         ->where('type', '=', 'to')
        //         ->get();
        // }
        // dd($messages);
        // $application = application::findOrFail($id);
        $attachments = attachment::where('id_application', '=', $id)
            ->where('sended', '=', null)
            ->get()->all();
        foreach ($attachments as $attachment) {
            $attachment->ext = pathinfo($attachment->location, PATHINFO_EXTENSION);
        }
        try {
            $message_id = message::where('application_id', '=', $id)->first()->id;
            $subject =
                message::where('application_id', '=', $id)->first()->subject;
            $to_email = DB::table('addresses')
                ->where('message_id', '=', $message_id)
                ->where('type', '=', 'from')
                ->first()
                ->email;
            // echo $to;


        } catch (\Exception $e) {
            $message_id = 0;
            $subject = '';
            $to_email = '';
            // $attachments = [];
        }
        foreach ($messages as $message) {
            if (isset($message->application_id)) {
                $body = $message->body_html;
                // $body_start = mb_strpos($body, '<body');
                // $body_end = mb_strpos($body, '</body>');
                // if ($body_start) {
                //     $body = mb_substr($body, $body_start, $body_end - $body_start);
                // }
                // $body = str_replace('color', '', $body);

                // $body = str_replace('font-size:', '', $body);
                // $body = str_replace('cid:', '/storage/files/' . $message->application_id . '/', $body);
                // $body = str_replace('gif', 'gif"', $body);
                // $body = str_replace('png', 'png"', $body);
                // $body = str_replace('""', '"', $body);
                // $body = str_replace('=">', '="">', $body);
                if (strpos($body, '<blockquote')) {
                    // $body = preg_replace("~<blockquote(.*?)>(.*)</blockquote>~si", "", ' ' . $body . ' ');
                    // $body = substr($body, 0, strpos($body, '<blockquote'));
                }
                $loc = Attachment::where('message_id', '=', $message->id)
                    ->where('file_name', 'like', '%image001.png%')
                    ->first();
                $location = isset($loc->location) ? $loc->location : '';
                $body = str_replace('image001.png', $location, $body);

                $start = mb_strpos('png', $body) ? 2 : 1;
                for ($i = $start; $i < 10; $i++) {
                    $loc = Attachment::where('message_id', '=', $message->id)
                        ->where('file_name', 'like', '%image00' . $i . '.gif%')
                        ->first();
                    $location = isset($loc->location) ? $loc->location : '';
                    $body = str_replace('image00' . $i . '.gif', $location, $body);
                    // $body = str_replace('<base target="_self" href="https://e.mail.ru/">', '', $body);
                    // $body = str_replace('<base target="_self" href="https://e.mail.ru/">', '', $body);
                    // $body = str_replace('<base href="https://e.mail.ru/" target="_self">', '', $body);
                    // $body = str_replace('<base target="_self" href="https://e.mail.ru/" style="">', '', $body);

                }
                $body = preg_replace("~<base(.*?)>(.*)</base>~si", "",  $body);
                $body = preg_replace("~<base(.*?)>~si", "",  $body);
                $body = preg_replace("~<img(.*?)>~si", "",  $body);
                $message->body = $body;
            }
        }
        $application = application::where('id', '=', $id)->first();
        if ($application->id_status < 12 && $application->id_status != 6 && $application->id_status != 7)
            session(['rout' => 'applications']);
        if ($application->id_status == 6 || $application->id_status == 7)
            session(['rout' => 'failures']);
        if ($application->id_status > 11 && $application->id_status < 25)
            session(['rout' => 'directions']);
        if ($application->id_status == 25)
            session(['rout' => 'archive']);
        if ($application->id_status == 27)
            session(['rout' => 'temp']);

        return view('application.mail', [
            'messages' => $messages->all(),
            'id' => $id,
            'to_email' => $to_email,
            'attach' => $attachments,
            'subject' => $subject,
            'sign' => $sign,
            'application' => $application
        ]);
    }
    public function chat(Request $request, $id)
    {
        $selected = '';
        $mess_id = '';
        if ($request->input('selected')) {
            $selected = $request->input('selected');
            $mess_id = $request->input('mess_id');
        }
        // $status_chat = status_chat::where('id_user', '=', Auth::user()->id)->where('id_application', '=', $id)
        //     ->where('is_readed', '=', '0')->get();
        // foreach ($status_chat as $status) {
        //     $status->is_readed = 1;
        //     $status->save();
        // }
        // $notify = notify::where('id_application', '=', $id)
        //     ->where('id_user', '=', Auth::user()->id)
        //     ->where('id_text', '=', '2')->get()->all();
        // foreach ($notify as $notif) {
        //     $notif->delete();
        // }

        $users = DB::table('users')->get();
        $groups = DB::table('cities')->get();
        foreach ($groups as $group) {
            $group->members = DB::table('users')->join('user_cities', function ($join) use ($group) {
                $join
                    ->where('user_cities.id_city', '=', $group->id)
                    ->on('user_cities.id_user', '=', 'users.id');
            })->get();
        }
        // dd($groups);
        if ($request->input('only_me') || Gate::allows('service')) {
            $chats = DB::table('chats')
                ->where('id_application', '=', $id)
                ->where(function ($query) {
                    $query->Where('from', '=', Auth::user()->id);
                })
                ->leftjoin('users as userfrom', 'chats.from', '=', 'userfrom.id')

                ->leftjoin('users as userto', 'chats.to', '=', 'userto.id')
                ->select('chats.*', 'userfrom.name as from_name', 'userto.name as to_name')
                ->get();
        } else {
            $chats = DB::table('chats')
                ->where('id_application', '=', $id)
                ->leftjoin('users as userfrom', 'chats.from', '=', 'userfrom.id')
                ->leftjoin('users as userto', 'chats.to', '=', 'userto.id')
                ->select('chats.*', 'userfrom.name as from_name', 'userto.name as to_name')
                ->get();
        }
        foreach ($chats as $chat) {
            $chat->user_to = chat_rec::where('id_chat', '=', $chat->id)
                ->join('users as userto', 'chat_recs.id_to', '=', 'userto.id')
                ->select('userto.name as to_name')->get();
            $chat->time
                = Date::parse($chat->created_at)->format('j F Yг. H:i');
            $chat->attachment = chat_attachment::where('chat_id', '=', $chat->id)->get();
        }
        // dd($chats);
        // dd($rewiew->all()) ;
        return view('application.chat', [
            'chats' => $chats->all(),
            'users' => $users->all(),
            'id' => $id,
            'only_me' => $request->input('only_me'),
            'selected' => $selected,
            'mess_id' => $mess_id
        ]);
    }
    public function chat_sent(Request $request, $id)
    {
        // dd($request);
        $chat = new chat;
        $author = User::findOrFail($request->from);

        $chat->id_application = $id;
        $chat->from = $request->from;
        $chat->to = 0;
        $chat->message = $request->message;
        $chat->save();

        foreach ($request->to as $key => $to) {
            // dd($to);
            if ($to != 0) {
                $recipient = User::findOrFail($to);
                $chat_recs = new chat_rec;
                $chat_recs->id_chat = $chat->id;
                $chat_recs->id_to = $to;
                $chat_recs->save();
                $notify = new notify;
                $notify->id_user = $to;
                $notify->id_application = $id;
                $notify->id_text = 2;
                $notify->link = "/chat/";


                $status_chat = new status_chat;
                $status_chat->id_user = $to;
                $status_chat->id_application = $id;
                $status_chat->is_readed = false;
                $status_chat->save();
                $notification = DB::table('notifies')->where('notifies.id', '=', $notify->id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
                    ->select('notifies.*', 'text_notifies.text')->first();
                broadcast(new MessageSent($notification));
                if ($recipient->telegram_notify) {
                    $new_chat = TelegraphChat::where('chat_id', '=', $recipient->telegram_id)->first();
                    $ch = $new_chat->html("В чате заявки {$id} новое сообщение \n<b>{$author->name}</b>\n\n <i>{$request->message}</i>")
                        ->keyboard(
                            Keyboard::make()
                                ->buttons([Button::make("Ответить")->action('promotion')->param('app_id', "{$id}"),])
                        )
                        ->send();
                    // dd(json_decode($ch));
                    $notify->id_message = json_decode($ch)->result->message_id;
                }

                $notify->save();
            }
        }




        // $qs = new QueueSenderChat('aleksmakarov5@yandex.ru', $id, $author->name, $request->message);
        // $this->dispatch($qs);
        // Mail::to('aleksmakarov5@yandex.ru')->queue(new newmessage($id, $author->name, $request->message));

        return redirect()
            ->route('chat', $id);
    }
    public function doc_load($id)
    {
        $documents = DB::table('attachments')
            ->where('id_application', '=', $id)
            ->get();
        return view('application.doc_load', ['id' => $id], ['documents' => $documents]);
    }
    public function tg_load($id)
    {
        if (Auth::user()->telegram_notify) {
            $new_chat = TelegraphChat::where('chat_id', '=', Auth::user()->telegram_id)->first();
            $telegram_action = telegram_action::updateOrCreate(
                ['id_chat' => Auth::user()->telegram_id],
                [
                    'id_chat' => Auth::user()->telegram_id,
                    'id_application' => $id,
                    'action' => 1,
                    'number' => 0,
                    'id_message' => 0,
                    'count' => 0,
                    'message_id' => 0
                ]
            );
            $documents = DB::table('attachments')
                ->where('id_application', '=', $id)
                ->get()->all();
            $i = 0;
            $keyboard = [];
            $dir = 'storage/files/' . $id;
            $application = application::where('id', '=', $id)->first();
            foreach ($documents as $attach) {
                if (pathinfo($attach->location, PATHINFO_EXTENSION) != 'gif' && pathinfo($attach->location, PATHINFO_EXTENSION) != 'png') {
                    if (
                        pathinfo($attach->location, PATHINFO_EXTENSION) == 'zip' ||
                        pathinfo($attach->location, PATHINFO_EXTENSION) == 'rar' ||
                        pathinfo($attach->location, PATHINFO_EXTENSION) == '7z'
                    )
                        $path = 'https://mandarin-portal.ru/gallery/' . $id . '/' . $attach->location . '/' . $attach->file_name . '?hide_menu=1';
                    else

                        $path = "https://mandarin-portal.ru/" . $dir . '/' . $attach->location;

                    $name = pathinfo($attach->file_name, PATHINFO_FILENAME);
                    $keyboard[$i++] = Button::make("{$name}")->webApp("{$path}");
                }
            }
            $keyboard[$i++] = Button::make("Основная информация")->action("application")->param('id', "{$id}");
            $keyboard[$i++] = Button::make("Отправить документ")->action("doc_load")->param('id', "{$id}");
            $keyboard[$i++] = Button::make("Распознать данные")->action("doc_ocr")->param('id', "{$id}");

            $message = $new_chat->message("Документы|Заявка {$application->id}| {$application->gnz}")
                ->keyboard(
                    Keyboard::make()
                        ->buttons($keyboard)
                )


                ->send();
            $user = User::where('telegram_id', '=', Auth::user()->telegram_id)->first();
            if ($user->message_id)
                $new_chat->deleteMessage($user->message_id)->send();
            $user->message_id = json_decode($message)->result->message_id;
            $user->save();
        }

        return redirect()->back();
    }
    public function sms_send($id)
    {
        $application = application::where('id', '=', $id)->first();
        $service = service::where('id', '=', $application->id_service)->first();
        $usr = User::where('id', '=', $service->id_tg)->first();

        $new_chat = TelegraphChat::where('chat_id', '=', Auth::user()->telegram_id)->first();
        $new_chat1 = TelegraphChat::where('chat_id', '=', $usr->telegram_id)->first();


        $telegram_action = telegram_action::updateOrCreate(
            ['id_chat' => $usr->telegram_id],
            [
                'id_chat' => $usr->telegram_id,
                'id_application' => $id,
                'action' => 1,
                'number' => 35,
                'id_message' => 0,
                'count' => 0,
                'message_id' => 0
            ]
        );
        // dd($telegram_action);
        // $documents = DB::table('attachments')
        //     ->where('id_application', '=', $id)
        //     ->get()->all();
        // $i = 0;
        $text = "";
        $keyboard = [];
        $our_company = OurCompany::join('legal_entities', 'legal_entities.id', '=', 'our_companies.id_legal')
            ->where('our_companies.id', '=', $application->our_company)
            ->select('legal_entities.name')
            ->first();
        $ic = InsuranceCompany::where('id', '=', $application->id_insuranceCompany)->first();
        if ($application->gender)
            $text = 'Уважаемая ';
        else
            $text = 'Уважаемый ';

        $text .=  ($application->ClientName != "" ? $application->ClientName : 'клиент') . '! Приглашаем Вас на ремонт Вашего транспортного средства ' . $application->CarModel . ' ' . $application->gnz . ' на СТОА ' .
            $our_company->name . ', расположенную по адресу: ' .
            $service->service_description . ', для проведения восстановительного ремонта по направлению ' .
            $application->numberInsuranceCompanyApplication . ' выданному ' . (isset($application->napr_date) ? Date::parse($application->napr_date)->format('j F Yг.') : '') . ' ' . $ic->address . '. Режим работы СТОА: ' . $service->mode . '.';
        // $dir = 'storage/files/' . $id;
        // dd($text);


        // foreach ($documents as $attach) {
        //     if (pathinfo($attach->location, PATHINFO_EXTENSION) != 'gif' && pathinfo($attach->location, PATHINFO_EXTENSION) != 'png') {
        //         if (
        //             pathinfo($attach->location, PATHINFO_EXTENSION) == 'zip' ||
        //             pathinfo($attach->location, PATHINFO_EXTENSION) == 'rar' ||
        //             pathinfo($attach->location, PATHINFO_EXTENSION) == '7z'
        //         )
        //             $path = 'https://mandarin-portal.ru/gallery/' . $id . '/' . $attach->location . '/' . $attach->file_name . '?hide_menu=1';
        //         else

        //             $path = "https://mandarin-portal.ru/" . $dir . '/' . $attach->location;

        //         $name = pathinfo($attach->file_name, PATHINFO_FILENAME);
        //         $keyboard[$i++] = Button::make("{$name}")->webApp("{$path}");
        //     }
        // }
        // $keyboard[$i++] = Button::make("Основная информация")->action("application")->param('id', "{$id}");
        // $keyboard[$i++] = Button::make("Отправить документ")->action("doc_load")->param('id', "{$id}");
        // $keyboard[$i++] = Button::make("Распознать данные")->action("doc_ocr")->param('id', "{$id}");


        $message = $new_chat->message("{$text} ")
            // ->keyboard(
            //     Keyboard::make()
            //         ->buttons($keyboard)
            // )

            // ->keyboard(Keyboard::make()
            //     ->buttons([
            //         Button::make("Отправить сообщение")->url("https://mandarin-portal.ru/application/{$application->id}/sms_send_result"),

            //     ]))
            ->send();
        $user = User::where('telegram_id', '=', Auth::user()->telegram_id)->first();
        if ($user->message_id)
            $new_chat->deleteMessage($user->message_id)->send();
        $user->message_id = json_decode($message)->result->message_id;
        $user->save();
        $message = $new_chat1->message("{$text} \n\n\nНажмите на кнопку для отправки сообщения. После отправки сделайте скриншот экрана и отправьте ответным сообщением в этот чат.")
            // ->keyboard(
            //     Keyboard::make()
            //         ->buttons($keyboard)
            // )

            ->keyboard(Keyboard::make()
                ->buttons([
                    Button::make("Отправить сообщение")->url("https://mandarin-portal.ru/application/{$application->id}/sms_send_result"),

                ]))
            ->send();
        if ($usr->message_id)
            $new_chat->deleteMessage($usr->message_id)->send();
        $usr->message_id = json_decode($message)->result->message_id;
        $usr->save();


        return redirect()->back();
    }
    public function sms_send_result($id)
    {
        $application = application::where('id', '=', $id)->first();
        $our_company = OurCompany::join('legal_entities', 'legal_entities.id', '=', 'our_companies.id_legal')
            ->where('our_companies.id', '=', $application->our_company)
            ->select('legal_entities.name')
            ->first();
        $service = service::where('id', '=', $application->id_service)->first();
        $ic = InsuranceCompany::where('id', '=', $application->id_insuranceCompany)->first();
        if ($application->gender)
            $text = 'Уважаемая ';
        else
            $text = 'Уважаемый ';

        $text .=  ($application->ClientName != "" ? $application->ClientName : 'клиент') . '! Приглашаем Вас на ремонт Вашего транспортного средства ' . $application->CarModel . ' ' . $application->gnz . ' на СТОА ' .
            $our_company->name . ', расположенную по адресу: ' .
            $service->service_description . ', для проведения восстановительного ремонта по направлению ' .
            $application->numberInsuranceCompanyApplication . ' выданному ' . (isset($application->napr_date) ? Date::parse($application->napr_date)->format('j F Yг.') : '') . ' ' . $ic->address . '. Режим работы СТОА: ' . $service->mode . '.';
        // $text = htmlentities($text);
        $phone = $application->ClientPhone;
        $phone = str_replace('(', '', $phone);
        $phone = str_replace(')', '', $phone);

        $phone = str_replace('-', '', $phone);

        $phone = str_replace(' ', '', $phone);
        // if (isset($application->pred_phone)) {
        //     $phone1 = $application->pred_phone;
        //     $phone1 = str_replace('(', '', $phone1);
        //     $phone1 = str_replace(')', '', $phone1);

        //     $phone1 = str_replace('-', '', $phone1);

        //     $phone1 = str_replace(' ', '', $phone1);
        //     $phone = 'open?addresses=' . $phone . ',' . $phone1;
        // }

        $href = "sms://" . $phone . ";?&body=" . $text;
        // dd($href);
        // $href = "tel:+79815609071";
        // $href = "ya.ru";
        // $text = "<a href='" . $href . "'> Отправить собщение </a>";




        return view('application.sms', ['id' => $id, 'href' => $href]);
    }

    public function data_load(Request $request, $id)
    {
        $loc = mb_substr($request['location'], 0, 8);
        $applications = array();
        $estimate = '';
        $price = [];

        $smeta = false;


        if ($loc == "passport") {
            rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
            exec('base64 -i storage/files/' . $id . '/work.pdf > storage/files/' . $id . '/output.txt');
            rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);
            $output = file_get_contents('storage/files/' . $id . '/output.txt');
            $data = array(
                "mimeType" => "application/pdf",
                "languageCodes" => ["*"],
                "model" => "vehicle-registration-front",
                "content" => $output
            );


            $ch = curl_init('https://ocr.api.cloud.yandex.net/ocr/v1/recognizeTextAsync');
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type:application/json',
                    'Authorization: Api-Key AQVNwD4aqhqRFt7PCZNt1WEmpma_qrsqyLCuxtm2',
                    'x-folder-id: b1gcnqkpepfcp6fulsah',
                    'x-data-logging-enabled: true'
                )
            );
            curl_setopt(
                $ch,
                CURLOPT_POST,
                1
            );
            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                json_encode($data, JSON_UNESCAPED_UNICODE)
            );
            curl_setopt(
                $ch,
                CURLOPT_RETURNTRANSFER,
                true
            );
            curl_setopt(
                $ch,
                CURLOPT_SSL_VERIFYPEER,
                false
            );
            curl_setopt(
                $ch,
                CURLOPT_HEADER,
                false
            );
            $res = curl_exec($ch);
            curl_close($ch);

            $res = json_decode($res, JSON_UNESCAPED_UNICODE);

            // echo $res['id'];

            $ch = curl_init('https://ocr.api.cloud.yandex.net/ocr/v1/getRecognition?operationId=' . $res['id']);
            curl_setopt(
                $ch,
                CURLOPT_RETURNTRANSFER,
                true
            );
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type:application/json',
                    'Authorization: Api-Key AQVNwD4aqhqRFt7PCZNt1WEmpma_qrsqyLCuxtm2',
                    'x-folder-id: b1gcnqkpepfcp6fulsah',
                    'x-data-logging-enabled: true'
                )
            );
            curl_setopt(
                $ch,
                CURLOPT_SSL_VERIFYPEER,
                false
            );
            curl_setopt(
                $ch,
                CURLOPT_HEADER,
                false
            );
            $html = curl_exec($ch);
            $html = json_decode($html, JSON_UNESCAPED_UNICODE);
            if (isset($html['error']['httpCode']))
                while (isset($html['error'])) {
                    // echo ($html['error']['httpCode'] . '<br>');
                    $html = curl_exec($ch);
                    $html = json_decode($html, JSON_UNESCAPED_UNICODE);
                }
            $html = curl_exec($ch);
            $html = explode("\n", $html);
            $passport = [];
            $i = 0;
            foreach ($html as $key => $value) {
                $passport[$i++] = json_decode($value, JSON_UNESCAPED_UNICODE);
            }
            curl_close($ch);
            dd($passport);
            // $data = $passport[0]['textAnnotation']['entities'];
            foreach ($passport[0]['result']['textAnnotation']['entities'] as $entity) {

                $applications[$entity['name']] = $entity['text'];
            }
            $applications['issued_by'] = mb_convert_case($applications['issued_by'], MB_CASE_UPPER, "UTF-8");
            $applications['number'] = mb_substr($applications['number'], 0, 4) . ' ' . mb_substr($applications['number'], 4, 6);
            $applications['ClientName']
                = mb_convert_case($applications['name'], MB_CASE_TITLE, "UTF-8") . ' ' . mb_convert_case($applications['middle_name'], MB_CASE_TITLE, "UTF-8") . ' ' . mb_convert_case($applications['surname'], MB_CASE_TITLE, "UTF-8");
            //dd($applications);
        } elseif (mb_strpos($loc, 'ТС') != false || mb_strpos($loc, 'тс') != false) {
            // dd($loc);
            rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
            exec('base64 -i storage/files/' . $id . '/work.pdf > storage/files/' . $id . '/output.txt');
            rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);
            $output = file_get_contents('storage/files/' . $id . '/output.txt');
            $data = array(
                "mimeType" => "application/pdf",
                "languageCodes" => ["*"],
                "model" => "vehicle-registration-front",
                "content" => $output
            );


            $ch = curl_init('https://ocr.api.cloud.yandex.net/ocr/v1/recognizeTextAsync');
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type:application/json',
                    'Authorization: Api-Key AQVNwD4aqhqRFt7PCZNt1WEmpma_qrsqyLCuxtm2',
                    'x-folder-id: b1gcnqkpepfcp6fulsah',
                    'x-data-logging-enabled: true'
                )
            );
            curl_setopt(
                $ch,
                CURLOPT_POST,
                1
            );
            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                json_encode($data, JSON_UNESCAPED_UNICODE)
            );
            curl_setopt(
                $ch,
                CURLOPT_RETURNTRANSFER,
                true
            );
            curl_setopt(
                $ch,
                CURLOPT_SSL_VERIFYPEER,
                false
            );
            curl_setopt(
                $ch,
                CURLOPT_HEADER,
                false
            );
            $res = curl_exec($ch);
            curl_close($ch);

            $res = json_decode($res, JSON_UNESCAPED_UNICODE);

            // echo $res['id'];

            $ch = curl_init('https://ocr.api.cloud.yandex.net/ocr/v1/getRecognition?operationId=' . $res['id']);
            curl_setopt(
                $ch,
                CURLOPT_RETURNTRANSFER,
                true
            );
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type:application/json',
                    'Authorization: Api-Key AQVNwD4aqhqRFt7PCZNt1WEmpma_qrsqyLCuxtm2',
                    'x-folder-id: b1gcnqkpepfcp6fulsah',
                    'x-data-logging-enabled: true'
                )
            );
            curl_setopt(
                $ch,
                CURLOPT_SSL_VERIFYPEER,
                false
            );
            curl_setopt(
                $ch,
                CURLOPT_HEADER,
                false
            );
            $html = curl_exec($ch);
            $html = json_decode($html, JSON_UNESCAPED_UNICODE);
            if (isset($html['error']['httpCode']))
                while (isset($html['error'])) {
                    // echo ($html['error']['httpCode'] . '<br>');
                    $html = curl_exec($ch);
                    $html = json_decode($html, JSON_UNESCAPED_UNICODE);
                }
            $html = curl_exec($ch);
            $html = explode("\n", $html);
            $passport = [];
            $i = 0;
            foreach ($html as $key => $value) {
                $passport[$i++] = json_decode($value, JSON_UNESCAPED_UNICODE);
            }
            curl_close($ch);
            // dd($passport);
            // $data = $passport[0]['textAnnotation']['entities'];
            $data = [];
            foreach ($passport[0]['result']['textAnnotation']['entities'] as $entity) {

                $data[$entity['name']] = $entity['text'];
                $data[$entity['name']] = mb_convert_case($data[$entity['name']], MB_CASE_UPPER, "UTF-8");
            }
            // dd($data);
            $applications['CarModel'] = $data['stsfront_car_brand'] . ' ' . $data['stsfront_car_model'];
            $applications['gnz'] = $data['stsfront_car_number'];
            $applications['vin'] = $data['stsfront_vin_number'];
            $applications['vehicle_passport'] = mb_substr($data['stsfront_sts_number'], 0, 4) . ' ' . mb_substr($data['stsfront_sts_number'],  4);
            $applications['Color'] = $data['stsfront_car_color'];

            rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
            exec('base64 -i storage/files/' . $id . '/work.pdf > storage/files/' . $id . '/output.txt');
            rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);
            $output = file_get_contents('storage/files/' . $id . '/output.txt');
            $data = array(
                "mimeType" => "application/pdf",
                "languageCodes" => ["*"],
                "model" => "vehicle-registration-back",
                "content" => $output
            );


            $ch = curl_init('https://ocr.api.cloud.yandex.net/ocr/v1/recognizeTextAsync');
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type:application/json',
                    'Authorization: Api-Key AQVNwD4aqhqRFt7PCZNt1WEmpma_qrsqyLCuxtm2',
                    'x-folder-id: b1gcnqkpepfcp6fulsah',
                    'x-data-logging-enabled: true'
                )
            );
            curl_setopt(
                $ch,
                CURLOPT_POST,
                1
            );
            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                json_encode($data, JSON_UNESCAPED_UNICODE)
            );
            curl_setopt(
                $ch,
                CURLOPT_RETURNTRANSFER,
                true
            );
            curl_setopt(
                $ch,
                CURLOPT_SSL_VERIFYPEER,
                false
            );
            curl_setopt(
                $ch,
                CURLOPT_HEADER,
                false
            );
            $res = curl_exec($ch);
            curl_close($ch);

            $res = json_decode(
                $res,
                JSON_UNESCAPED_UNICODE
            );

            // echo $res['id'];

            $ch = curl_init('https://ocr.api.cloud.yandex.net/ocr/v1/getRecognition?operationId=' . $res['id']);
            curl_setopt(
                $ch,
                CURLOPT_RETURNTRANSFER,
                true
            );
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type:application/json',
                    'Authorization: Api-Key AQVNwD4aqhqRFt7PCZNt1WEmpma_qrsqyLCuxtm2',
                    'x-folder-id: b1gcnqkpepfcp6fulsah',
                    'x-data-logging-enabled: true'
                )
            );
            curl_setopt(
                $ch,
                CURLOPT_SSL_VERIFYPEER,
                false
            );
            curl_setopt(
                $ch,
                CURLOPT_HEADER,
                false
            );
            $html = curl_exec($ch);
            $html = json_decode($html, JSON_UNESCAPED_UNICODE);
            if (isset($html['error']['httpCode']))
                while (isset($html['error'])) {
                    // echo ($html['error']['httpCode'] . '<br>');
                    $html = curl_exec($ch);
                    $html = json_decode($html, JSON_UNESCAPED_UNICODE);
                }
            $html = curl_exec($ch);
            $html = explode("\n", $html);
            $passport = [];
            $i = 0;
            foreach ($html as $key => $value) {
                $passport[$i++] = json_decode($value, JSON_UNESCAPED_UNICODE);
            }
            curl_close($ch);
            // dd($passport);
            // $data = $passport[0]['textAnnotation']['entities'];
            $data = [];
            foreach ($passport[0]['result']['textAnnotation']['entities'] as $entity) {

                $data[$entity['name']] = $entity['text'];
                // $data[$entity['name']] = mb_convert_case($data[$entity['name']], MB_CASE_UPPER, "UTF-8");

                // $data[$entity['name']] = ucfirst($data[$entity['name']]);
            }
            // dd($data);
            $applications['ClientName'] = $data['stsback_car_owner'];
            // dd($applications);
        } elseif (mb_strpos($loc, 'О_') != false) {
            rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
            exec('pdftohtml storage/files/' . $id . '/work.pdf  -p -i');
            rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);

            $html = 'works.html';
            $file = file_get_contents('storage/files/' . $id . '/' . $html);
            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = preg_replace("(\u{A0})", '', $file);
            $file = str_replace('&#160', '', $file);
            $file = preg_replace('/\xc2\xa0/', '<br/>', $file);

            $file = preg_replace("/&#160/", ' ', $file);
            $parse_start = mb_strpos($file, 'Страховой полис');
            $parse = mb_substr($file, $parse_start);
            $parse = explode('<br/>', $parse);
            $applications['dogovor'] = $parse[1];
            $parse_start = mb_strpos($file, 'Марка, модель');
            $parse = mb_substr($file, $parse_start);
            $parse = explode('<br/>', $parse);
            $applications['CarModel'] = $parse[1];
            $parse_start = mb_strpos($file, 'Год выпуска');
            $parse = mb_substr($file, $parse_start);
            $parse = explode('<br/>', $parse);
            $applications['mnf_year'] = $parse[1];
            $parse_start = mb_strpos($file, 'Пробег');
            $parse = mb_substr($file, $parse_start);
            $parse = explode('<br/>', $parse);
            $applications['mileage'] = $parse[1];
            $parse_start = mb_strpos($file, 'Дата:');
            $parse = mb_substr($file, $parse_start);
            $parse = explode('<br/>', $parse);
            $applications['DTPdate'] = trim(str_replace('Дата:', '', $parse[0]));
            $parse_start = mb_strpos($file, 'Гос.номер');
            $parse = mb_substr($file, $parse_start);
            $parse = explode('<br/>', $parse);
            $applications['gnz'] = trim(str_replace('Гос.номер:', '', $parse[0]));
            $parse_start = mb_strpos($file, 'VIN');
            $parse = mb_substr($file, $parse_start);
            $parse = explode('<br/>', $parse);
            $applications['vin'] = trim(str_replace('VIN:', '', $parse[0]));


            // dd($parse, $applications);
        } else {
            if (pathinfo($request['location'], PATHINFO_EXTENSION) == 'doc' || pathinfo($request['location'], PATHINFO_EXTENSION) == 'docx' || pathinfo($request['location'], PATHINFO_EXTENSION) == 'rtf') {
                if (!file_exists('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.html')) {
                    $pathToSave = 'storage/files/' . $id . '/' . $request['location'];
                    $dir = 'storage/files/' . $id;
                    exec('chmod 755 "' . $pathToSave . '"');
                    $comand = 'libreoffice --headless --convert-to html "' . $pathToSave . '" --outdir ' . $dir;
                    exec($comand);
                    // dd($comand);
                }
                $request['location'] = pathinfo($request['location'], PATHINFO_FILENAME) . '.html';
                rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/works.html');
            }
            //dd($request['iskl']);
            else {
                rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
                exec('pdftohtml storage/files/' . $id . '/work.pdf  -p -i');
                rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);
            }

            $html = 'works.html';
            $file = file_get_contents('storage/files/' . $id . '/' . $html);
            $file = html_entity_decode(strip_tags($file, ['br']));




            $app = Application::find($id);
            if ($request['id_ic'])
                $app->id_insuranceCompany = 3;
            if ((mb_strpos($file, 'одометр') != 0 || mb_strpos($file, 'Ананьин') != 0 || mb_strpos($file, 'Мандарин') != 0) && !(mb_strpos($file, 'одометра')) || mb_strpos($file, "1\t")) {
                // $file = html_entity_decode(strip_tags($file, ['br']));
                $file = str_replace("\t", '', $file);
                $file = str_replace("\n\n", '<br/>', $file);
                $file = str_replace("<br/><br/>", '<br/>', $file);

                $file = str_replace("\n", ' ', $file);
                $file = str_replace("  ", ' ', $file);
                // $file = str_replace("/\u{000D}", '<br/>', $file);


                $file = preg_replace("(\u{A0})", ' ', $file);

                $file = str_replace("/\u{A0}/", '', $file);
                // dd($file);


                // dd($applications['numberInsuranceCompanyApplication']);
                $estimate_start = mb_strpos($file, 'стоимость запчастей');
                $estimate = mb_substr($file, $estimate_start);
                $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                $estimate = explode('<br/>', $estimate);
                $price[0] = str2number($estimate[1]);
                $estimate_start = mb_strpos($file, 'стоимость запчастей (с учетом скидки)');
                $estimate = mb_substr($file, $estimate_start);
                $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                $estimate = explode('<br/>', $estimate);
                $price[1] = str2number($estimate[1]);
                $estimate_start = mb_strpos($file, 'стоимость ремонтных работ');
                $estimate = mb_substr($file, $estimate_start);
                $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                $estimate = explode('<br/>', $estimate);
                $price[2] = str2number($estimate[1]);
                $estimate_start = mb_strpos($file, 'стоимость окрасочных работ');
                $estimate = mb_substr($file, $estimate_start);
                $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                $estimate = explode('<br/>', $estimate);
                $price[3] = str2number($estimate[1]);
                $estimate_start = mb_strpos($file, 'дополнительных материалов');
                $estimate = mb_substr($file, $estimate_start);
                $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                $estimate = explode('<br/>', $estimate);
                $price[4] = str2number($estimate[1]);
                $estimate_start = mb_strpos($file, 'стоимость устранения дефектов АТС (с учетом скидки)');
                if (!$estimate_start)
                    $estimate_start = mb_strpos($file, 'АТС (с учетом');

                $estimate = mb_substr($file, $estimate_start);
                $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                $estimate = explode('<br/>', $estimate);
                $price[7] = str2number($estimate[1]);
                $estimate_start = mb_strpos($file, 'стоимость ремонта АТС');
                $estimate = mb_substr($file, $estimate_start);
                $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                $estimate = explode('<br/>', $estimate);
                $price[6] = str2number($estimate[1]);
                // dd($applications);
            } else {
                if ($app->id_insuranceCompany == 5 || $app->id_insuranceCompany == 1) {
                    $application_start = mb_strpos($file, 'Марка, модель');
                    $estimate_start = mb_strpos($file, 'стоимость узлов и деталей');
                    $estimate_end = mb_strlen($file);
                    if ($estimate_start !== false && $estimate_end !== false) {
                        $estimate = mb_substr($file, $estimate_start, $estimate_end - $estimate_start);


                        $estimate = explode('<br/>', $estimate);

                        if (is_numeric(str_replace(' ', '', $estimate[1])))
                            $price[0] = str_replace(' ', '', $estimate[1]);
                        else
                            $price[0] = str_replace(' ', '', $estimate[2]);

                        if (is_numeric(str_replace(' ', '', $estimate[3])))
                            $price[1] = str_replace(' ', '', $estimate[3]);
                        else
                            $price[1] = str_replace(' ', '', $estimate[4]);
                    }
                    $estimate_start = mb_strpos($file, 'стоимость ремонта/замены');
                    $estimate_end = mb_strlen($file);
                    $estimate = '';

                    if ($estimate_start !== false && $estimate_end !== false) {
                        $estimate = mb_substr($file, $estimate_start, $estimate_end - $estimate_start);
                        $estimate = preg_replace("(\u{A0})", '<br/>', $estimate);

                        $estimate = explode('<br/>', $estimate);
                        for ($i = 0; $i < count($estimate) - 1; $i++) {
                            if ($estimate[$i] == '1' || $estimate[$i] == '2')
                                for ($j = $i; $j < count($estimate) - 1; $j++)
                                    $estimate[$j] = $estimate[$j + 1];
                        }
                        // dd($estimate);
                        if (is_numeric(str_replace(' ', '', $estimate[1])))
                            $price[2] = str_replace(' ', '', $estimate[1]);
                        else
                            $price[2] = str_replace(' ', '', $estimate[2]);
                        if (is_numeric(str_replace(' ', '', $estimate[3])))
                            $price[3] = str_replace(' ', '', $estimate[3]);
                        else
                            $price[3] = str_replace(' ', '', $estimate[4]);
                        if (is_numeric(str_replace(' ', '', $estimate[5])))
                            $price[4] = str_replace(' ', '', $estimate[5]);
                        else
                            $price[4] = str_replace(' ', '', $estimate[6]);
                        if (is_numeric(str_replace(' ', '', $estimate[7])))
                            $price[5] = str_replace(' ', '', $estimate[7]);
                        else
                            $price[5] = str_replace(' ', '', $estimate[8]);
                        if (trim($estimate[9]) == 'ВСЕГО:') {
                            if (is_numeric(str_replace(' ', '', $estimate[10])))
                                $price[6] = str_replace(' ', '', $estimate[10]);
                            else
                                $price[6] = str_replace(' ', '', $estimate[11]);
                            if (is_numeric(str_replace(' ', '', $estimate[12])))
                                $price[7] = str_replace(' ', '', $estimate[12]);
                            else
                                $price[7] = str_replace(' ', '', $estimate[13]);
                        } else {
                            if (is_numeric(str_replace(' ', '', $estimate[9])))
                                $price[6] = str_replace(' ', '', $estimate[9]);
                            else
                                $price[6] = str_replace(' ', '', $estimate[10]);
                            if (is_numeric(str_replace(' ', '', $estimate[11])))
                                $price[7] = str_replace(' ', '', $estimate[11]);
                            else
                                $price[7] = str_replace(' ', '', $estimate[12]);
                        }
                    }

                    // dd($price);
                    if ($application_start) {
                        $smeta = true;
                    }

                    // echo $application_start;
                    // dd($file);
                    $file = preg_replace("/\u{A0}/", ' ', $file);
                    // dd($loc);
                    if (mb_strpos($loc, 'правле') != false) {
                        $parse_start = mb_strpos($file, 'СТРАХОВАТЕЛЬ');
                        if (!$parse_start)
                            $parse_start = mb_strpos($file, 'ПОТЕРПЕВШИЙ');

                        $parse = mb_substr($file, $parse_start);
                        $parse = explode('<br/>', $parse);
                        $applications['ClientName'] = $parse[1];
                        $parse_start = mb_strpos($file, 'Контактный тел.');
                        $parse = mb_substr($file, $parse_start);
                        $parse = explode('<br/>', $parse);
                        $phone = $parse[1];
                        $phone = preg_replace('/[^0-9]/', '', $phone);
                        $start = mb_strpos($phone, '9');
                        $phone = mb_substr($phone, $start);
                        $phone = '+7 (' . $phone[0] . $phone[1] . $phone[2] . ') ' . $phone[3] . $phone[4] . $phone[5] . '-' . $phone[6] . $phone[7] . '-' . $phone[8] . $phone[9];
                        $applications['ClientPhone'] = $phone;
                        $parse_start = mb_strpos($file, 'Убыток №:');
                        $parse = mb_substr($file, $parse_start);
                        $parse = explode('<br/>', $parse);
                        $applications['ub_number'] = mb_substr($parse[0], mb_strpos($parse[0], '/') + 1);
                        if (mb_strpos($parse[0], 'ДО')) {
                            $applications['CarModel'] = $parse[8];
                            $applications['gnz'] = $parse[9];
                            $applications['mileage'] = (int) $parse[16];
                            $applications['vin'] = $parse[10];
                            $applications['mnf_year'] = $parse[15];
                        } else {
                            $applications['CarModel'] = $parse[6];
                            $applications['gnz'] = $parse[7];
                            $applications['mileage'] = (int) $parse[14];
                            $applications['vin'] = $parse[8];
                            $applications['mnf_year'] = $parse[13];
                        }
                        // dd($applications, $file, $parse);
                    } else {
                        $parse_start = mb_strpos($file, 'Марка, модель');
                        if (!$parse_start)
                            $parse_start = mb_strpos($file, 'Марка, модель');
                        $parse = mb_substr($file, $parse_start);
                        $parse = explode('<br/>', $parse);
                        $applications['CarModel'] = $parse[1];
                        $parse_start = mb_strpos($file, 'VIN/кузов');
                        if (!$parse_start)
                            $parse_start = mb_strpos($file, 'VIN / кузов');
                        $parse = mb_substr($file, $parse_start);
                        $parse = explode('<br/>', $parse);
                        $applications['vin'] = $parse[1];
                        $parse_start = mb_strpos($file, 'Регистр. знак');
                        $parse = mb_substr($file, $parse_start);
                        $parse = explode('<br/>', $parse);
                        $applications['gnz'] = $parse[1];
                        $parse_start = mb_strpos($file, 'Пробег, км');
                        $parse = mb_substr($file, $parse_start);
                        $parse = explode('<br/>', $parse);
                        $applications['mileage'] = $parse[1];
                        $parse_start = mb_strpos($file, 'Цвет, тип окраски');
                        $parse = mb_substr($file, $parse_start);
                        $parse = explode('<br/>', $parse);
                        $applications['Color'] = $parse[1];
                        $parse_start = mb_strpos($file, 'Год выпуска');
                        $parse = mb_substr($file, $parse_start);
                        $parse = explode('<br/>', $parse);
                        $applications['mnf_year'] = $parse[1];
                        $parse_start = mb_strpos($file, 'Дата происшествия / ДТП');
                        if (!$parse_start)
                            $parse_start = mb_strpos($file, 'Дата ДТП');
                        $parse = mb_substr($file, $parse_start);
                        $parse = explode('<br/>', $parse);
                        $applications['DTPdate'] = $parse[1];
                        $parse_start = mb_strpos($file, 'Собственник');
                        $parse = mb_substr($file, $parse_start);
                        $parse = explode('<br/>', $parse);
                        $applications['ClientName'] = $parse[1];
                        $parse_start = mb_strpos($file, 'Свид. о рег. / ПТС');
                        if (!$parse_start)
                            $parse_start = mb_strpos($file, 'ПТС/свид.о рег.');
                        $parse = mb_substr($file, $parse_start);

                        $parse = explode('<br/>', $parse);
                        $applications['vehicle_passport'] = $parse[1];
                    }
                } else if ($app->id_insuranceCompany == 3) {
                    $file = preg_replace("(\u{A0})", ' ', $file);

                    $file = str_replace("/\u{A0}/", '', $file);
                    // dd($file);
                    $application_start = mb_strpos($file, 'Марка, модель');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    $applications['CarModel'] = $application_array[1];
                    $application_start = mb_strpos($file, 'Дата выпуска');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    $applications['mnf_year'] = $application_array[1];
                    $application_start = mb_strpos($file, 'Регистр. знак');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    $applications['gnz'] = $application_array[1];
                    $application_start = mb_strpos($file, '№ кузова');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    $applications['vin'] = $application_array[1];
                    $application_start = mb_strpos($file, 'одометра');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    $applications['mileage'] = (int) $application_array[1];
                    $application_start = mb_strpos($file, 'Свид.о регистр.');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    $applications['vehicle_passport'] = $application_array[1];
                    $application_start = mb_strpos($file, 'Собственник');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    $applications['ClientName'] = $application_array[1];
                    $application_start = mb_strpos($file, 'Дата:');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    $applications['DTPdate'] = $application_array[1];

                    $file = preg_replace("(\u{A0})", ' ', $file);

                    $estimate_start = mb_strpos($file, 'использованных для ремонта');
                    if (!$estimate_start)
                        $estimate_start = mb_strpos($file, 'стоимость узлов и деталей:');
                    $estimate_end = mb_strlen($file);
                    // dd($file);
                    if ($estimate_start !== false && $estimate_end !== false) {
                        $estimate = mb_substr($file, $estimate_start, $estimate_end - $estimate_start);


                        $estimate = explode('<br/>', $estimate);
                        // dd($estimate);

                        if (is_numeric(str2number($estimate[1])))
                            $price[0] = str2number($estimate[1]);
                        else
                            $price[0] = str2number($estimate[2]);

                        if (is_numeric(str2number($estimate[3])))
                            $price[1] = str2number($estimate[3]);
                        else
                            $price[1] = str2number($estimate[4]);
                    }
                    $estimate_start = mb_strpos($file, 'ремонтных работ');
                    if (!$estimate_start)
                        $estimate_start = mb_strpos($file, 'ремонта/замены');
                    $estimate_end = mb_strlen($file);
                    $estimate = '';
                    // dd($file);
                    if ($estimate_start !== false && $estimate_end !== false) {
                        $estimate = mb_substr($file, $estimate_start, $estimate_end - $estimate_start);
                        $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                        $estimate = explode('<br/>', $estimate);
                        // dd($estimate);
                        for ($i = 0; $i < count($estimate) - 1; $i++) {
                            if ($estimate[$i] == '1' || $estimate[$i] == '2')
                                for ($j = $i; $j < count($estimate) - 1; $j++)
                                    $estimate[$j] = $estimate[$j + 1];
                        }
                        if (is_numeric(str2number($estimate[1])))
                            $price[2] = str2number($estimate[1]);
                        else
                            $price[2] = str2number($estimate[2]);
                    }

                    $estimate_start = mb_strpos($file, 'окрасочных работ:');
                    if (!$estimate_start)
                        $estimate_start = mb_strpos($file, 'окраски контроля');
                    // dd($file, $estimate_start);
                    $estimate_end = mb_strlen($file);
                    $estimate = '';

                    if ($estimate_start !== false && $estimate_end !== false) {
                        $estimate = mb_substr($file, $estimate_start, $estimate_end - $estimate_start);
                        $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                        $estimate = explode('<br/>', $estimate);
                        // dd($estimate);
                        for ($i = 0; $i < count($estimate) - 1; $i++) {
                            if ($estimate[$i] == '1' || $estimate[$i] == '2')
                                for ($j = $i; $j < count($estimate) - 1; $j++)
                                    $estimate[$j] = $estimate[$j + 1];
                        }
                        if (is_numeric(str2number($estimate[1])))
                            $price[3] = str2number($estimate[1]);
                        else
                            $price[3] = str2number($estimate[2]);
                    }
                    $estimate_start = mb_strpos($file, 'стоимость расходных материалов для ремонта:');
                    if (!$estimate_start)
                        $estimate_start = mb_strpos($file, 'расходных материалов');
                    $estimate_end = mb_strlen($file);
                    $estimate = '';

                    if ($estimate_start !== false && $estimate_end !== false) {
                        $estimate = mb_substr($file, $estimate_start, $estimate_end - $estimate_start);
                        $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                        $estimate = explode('<br/>', $estimate);
                        // dd($estimate);
                        for ($i = 0; $i < count($estimate) - 1; $i++) {
                            if ($estimate[$i] == '1' || $estimate[$i] == '2')
                                for ($j = $i; $j < count($estimate) - 1; $j++)
                                    $estimate[$j] = $estimate[$j + 1];
                        }
                        if (is_numeric(str2number($estimate[1])))
                            $price[4] = str2number($estimate[1]);
                        else
                            $price[4] = str2number($estimate[2]);
                    }
                    $estimate_start = mb_strpos($file, 'всех ремонтных работ:');
                    if (!$estimate_start)
                        $estimate_start = mb_strpos($file, 'работ и материалов');
                    $estimate_end = mb_strlen($file);
                    $estimate = '';

                    if ($estimate_start !== false && $estimate_end !== false) {
                        $estimate = mb_substr($file, $estimate_start, $estimate_end - $estimate_start);
                        $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                        $estimate = explode('<br/>', $estimate);
                        // dd($estimate);
                        for ($i = 0; $i < count($estimate) - 1; $i++) {
                            if ($estimate[$i] == '1' || $estimate[$i] == '2')
                                for ($j = $i; $j < count($estimate) - 1; $j++)
                                    $estimate[$j] = $estimate[$j + 1];
                        }
                        if (is_numeric(str2number($estimate[1])))
                            $price[5] = str2number($estimate[1]);
                        else
                            $price[5] = str2number($estimate[2]);
                    }
                    if (!isset($price[5]) && isset($price[2]) && isset($price[3]))
                        $price[5] = $price[2] + $price[3];
                    $estimate_start = mb_strpos($file, 'ремонта АТС');
                    if ($estimate_start) {
                        $estimate_end = mb_strlen($file);
                        $estimate = '';

                        if ($estimate_start !== false && $estimate_end !== false) {
                            $estimate = mb_substr($file, $estimate_start, $estimate_end - $estimate_start);
                            $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                            $estimate = explode('<br/>', $estimate);
                            // dd($estimate);
                            for ($i = 0; $i < count($estimate) - 1; $i++) {
                                if ($estimate[$i] == '1' || $estimate[$i] == '2')
                                    for ($j = $i; $j < count($estimate) - 1; $j++)
                                        $estimate[$j] = $estimate[$j + 1];
                            }
                            if (is_numeric(str2number($estimate[0])))
                                $price[6] = str2number($estimate[1]);
                            else
                                $price[6] = str2number($estimate[2]);
                        }
                    } else {

                        $estimate_start = mb_strripos($file, 'стоимость восстановительного ремонта');
                        $estimate_end = mb_strlen($file);
                        $estimate = '';

                        if ($estimate_start !== false && $estimate_end !== false) {
                            $estimate = mb_substr($file, $estimate_start, $estimate_end - $estimate_start);
                            $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                            $estimate = explode('<br/>', $estimate);
                            // dd($estimate);
                            for ($i = 0; $i < count($estimate) - 1; $i++) {
                                if ($estimate[$i] == '1' || $estimate[$i] == '2')
                                    for ($j = $i; $j < count($estimate) - 1; $j++)
                                        $estimate[$j] = $estimate[$j + 1];
                            }
                            if (is_numeric(str2number($estimate[0])) && str2number($estimate[0]) != 0)
                                $price[6] = str2number($estimate[0]);
                            else
                                $price[6] = str2number($estimate[1]);
                        }
                    }
                    $estimate_start = mb_strpos($file, 'стоимость устранения дефектов');
                    if (!$estimate_start)
                        $estimate_start = mb_strpos($file, 'затраты на восстановительный ремонт');
                    $estimate_end = mb_strlen($file);
                    $estimate = '';

                    if ($estimate_start !== false && $estimate_end !== false) {
                        $estimate = mb_substr($file, $estimate_start, $estimate_end - $estimate_start);
                        $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                        $estimate = explode('<br/>', $estimate);
                        // dd($estimate);
                        for ($i = 0; $i < count($estimate) - 1; $i++) {
                            if ($estimate[$i] == '1' || $estimate[$i] == '2')
                                for ($j = $i; $j < count($estimate) - 1; $j++)
                                    $estimate[$j] = $estimate[$j + 1];
                        }
                        if (is_numeric(str2number($estimate[1])) && str2number($estimate[0]) != 0)
                            $price[7] = str2number($estimate[0]);
                        else
                            $price[7] = str2number($estimate[1]);
                    }

                    // dd($price);
                } else if ($app->id_insuranceCompany == 4) {
                    // $file = str_replace("/\u{000D}", '<br/>', $file);
                    // dd($file);

                    $file = preg_replace("(\u{A0})", ' ', $file);

                    $file = str_replace("/\u{A0}/", '', $file);

                    $application_start = mb_strpos($file, 'марка модель');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    // dd($application_array);
                    $applications['CarModel'] = $application_array[1];
                    $application_start = mb_strpos($file, 'дата выпуска');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    $applications['mnf_year'] = $application_array[1];
                    $application_start = mb_strpos($file, 'гос.номер');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    $applications['gnz'] = $application_array[1];
                    $application_start = mb_strpos($file, 'VIN');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    $applications['vin'] = $application_array[1];
                    $application_start = mb_strpos($file, 'пробег');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    $applications['mileage'] = (int) $application_array[1];
                    $application_start = mb_strpos($file, 'основании акта осмотра');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    $applications['numberInsuranceCompanyApplication'] = $application_array[1];
                    $estimate_start = mb_strpos($file, 'узлов и деталей с учетом мелких деталей:');
                    $estimate = mb_substr($file, $estimate_start);
                    $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                    $estimate = explode('<br/>', $estimate);
                    $price[0] = $estimate[1];
                    $estimate_start = mb_strpos($file, 'узлов и деталей с учетом износа:');
                    $estimate = mb_substr($file, $estimate_start);
                    $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                    $estimate = explode('<br/>', $estimate);
                    $price[1] = $estimate[1];
                    $estimate_start = mb_strpos($file, 'стоимость ремонтных работ:');
                    $estimate = mb_substr($file, $estimate_start);
                    $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                    $estimate = explode('<br/>', $estimate);
                    $price[2] = $estimate[1];
                    $estimate_start = mb_strpos($file, 'стоимость окраски/контроля:');
                    $estimate = mb_substr($file, $estimate_start);
                    $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                    $estimate = explode('<br/>', $estimate);
                    $price[3] = $estimate[1];
                    $estimate_start = mb_strpos($file, 'стоимость расходных материалов:');
                    $estimate = mb_substr($file, $estimate_start);
                    $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                    $estimate = explode('<br/>', $estimate);
                    $price[4] = $estimate[1];
                    $estimate_start = mb_strpos($file, 'устранения дефектов АМТС (без учета износа):');
                    $estimate = mb_substr($file, $estimate_start);
                    $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                    $estimate = explode('<br/>', $estimate);
                    $price[6] = $estimate[1];
                    // dd($applications);
                } else if ($app->id_insuranceCompany == 7) {
                    // $file = str_replace("/\u{000D}", '<br/>', $file);
                    // dd($file);

                    $file = preg_replace("(\u{A0})", ' ', $file);

                    $file = str_replace("/\u{A0}/", '', $file);

                    $application_start = mb_strpos($file, 'транспортного средства');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    // dd($application_array);
                    $applications['CarModel'] = mb_substr($application_array[1], 0, mb_strpos($application_array[1], ','));
                    // dd($applications['CarModel']);
                    $application_start = mb_strpos($file, 'VIN');
                    $application = mb_substr($file, $application_start);
                    $application_array = explode('<br/>', $application);
                    $applications['vin'] = mb_substr($application_array[0], 4);
                    $application = mb_substr($file, 0);
                    $application_array = explode('<br/>', $application);
                    $start_pos = mb_strpos($application_array[0], '№ ');
                    if ($start_pos != 0)
                        $start_pos += 3;
                    $app_number = mb_substr($application_array[0], $start_pos, 16);
                    $applications['numberInsuranceCompanyApplication'] = $app_number;
                    // dd($applications['numberInsuranceCompanyApplication']);
                    $estimate_start = mb_strpos($file, 'узлов и деталей:');
                    $estimate = mb_substr($file, $estimate_start);
                    $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                    $estimate = explode('<br/>', $estimate);
                    $price[0] = $estimate[1];
                    $estimate_start = mb_strpos($file, 'узлов и деталей с учетом износа:');
                    $estimate = mb_substr($file, $estimate_start);
                    $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                    $estimate = explode('<br/>', $estimate);
                    $price[1] = $estimate[1];
                    $estimate_start = mb_strpos($file, 'ремонта/замены:');
                    $estimate = mb_substr($file, $estimate_start);
                    $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                    $estimate = explode('<br/>', $estimate);
                    $price[2] = $estimate[1];
                    $estimate_start = mb_strpos($file, 'окраски контроля:');
                    $estimate = mb_substr($file, $estimate_start);
                    $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                    $estimate = explode('<br/>', $estimate);
                    $price[3] = $estimate[1];
                    $estimate_start = mb_strpos($file, 'стоимость расходных материалов:');
                    $estimate = mb_substr($file, $estimate_start);
                    $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                    $estimate = explode('<br/>', $estimate);
                    $price[4] = $estimate[1];
                    $estimate_start = mb_strpos($file, 'учетом износа (восстановительные расходы) составляет:');
                    $estimate = mb_substr($file, $estimate_start);
                    $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                    $estimate = explode('<br/>', $estimate);
                    $price[7] = $estimate[1];
                    $estimate_start = mb_strpos($file, 'восстановительного ремонта составляет:');
                    $estimate = mb_substr($file, $estimate_start);
                    $estimate = preg_replace("(\u{A0})", ' ', $estimate);
                    $estimate = explode('<br/>', $estimate);
                    $price[6] = $estimate[1];
                    // dd($applications);
                }
            }
        }


        return view('application.data_select', ['applications' => $applications, 'id' => $id, 'smeta' => $smeta, 'price' => $price]);
    }
    public function data_save(Request $request, $id)
    {

        if (isset($request->price_sp_full) || isset($request->price_repair_full)) {
            $estimates = estimate::updateorcreate(
                ['id_application' => $id],
                [
                    'price_sp_full' => str2number($request->price_sp_full),
                    'price_sp_half' => str2number($request->price_sp_half),
                    'price_repair' => str2number($request->price_repair),
                    'price_paint' => str2number($request->price_paint),
                    'price_zip' => str2number($request->price_zip),
                    'price_work' => str2number($request->price_work),
                    'price_repair_full' => str2number($request->price_repair_full),
                    'price_repair_half' => str2number($request->price_repair_half)
                ]
            );
        }
        // dd($estimates);
        $application = application::findOrFail($id);
        if ($application->id_status < 2) {
            $application->id_status = 2;
            $history = new history;
            $history->id_user = Auth::user()->id;
            $history->id_status = 2;
            $history->id_application = $id;
            $history->save();
        }
        // dd($request);
        if (isset($request->CarModel)) {
            $data = $this->validate($request, [
                'CarModel' => 'required|string',
                'gnz' => 'nullable',
                'vin' => 'nullable',
                'ClientName' => 'nullable',
                'ClientPhone' => 'nullable',
                'mileage' => 'nullable',
                'vehicle_passport' => 'nullable',
                'Color' => 'nullable',
                'ClientPassport' => 'nullable',
                'DTPdate' => 'nullable',
                'id_MRK' => 'nullabler',
                'id_spm' => 'nullable',
                'dogovor' => 'nullable',
                'mnf_year' => 'nullable',
                'ub_number' => 'nullable',
                'numberInsuranceCompanyApplication' => 'nullable'
            ]);
            // dd($data);
            if (isset($data['mileage']))
                $data['mileage'] = str2number($data['mileage']);
            if (isset($data['vin']))
                $data['vin'] = str_replace(' ', '', $data['vin']);
            if (isset($data['gnz']))
                $data['gnz'] = str_replace(' ', '', $data['gnz']);


            foreach ($data as $key => $value) {
                if ($value != '') {
                    if ($key == 'DTPdate') {
                        $value = date('Y-m-d', strtotime($value));
                    }
                    $application->$key = $value;
                }
            }

            $application->save();
        }
        return redirect()->route('application', $id);
    }
    public function send_mrk($id, $id_MRK)
    {
        $user = User::find($id_MRK);
        // Mail::to('aleksmakarov5@yandex.ru')->queue(new mrknotify($id));
        $application = application::findOrFail($id);
        $spm = User::find($application->id_spm);
        $application->id_status = 3;
        $application->check_date = carbon::now();
        $application->save();
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_status = 3;
        $history->id_application = $id;
        $history->save();
        $user = user::find($id_MRK);
        $notify = new notify;
        $notify->id_user = $user->id;
        $notify->id_application = $id;
        $notify->id_text = 3;
        $notify->link = "/application/";

        $status_application = new status_application;
        $status_application->id_user = $user->id;
        $status_application->id_application = $id;
        $status_application->is_readed = false;
        $status_application->save();
        $notification = DB::table('notifies')->where('notifies.id', '=', $notify->id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
            ->select('notifies.*', 'text_notifies.text')->first();
        broadcast(new MessageSent($notification));
        if ($user->telegram_notify) {
            $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
            $status = ApplicationStatus::findOrFail(
                $application->id_status
            )->StatusName;
            $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                ->keyboard(
                    Keyboard::make()
                        ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                )
                ->send();
            $notify->id_message = json_decode($ch)->result->message_id;
        }

        $notify->save();
        $notify = new notify;
        $notify->id_user = $spm->id;
        $notify->id_application = $id;
        $notify->id_text = 3;
        $notify->link = "/application/";

        $status_application = new status_application;
        $status_application->id_user = $spm->id;
        $status_application->id_application = $id;
        $status_application->is_readed = false;
        $status_application->save();
        $notification = DB::table('notifies')->where('notifies.id', '=', $notify->id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
            ->select('notifies.*', 'text_notifies.text')->first();
        broadcast(new MessageSent($notification));
        if ($spm->telegram_notify) {
            $new_chat = TelegraphChat::where('chat_id', '=', $spm->telegram_id)->first();
            $status = ApplicationStatus::findOrFail(
                $application->id_status
            )->StatusName;
            $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i> необходимо произвести проценку")
                ->keyboard(
                    Keyboard::make()
                        ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                )
                ->send();
            $notify->id_message = json_decode($ch)->result->message_id;
        }

        $notify->save();
        return redirect()->route('applications');
    }
    public function change_status($id, $variant)
    {
        $application = application::findOrFail($id);
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_application = $id;
        switch ($variant) {
            case 1:
                $application->id_status = 4;
                // dd ($application->id_status);

                $history->id_status = 4;


                // Mail::to('aleksmakarov5@yandex.ru')->queue(new mrknotify($id));
                break;
            case 2:
                $application->id_status = 8;
                $history->id_status = 8;

                break;
            case 3:
                $application->id_status = 7;
                $history->id_status = 7;

                break;
            case 4:
                $application->id_status = 6;
                $history->id_status = 6;
                break;
        }
        $application->save();
        $history->save();
        $notify_id = 0;
        $status_group = ApplicationStatus::findOrFail($application->id_status)->current_group;
        switch ($status_group) {
            case 2:
                $users = user::where('GroupId', '=', $status_group)->get()->all();
                foreach ($users as $user) {
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 3;
                    $notify->link = "/application/";


                    $notify_id = $notify->id;

                    $status_application = new status_application;
                    $status_application->id_user = $user->id;
                    $status_application->id_application = $id;
                    $status_application->is_readed = false;
                    $status_application->save();
                    if ($user->telegram_notify) {
                        $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                        $status = ApplicationStatus::findOrFail(
                            $application->id_status
                        )->StatusName;
                        $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                            ->keyboard(
                                Keyboard::make()
                                    ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                            )
                            ->send();
                        $notify->id_message = json_decode($ch)->result->message_id;
                    }

                    $notify->save();
                }
                break;
            case 3:
                $users = user::where('GroupId', '=', $status_group)->get()->all();
                foreach ($users as $user) {
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 3;
                    $notify->link = "/application/";


                    $notify_id = $notify->id;
                    $status_application = new status_application;
                    $status_application->id_user = $user->id;
                    $status_application->id_application = $id;
                    $status_application->is_readed = false;
                    $status_application->save();
                    if ($user->telegram_notify) {
                        $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                        $status = ApplicationStatus::findOrFail(
                            $application->id_status
                        )->StatusName;
                        $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                            ->keyboard(
                                Keyboard::make()
                                    ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                            )
                            ->send();
                        $notify->id_message = json_decode($ch)->result->message_id;
                    }

                    $notify->save();
                }
                break;
            case 5:
                $user = user::find($application->id_MRK);

                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 3;
                $notify->link = "/application/";


                $notify_id = $notify->id;
                $status_application = new status_application;
                $status_application->id_user = $user->id;
                $status_application->id_application = $id;
                $status_application->is_readed = false;
                $status_application->save();
                if ($user->telegram_notify) {
                    $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                    $status = ApplicationStatus::findOrFail(
                        $application->id_status
                    )->StatusName;
                    $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                        ->keyboard(
                            Keyboard::make()
                                ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                        )
                        ->send();
                    $notify->id_message = json_decode($ch)->result->message_id;
                }

                $notify->save();

                break;
            case 6:
                $user = user::find($application->id_spm);

                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 3;
                $notify->link = "/application/";


                $notify_id = $notify->id;
                $status_application = new status_application;
                $status_application->id_user = $user->id;
                $status_application->id_application = $id;
                $status_application->is_readed = false;
                $status_application->save();
                if ($user->telegram_notify) {
                    $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                    $status = ApplicationStatus::findOrFail(
                        $application->id_status
                    )->StatusName;
                    $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                        ->keyboard(
                            Keyboard::make()
                                ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                        )
                        ->send();
                    $notify->id_message = json_decode($ch)->result->message_id;
                }

                $notify->save();

                break;
            case 8:
                $users = user::where('GroupId', '=', $status_group)->get()->all();
                foreach ($users as $user) {
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 3;
                    $notify->link = "/application/";


                    $notify_id = $notify->id;
                    $status_application = new status_application;
                    $status_application->id_user = $user->id;
                    $status_application->id_application = $id;
                    $status_application->is_readed = false;
                    $status_application->save();
                    if ($user->telegram_notify) {
                        $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                        $status = ApplicationStatus::findOrFail(
                            $application->id_status
                        )->StatusName;
                        $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                            ->keyboard(
                                Keyboard::make()
                                    ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                            )
                            ->send();
                        $notify->id_message = json_decode($ch)->result->message_id;
                    }

                    $notify->save();
                }
                break;
        }
        $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
            ->select('notifies.*', 'text_notifies.text')->first();
        broadcast(new MessageSent($notification));

        return redirect()->route('applications');
    }
    public function change_status_km($id, $variant)
    {
        $application = application::findOrFail($id);
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_application = $id;
        switch ($variant) {
            case 1: {
                    $application->id_status = 2;
                    $history->id_status = 2;
                    // Mail::to('aleksmakarov5@yandex.ru')->queue(new mrknotify($id));
                    $application->save();
                    $history->save();
                    return redirect()->route('applications');
                }
            case 2: {
                    $application->id_status = 5;

                    $application->save();

                    // Mail::to('aleksmakarov5@yandex.ru')->queue(new mrknotify($id));

                    return redirect()->route('answer_fail_sel', $id);
                }
        }
        $notify_id = 0;
        $status_group = ApplicationStatus::findOrFail($application->id_status)->current_group;
        switch ($status_group) {
            case 2:
                $users = user::where('GroupId', '=', $status_group)->get()->all();
                foreach ($users as $user) {
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 3;
                    $notify->link = "/application/";


                    $notify_id = $notify->id;
                    $status_application = new status_application;
                    $status_application->id_user = $user->id;
                    $status_application->id_application = $id;
                    $status_application->is_readed = false;
                    $status_application->save();
                    if ($user->telegram_notify) {
                        $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                        $status = ApplicationStatus::findOrFail(
                            $application->id_status
                        )->StatusName;
                        $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                            ->keyboard(
                                Keyboard::make()
                                    ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                            )
                            ->send();
                        $notify->id_message = json_decode($ch)->result->message_id;
                    }

                    $notify->save();
                }
                break;
            case 3:
                $users = user::where('GroupId', '=', $status_group)->get()->all();
                foreach ($users as $user) {
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 3;
                    $notify->link = "/application/";


                    $notify_id = $notify->id;
                    $status_application = new status_application;
                    $status_application->id_user = $user->id;
                    $status_application->id_application = $id;
                    $status_application->is_readed = false;
                    $status_application->save();
                    if ($user->telegram_notify) {
                        $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                        $status = ApplicationStatus::findOrFail(
                            $application->id_status
                        )->StatusName;
                        $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                            ->keyboard(
                                Keyboard::make()
                                    ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                            )
                            ->send();
                        $notify->id_message = json_decode($ch)->result->message_id;
                    }

                    $notify->save();
                }
                break;
            case 5:
                $user = user::find($application->id_MRK);

                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 3;
                $notify->link = "/application/";


                $notify_id = $notify->id;
                $status_application = new status_application;
                $status_application->id_user = $user->id;
                $status_application->id_application = $id;
                $status_application->is_readed = false;
                $status_application->save();
                if ($user->telegram_notify) {
                    $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                    $status = ApplicationStatus::findOrFail(
                        $application->id_status
                    )->StatusName;
                    $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                        ->keyboard(
                            Keyboard::make()
                                ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                        )
                        ->send();
                    $notify->id_message = json_decode($ch)->result->message_id;
                }

                $notify->save();

                break;
            case 6:
                $user = user::find($application->id_spm);

                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 3;
                $notify->link = "/application/";


                $notify_id = $notify->id;
                $status_application = new status_application;
                $status_application->id_user = $user->id;
                $status_application->id_application = $id;
                $status_application->is_readed = false;
                $status_application->save();
                if ($user->telegram_notify) {
                    $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                    $status = ApplicationStatus::findOrFail(
                        $application->id_status
                    )->StatusName;
                    $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                        ->keyboard(
                            Keyboard::make()
                                ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                        )
                        ->send();
                    $notify->id_message = json_decode($ch)->result->message_id;
                }

                $notify->save();

                break;
            case 8:
                $users = user::where('GroupId', '=', $status_group)->get()->all();
                foreach ($users as $user) {
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 3;
                    $notify->link = "/application/";


                    $notify_id = $notify->id;
                    $status_application = new status_application;
                    $status_application->id_user = $user->id;
                    $status_application->id_application = $id;
                    $status_application->is_readed = false;
                    $status_application->save();
                    if ($user->telegram_notify) {
                        $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                        $status = ApplicationStatus::findOrFail(
                            $application->id_status
                        )->StatusName;
                        $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
                            ->keyboard(
                                Keyboard::make()
                                    ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
                            )
                            ->send();
                    }
                    $notify->id_message = json_decode($ch)->result->message_id;
                }

                $notify->save();
                break;
        }
        $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
            ->select('notifies.*', 'text_notifies.text')->first();
        broadcast(new MessageSent($notification));
    }

    public function answer_fail_sel($id)
    {



        $fail_vars = DB::table('failure_vars')->get();

        return view('application.answer_fail_select', ['id' => $id], ['fail_vars' => $fail_vars->all()]);
    }
    public function answer_fail_save(Request $request, $id)
    {

        $variants = $request->variant;
        // dd($variants);
        $application = application::findOrFail($id);
        $application->comment_false = $request->comment;
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_status = 5;
        $history->id_application = $id;
        $history->comment = $request->comment;
        $history->save();
        $application->save();
        if (is_array($variants)) {
            foreach ($variants as $key => $value) {
                $failure = new failure;
                $failure->id_application = $id;
                $failure->id_var = $value;
                $failure->save();
            }
        }
        return redirect()->route('applications');
    }

    public function answer_ic(Request $request, $id)
    {
        $application = application::findOrFail($id);
        $fail_vars = DB::table('failures')
            ->where('id_application', '=', $id)
            ->join('failure_vars', 'failures.id_var', '=', 'failure_vars.id')
            ->select('failures.*', 'failure_vars.variant')
            ->get();
        try {
            $message_id = message::where('application_id', '=', $id)->first()->id;
            $subject =
                message::where('application_id', '=', $id)->first()->subject;

            $message_id = message::where('application_id', '=', $id)->first()->id;
            $to = DB::table('addresses')
                ->where('message_id', '=', $message_id)
                ->where('type', '=', 'from')
                ->first()
                ->email;
        } catch (\Exception $e) {
            $subject = "";
            $to = "";
        }
        return view('application.answer_ic', ['subject' => $subject, 'to' => $to, 'fail_vars' => $fail_vars->all(), 'application' => $application, 'id' => $id]);
    }
    public function answer_ic_sent(Request $request, $id)
    {
        $application = application::findOrFail($id);
        $data["email"] = $request->to;
        $data["title"] = $request->subject;
        $data["body"] = $request->message;
        $data["from"] = OurCompany::where('id', '=', $application->our_company)->first()->our_email;
        $data["name"] = OurCompany::where('id', '=', $application->our_company)->first()->our_name;
        $data["mailer"] = OurCompany::where('id', '=', $application->our_company)->first()->our_mailer;
        $files = [];
        if (isset($request->location[0]))
            for ($i = 0; $i < count($request->location); $i++) {
                $files[] = public_path('/storage/files/' . $id . '/' . $request->location[$i]);
            }

        Mail_send::mailer($data['mailer'])->send('email.' . $data['mailer'], $data, function ($message) use ($data, $files) {
            $message->to($data["email"], $data["name"])
                ->from($data['from'], $data['name'])
                ->subject($data["title"]);
            foreach ($files as $file) {
                $message->attach($file);
            }
        });
        $application->id_status = 25;
        $history = new history;
        $messages = new message();
        $messages->mailbox_id = $application->our_company;
        $messages->type = 1;
        $messages->application_id = $id;
        $messages->body_html = $request->message;
        $messages->message_date = date('Y-m-d H:i:s');


        $messages->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'from';
        $address->email = $data["from"];
        $address->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'to';
        $address->email = $data["email"];
        $address->save();
        $history->id_user = Auth::user()->id;
        $history->id_application = $id;
        $history->id_status = 25;
        $history->save();
        $application->save();
        return redirect()
            ->route('archive');
    }
    public function answer_true_sel($id)
    {
        $true_vars = DB::table('true_vars')->get();
        return view('application.answer_true_select', ['id' => $id], ['true_vars' => $true_vars->all()]);
    }
    public function answer_true_save(Request $request, $id)
    {

        $variants = $request->variant;
        $application = application::findOrFail($id);
        $application->comment_true = $request->comment;
        $application->id_status = 10;
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_status = 10;
        $history->id_application = $id;
        $history->comment = $request->comment;
        $history->save();

        $application->save();

        // dd($variants);
        if (is_array($variants))
            foreach ($variants as $key => $value) {
                $tru = new tru;
                $tru->id_application = $id;
                $tru->id_var = $value;
                $tru->save();
            }


        return redirect()->route('applications');
    }

    public function answer_ic_true(Request $request, $id)
    {
        $application = application::findOrFail($id);
        $message_id = message::where('application_id', '=', $id)->first()->id;
        $subject =
            message::where('application_id', '=', $id)->first()->subject;

        $message_id = message::where('application_id', '=', $id)->first()->id;
        $to = DB::table('addresses')
            ->where('message_id', '=', $message_id)
            ->where('type', '=', 'from')
            ->first()
            ->email;
        $true_vars = DB::table('trus')
            ->where('id_application', '=', $id)
            ->join('true_vars', 'trus.id_var', '=', 'true_vars.id')
            ->select('trus.*', 'true_vars.variant')
            ->get();
        $users = DB::table('users')->get();
        $service = service::findOrFail($application->id_service);
        $attachments = attachment::where('id_application', '=', $id)->get()->all();

        return view('application.answer_ic_true', ['subject' => $subject, 'true_vars' => $true_vars->all(), 'application' => $application, 'id' => $id, 'to' => $to, 'service' => $service, 'attach' => $attachments]);
    }

    public function answer_ic_true_sent(Request $request, $id)
    {
        $application = application::findOrFail($id);
        $data["email"] = $request->to;
        $data["title"] = $request->subject;
        $data["body"] = $request->message;
        $data["from"] = OurCompany::where('id', '=', $application->our_company)->first()->our_email;
        $data["name"] = OurCompany::where('id', '=', $application->our_company)->first()->our_name;
        $data["mailer"] = OurCompany::where('id', '=', $application->our_company)->first()->our_mailer;
        $files = [];
        if (isset($request->location[0]))
            for ($i = 0; $i < count($request->location); $i++) {
                $files[] = public_path('/storage/files/' . $id . '/' . $request->location[$i]);
            }

        Mail_send::mailer($data['mailer'])->send('email.' . $data['mailer'], $data, function ($message) use ($data, $files) {
            $message->to($data["email"], $data["name"])
                ->from($data['from'], $data['name'])
                ->subject($data["title"]);
            foreach ($files as $file) {
                $message->attach($file);
            }
        });



        $application->id_status = 11;
        $application->check_date = carbon::now();
        $history = new history;

        $messages = new message();
        $messages->mailbox_id = $application->our_company;
        $messages->type = 1;
        $messages->application_id = $id;
        $messages->subject = message::where('application_id', '=', $id)->first()->subject;
        $messages->body_html = $request->message;
        $messages->message_date = date('Y-m-d H:i:s');


        $messages->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'from';
        $address->email = $data["from"];
        $address->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'to';
        $address->email = $request->to;
        $address->save();
        $history->id_user = Auth::user()->id;
        $history->id_application = $id;
        $history->id_status = 11;
        $history->save();
        $application->save();
        return redirect()
            ->route('applications');
    }

    public function change_st($id, $status_id)
    {
        $application = application::findOrFail($id);
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_application = $id;


        if ($status_id < 100) {
            $application->check_date = carbon::now();
            $application->id_status = $status_id;
            if ($status_id == 14) {


                $today = WorkCalendar::now();
                $today->addWorkDays(30);
                $application->end_date = $today;
                $application->view_date = null;

                $application->start_date = carbon::now();
                if ($application->finance_statuses_id < 3) {
                    $application->finance_statuses_id = 2;

                    $application->ic_answer_date = carbon::now();
                }


                // dd($application);
                // dd($today, $application->end_date);
            }
            if ($status_id == 13) {
                $attachments = attachment::where('id_application', '=', $id)->get()->all();
                foreach ($attachments as $attachment) {
                    if ($attachment->id_status == 9 || $attachment->id_status == 10) {
                        $attachment->id_status = null;
                        $attachment->save();
                    }
                }
            }
            if ($status_id == 12) {
                $application->napr_date = carbon::now();
            }
            if ($status_id == 11) {
                $application->check_date = date('Y-m-d H:i:s');
            }


            // dd ($application->id_status);
            // $data["email"] = "aleksmakarov5@yandex.ru";
            // $data["title"] = 'По заявке №' . $id . ' изменен статус';
            // $data["body"] = 'По заявке №' . $id . ' ' . Auth::user()->name . ' установил статус; "' . ApplicationStatus::findOrFail($status_id)->StatusName . ' "';
            // $data["from"] = OurCompany::where('id', '=', $application->our_company)->first()->our_email;
            // $data["name"] = OurCompany::where('id', '=', $application->our_company)->first()->our_name;
            // $data["mailer"] = OurCompany::where('id', '=', $application->our_company)->first()->our_mailer;

            // Mail_send::mailer($data['mailer'])->send('email.'. $data['mailer'], $data, function ($message) use ($data) {
            //     $message->to($data["email"], $data["email"])
            //         ->from($data['from'], $data['name'])
            //         ->subject($data["title"]);
            // });

        } else {
            $status_id = $status_id - 100;
            $application->ic_answer = false;
            $application->finance_statuses_id = $status_id;
            $application->ic_answer_date = date('Y-m-d H:i:s');

            $history->is_fin = true;
        }
        $history->id_status = $status_id;


        $application->save();
        $history->save();
        $notify_id = 0;
        $status_group = ApplicationStatus::findOrFail($status_id)->current_group;
        // dd($status_group);
        // if ($status_group == 2) {
        //     $users = user::where('GroupId', '=', $status_group)->get()->all();
        //     // dd($users);

        //     foreach ($users as $user) {
        //         $notify = new notify;
        //         $notify->id_user = $user->id;
        //         $notify->id_application = $id;
        //         $notify->id_text = 3;
        //         $notify->link = "/application/";

        //         $notify->save();
        //         $notify_id = $notify->id;
        //         $status_application = new status_application;
        //         $status_application->id_user = $user->id;
        //         $status_application->id_application = $id;
        //         $status_application->is_readed = false;
        //         $status_application->save();
        //     }
        // }
        // if ($status_group == 3) {
        //     $users = user::where('GroupId', '=', $status_group)->get()->all();
        //     // dd($users);

        //     foreach ($users as $user) {
        //         $notify = new notify;
        //         $notify->id_user = $user->id;
        //         $notify->id_application = $id;
        //         $notify->id_text = 3;
        //         $notify->link = "/application/";


        //         $notify_id = $notify->id;
        //         $status_application = new status_application;
        //         $status_application->id_user = $user->id;
        //         $status_application->id_application = $id;
        //         $status_application->is_readed = false;
        //         $status_application->save();
        //         if ($user->telegram_notify) {
        //             $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
        //             $status = ApplicationStatus::findOrFail(
        //                 $application->id_status
        //             )->StatusName;
        //             $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
        //                 ->keyboard(
        //                     Keyboard::make()
        //                         ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
        //                 )
        //                 ->send();
        //             $notify->id_message = json_decode($ch)->result->message_id;
        //         }

        //         $notify->save();
        //     }
        // }
        // if ($status_group == 5) {
        //     $user = user::find($application->id_MRK);
        //     if ($user) {
        //         $notify = new notify;
        //         $notify->id_user = $user->id;
        //         $notify->id_application = $id;
        //         $notify->id_text = 3;
        //         $notify->link = "/application/";

        //         $notify_id = $notify->id;

        //         $status_application = new status_application;
        //         $status_application->id_user = $user->id;
        //         $status_application->id_application = $id;
        //         $status_application->is_readed = false;
        //         $status_application->save();
        //         if ($user->telegram_notify) {
        //             $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
        //             $status = ApplicationStatus::findOrFail(
        //                 $application->id_status
        //             )->StatusName;
        //             $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
        //                 ->keyboard(
        //                     Keyboard::make()
        //                         ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
        //                 )
        //                 ->send();
        //             $notify->id_message = json_decode($ch)->result->message_id;
        //         }

        //         $notify->save();
        //     }
        // }
        // if ($status_group == 6) {
        //     $user = user::find($application->id_spm);


        //     $notify = new notify;
        //     $notify->id_user = $user->id;
        //     $notify->id_application = $id;
        //     $notify->id_text = 3;
        //     $notify->link = "/application/";


        //     $notify_id = $notify->id;
        //     $status_application = new status_application;
        //     $status_application->id_user = $user->id;
        //     $status_application->id_application = $id;
        //     $status_application->is_readed = false;
        //     $status_application->save();
        //     if ($user->telegram_notify) {
        //         $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
        //         $status = ApplicationStatus::findOrFail(
        //             $application->id_status
        //         )->StatusName;
        //         $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
        //             ->keyboard(
        //                 Keyboard::make()
        //                     ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
        //             )
        //             ->send();
        //         $notify->id_message = json_decode($ch)->result->message_id;
        //     }

        //     $notify->save();
        // }
        // if ($status_group == 8) {
        //     $users = user::where('GroupId', '=', $status_group)->get()->all();


        //     foreach ($users as $user) {
        //         $notify = new notify;
        //         $notify->id_user = $user->id;
        //         $notify->id_application = $id;
        //         $notify->id_text = 3;
        //         $notify->link = "/application/";


        //         $notify_id = $notify->id;
        //         $status_application = new status_application;
        //         $status_application->id_user = $user->id;
        //         $status_application->id_application = $id;
        //         $status_application->is_readed = false;
        //         $status_application->save();
        //         if ($user->telegram_notify) {
        //             $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
        //             $status = ApplicationStatus::findOrFail(
        //                 $application->id_status
        //             )->StatusName;
        //             $ch = $new_chat->html("По заявке {$id} изменен статус\nТекущий статус - <i>{$status}</i>")
        //                 ->keyboard(
        //                     Keyboard::make()
        //                         ->buttons([Button::make("Открыть заявку")->webApp("https://mandarin-portal.ru/application/{$id}"),])
        //                 )
        //                 ->send();
        //             $notify->id_message = json_decode($ch)->result->message_id;
        //         }

        //         $notify->save();
        //     }
        // }

        // $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
        //     ->select('notifies.*', 'text_notifies.text')->first();
        // // dd($notification);
        // broadcast(new MessageSent($notification));

        return redirect()->back();
    }

    public function answer_ic_mrk(Request $request, $id)
    {
        $application = application::findOrFail($id);
        $message_id = message::where('application_id', '=', $id)->first()->id;
        $subject =
            message::where('application_id', '=', $id)->first()->subject;
        $to = DB::table('addresses')
            ->where('message_id', '=', $message_id)
            ->where('type', '=', 'from')
            ->first()
            ->email;

        $users = DB::table('users')->get();
        $attachments = attachment::where('id_application', '=', $id)->get()->all();

        return view('application.answer_ic_mrk', ['users' => $users->all(), 'application' => $application, 'id' => $id, 'to' => $to, 'attach' => $attachments, 'subject' => $subject]);
    }
    public function answer_ic_trouble(Request $request, $id)
    {
        $application = application::findOrFail($id);
        $subject = '';
        $application = application::findOrFail($id);
        $subject = '';
        $to = '';
        if ($application->id_insuranceCompany == 3) {
            $gnz = $application->gnz;
            if (strpos($gnz, 'ru'))
                $gnz = substr($gnz, 0, strpos($gnz, 'ru'));
            if (strpos($gnz, 'RU'))
                $gnz = substr($gnz, 0, strpos($gnz, 'RU'));
            $to = DB::table('insurance_companies')
                ->where('id', '=', $application->id_insuranceCompany)
                ->first()
                ->trouble_email;
            $subject = $application->numberInsuranceCompanyApplication . ' ' . $gnz;
        } else {
            $message_id = message::where('application_id', '=', $id)->first()->id;
            $subject =
                message::where('application_id', '=', $id)->first()->subject;
            $to = DB::table('addresses')
                ->where('message_id', '=', $message_id)
                ->where('type', '=', 'from')
                ->first()
                ->email;
        }



        $users = DB::table('users')->get();
        $attachments = attachment::where('id_application', '=', $id)->get()->all();

        return view('application.answer_ic_mrk', ['users' => $users->all(), 'application' => $application, 'id' => $id, 'to' => $to, 'attach' => $attachments, 'subject' => $subject]);
    }

    public function answer_ic_mrk_sent(Request $request, $id)
    {
        // dd($request);

        // Mail::to('aleksmakarov5@yandex.ru')->queue(new ic_answer($id, $request->subject, $request->message, $request->location));
        $application = application::findOrFail($id);
        $data["email"] = $request->to;
        $data["title"] = $request->subject;
        $data["body"] = $request->message;
        $data["from"] = OurCompany::where('id', '=', $application->our_company)->first()->our_email;
        $data["name"] = OurCompany::where('id', '=', $application->our_company)->first()->our_name;
        $data["mailer"] = OurCompany::where('id', '=', $application->our_company)->first()->our_mailer;
        $files = [];
        if (isset($request->location[0]))
            for ($i = 0; $i < count($request->location); $i++) {
                $files[] = public_path('/storage/files/' . $id . '/' . $request->location[$i]);
            }

        Mail_send::mailer($data['mailer'])->send('email.' . $data['mailer'], $data, function ($message) use ($data, $files) {
            $message->to($data["email"], $data["name"])
                ->from($data['from'], $data['name'])
                ->subject($data["title"]);
            foreach ($files as $file) {
                $message->attach($file);
            }
        });
        // $application->id_status = 19;
        $application->check_date = carbon::now();
        $history = new history;

        $messages = new message();
        $messages->mailbox_id = $application->our_company;
        $messages->type = 1;
        $messages->application_id = $id;
        $messages->subject = message::where('application_id', '=', $id)->first()->subject;
        $messages->body_html = $request->message;
        $messages->message_date = date('Y-m-d H:i:s');



        $messages->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'from';
        $address->email = $data["from"];
        $address->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'to';
        $address->email = $request->to;
        $address->save();
        $history->id_user = Auth::user()->id;
        $history->id_application = $id;
        $history->id_status = 19;
        $history->save();
        $application->save();
        return redirect()->route('applications');
    }

    public function answer_ic_sch(Request $request, $id)
    {
        $application = application::findOrFail($id);
        $message_id = message::where('application_id', '=', $id)->first()->id;
        $subject =
            message::where('application_id', '=', $id)->first()->subject;
        $to = DB::table('addresses')
            ->where('message_id', '=', $message_id)
            ->where('type', '=', 'from')
            ->first()
            ->email;


        $attachments = attachment::where('id_application', '=', $id)->get()->all();

        return view('application.answer_ic_sch', ['application' => $application, 'id' => $id, 'to' => $to, 'attach' => $attachments, 'subject' => $subject]);
    }

    public function answer_ic_sch_sent(Request $request, $id)
    {
        // dd($request);

        // Mail::to('aleksmakarov5@yandex.ru')->queue(new ic_answer($id, $request->subject, $request->message, $request->location));
        $application = application::findOrFail($id);
        $data["email"] = $request->to;
        $data["title"] = $request->subject;
        $data["body"] = $request->message;
        $data["from"] = OurCompany::where('id', '=', $application->our_company)->first()->our_email;
        $data["name"] = OurCompany::where('id', '=', $application->our_company)->first()->our_name;
        $data["mailer"] = OurCompany::where('id', '=', $application->our_company)->first()->our_mailer;
        $files = [];
        if (isset($request->location[0]))
            for ($i = 0; $i < count($request->location); $i++) {
                $files[] = public_path('/storage/files/' . $id . '/' . $request->location[$i]);
            }

        Mail_send::mailer($data['mailer'])->send('email.' . $data['mailer'], $data, function ($message) use ($data, $files) {
            $message->to($data["email"], $data["name"])
                ->bcc($data["from"])
                ->from($data['from'], $data['name'])
                ->subject($data["title"]);
            foreach ($files as $file) {
                $message->attach($file);
            }
        });
        // $application->id_status = 22;
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_status = 22;
        $history->id_application = $id;
        $history->comment = $request->comment;
        $history->save();
        $messages = new message();
        $messages->application_id = $id;
        $messages->mailbox_id = $application->our_company;
        $messages->type = 1;

        $messages->subject = message::where('application_id', '=', $id)->first()->subject;
        $messages->body_html = $request->message;
        $messages->message_date = date('Y-m-d H:i:s');


        $messages->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'from';
        $address->email = $data["from"];
        $address->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'to';
        $address->email = $request->to;
        $address->save();
        // $history->id_user = Auth::user()->id;
        // $history->id_application = $id;
        // $history->id_status = 22;
        // $history->save();
        $application->save();
        return redirect()->route('applications');
    }

    public function test($id, $id_mes)
    {
        $notify = new notify;
        $notify->id_user = 2;
        $notify->id_application = $id;
        $notify->id_text = $id_mes;
        switch ($id_mes) {

            case 1:
                $notify->link = "/mail/";
                // broadcast(new MessageSent("Получено письмо от страховой по заявке №", $id, "/mail/"));
                break;
            case 2:
                $notify->link = "/chat/";
                // broadcast(new MessageSent("Получено сообщение в чате заявки №", $id, "/chat/"));
                break;
            case 3:
                $notify->link = "/application/";
                // broadcast(new MessageSent("Изменен статус по заявке №", $id, "/application/"));
                break;
        }
        $notify->save();
        // $notify->text = text_notify::findOrFail($notify->id_text)->first()->text;


        $notification = DB::table('notifies')->where('notifies.id', '=', $notify->id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
            ->select('notifies.*', 'text_notifies.text')->first();
        broadcast(new MessageSent($notification));
    }
    public function delete_message($id)
    {
        $message = message::findOrFail($id);
        // $messages = message::where('id', '>', '828')
        //     ->where('id', '<', '1193')
        //     ->get()->all();
        // foreach ($messages as $message) {
        $attachments = attachment::where('message_id', '=', $message->id)->get()->all();
        // dd($attachments);
        foreach ($attachments as $attachment) {
            try {
                unlink('storage/files/' . $attachment->id_application . '/' . $attachment->location);
            } catch (\Exception $e) {
                // dd($e);
            }
            $attachment->delete();
        }
        $message->delete();
        // }

        return redirect()->back();
    }
    public function add_mail($type, $id, $message_id)
    {
        $applications = application::get()->all();
        return view('application.add_mail', [
            'applications' => $applications,
            'message_id' => $message_id,
            'type' => $type,
            'id' => $id,
        ]);
    }
    public function new_mail(Request $request, $type, $id, $message_id, $id_application)
    {
        $our_company = $request->get('our_company');
        if ($our_company) {
            $application = new application;
            $application->our_company = $our_company;
            $application->numberInsuranceCompanyApplication = " ";
            $application->CarModel = " ";
            $application->id_status = 1;
            $application->id_insuranceCompany = 1;
            $application->save();
            $id_application = $application->id;
        }
        $message = message::findOrFail($message_id);
        $message->application_id = $id_application;
        $message->save();
        $attachments = attachment::where('message_id', '=', $message_id)->get();
        // dd($attachments);
        $dir = 'storage/files/' . $id_application;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        foreach ($attachments as $attachment) {
            $attachment->id_application = $id_application;
            $attachment->save();
            $file = 'storage/files/temp/' . $attachment->location;
            // dd($file);
            rename($file, 'storage/files/' . $id_application . '/' . $attachment->location);
        }

        return redirect()->route('mail_app', ['id' => $id_application]);
    }
    public function combine($id_comp, $id)
    {
        $applications = application::where('our_company', '=', $id_comp)->get()->all();
        return view('application.combine', [
            'applications' => $applications,
            'id' => $id,
        ]);
    }

    public function combine_fin($id_prev, $id_next)
    {
        // Move related data from $id_prev to $id_next//+
        $attachments = attachment::where('id_application', '=', $id_prev)->get();
        foreach ($attachments as $attachment) {
            $attachment->id_application = $id_next;
            $attachment->save();
        }
        $chats = chat::where('id_application', '=', $id_prev)->get(); //-
        foreach ($chats as $chat) { //-
            $chat->id_application = $id_next; //-
            $chat->save(); //-
        } //-
        $estimates = estimate::where('id_application', '=', $id_prev)->get(); //-
        foreach ($estimates as $estimate) { //-
            $estimate->id_application = $id_next; //-
            $estimate->save(); //-
        } //-
        $failures = failure::where('id_application', '=', $id_prev)->get(); //-
        foreach ($failures as $failure) { //-
            $failure->id_application = $id_next; //-
            $failure->save(); //-
        } //-
        $histories = history::where('id_application', '=', $id_prev)->get(); //-
        foreach ($histories as $history) { //-
            $history->id_application = $id_next; //-
            $history->save(); //-
        } //-
        $messages = message::where('application_id', '=', $id_prev)->get(); //-
        foreach ($messages as $message) { //-
            $message->application_id = $id_next; //-
            $message->save(); //-
        } //-
        $notifies = notify::where('id_application', '=', $id_prev)->get(); //-
        foreach ($notifies as $notify) { //-
            $notify->id_application = $id_next; //-
            $notify->save(); //-
        } //-
        $spare_parts = SparePart::where('id_application', '=', $id_prev)->get(); //-
        foreach ($spare_parts as $spare_part) { //-
            $spare_part->id_application = $id_next; //-
            $spare_part->save(); //-
        } //-
        $status_applications = //-
            status_application::where('id_application', '=', $id_prev)->get(); //-
        foreach ($status_applications as $status_application) { //-
            $status_application->id_application = $id_next; //-
            $status_application->save(); //-
        } //-
        $status_chats = //-
            status_chat::where('id_application', '=', $id_prev)->get(); //-
        foreach ($status_chats as $status_chat) { //-
            $status_chat->id_application = $id_next; //-
            $status_chat->save(); //-
        } //-
        $status_messages = //-
            status_message::where('id_application', '=', $id_prev)->get(); //-
        foreach ($status_messages as $status_message) { //-
            $status_message->id_application = $id_next; //-
            $status_message->save(); //-
        } //-
        $troubleshootings = //-
            troubleshooting::where('id_application', '=', $id_prev)->get(); //-
        foreach ($troubleshootings as $troubleshooting) { //-
            $troubleshooting->id_application = $id_next; //-
            $troubleshooting->save(); //-
        } //-
        $trus = //-
            tru::where('id_application', '=', $id_prev)->get(); //-
        foreach ($trus as $tru) { //-
            $tru->id_application = $id_next; //-
            $tru->save(); //-
        } //-
        $works = //-
            work::where('id_application', '=', $id_prev)->get(); //-
        foreach ($works as $work) { //-
            $work->id_application = $id_next; //-
            $work->save(); //-
        } //-

        $dir_prev = 'storage/files/' . $id_prev;
        $dir_next = 'storage/files/' . $id_next;
        if (is_dir($dir_prev)) {
            $files = scandir($dir_prev);
            if (!is_dir($dir_next)) {
                mkdir($dir_next, 0777, true);
            }

            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    if (!is_dir($file)) {
                        rename($dir_prev . '/' . $file, $dir_next . '/' . $file);
                    } else {
                        $files1 = scandir($dir_prev . '/' . $file);
                        foreach ($files1 as $file1) {
                            if ($file1 != '.' && $file1 != '..') {
                                rename($dir_prev . '/' . $file . '/' . $file1, $dir_next . '/' . $file . '/' . $file1);
                            }
                        }
                        try {
                            rmdir($dir_prev . '/' . $file);
                        } catch (\Exception $e) {
                            // dd($e);//-
                            // Handle exception//+
                        }
                    }
                }
            }
            try {
                rmdir($dir_prev);
            } catch (\Exception $e) {
                // dd($e);//-
                // Handle exception//+
            }
        }

        $application = application::findOrFail($id_prev);
        $application->delete();


        return redirect()->route('application', ['id' => $id_next]);
    }
    public function move_mail_index($id_prev, $id, $message_id)
    {
        $applications = application::get()->all();
        return view('application.move_mail', [
            'applications' => $applications,
            'message_id' => $message_id,
            'id' => $id,
            'id_prev' => $id_prev
        ]);
    }
    public function move_mail(Request $request, $id_prev, $message_id, $id_next)
    {
        $our_company = $request->get('our_company');
        if ($our_company) {
            $application = new application;
            $application->our_company = $our_company;
            $application->numberInsuranceCompanyApplication = " ";
            $application->CarModel = " ";
            $application->id_status = 1;
            $application->id_insuranceCompany = 1;
            $application->save();
            $id_next = $application->id;
        }

        $message = message::findOrFail($message_id);
        $message->application_id = $id_next;
        $message->save();
        $attachments = attachment::where('message_id', '=', $message_id)->get();
        // dd($attachments);
        $dir = 'storage/files/' . $id_next;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        foreach ($attachments as $attachment) {
            $attachment->id_application = $id_next;
            $attachment->save();
            $file = 'storage/files/' . $id_prev . '/' . $attachment->location;
            // dd($file);
            rename($file, 'storage/files/' . $id_next . '/' . $attachment->location);
        }

        return redirect()->route('mail_app', ['id' => $id_next]);
    }
    public function move_temp($id)
    {
        try {
            $application = application::findOrFail($id);
            $application->id_status = 27;
            $application->save();
            session(['rout' => 'temp']);
            return redirect()->route('application', $id);
        } catch (\Exception $error) {
            return redirect()->route('application', $id)->with('error', 'Ошибка при удалении заявки');
        }
    }
}
