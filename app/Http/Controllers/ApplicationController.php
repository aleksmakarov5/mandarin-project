<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\application;
use App\Models\work;

use App\Jobs\QueueSenderChat;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use App\Models\User;
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
use App\Models\text_notify;
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
use Illuminate\Support\Number;


use Illuminate\Validation\Rules\Can;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Psy\Command\HistoryCommand;
use Illuminate\Support\Facades\Mail as Mail_send;

class ApplicationController extends Controller
{

    public function configure($applications)
    {
        foreach ($applications as $application) {
            $spareparts = DB::table('spare_parts')->where('id_application', '=', $application->id)->get();
            $estimates = DB::table('estimates')->where('id_application', '=', $application->id)->first();
            $Works_serv = DB::table('works')->where('id_application', '=', $application->id)->get();
            $summ_service = 0;
            $summ_service_ic = 0;
            foreach ($Works_serv as $work) {
                $summ_service = $summ_service + (float) str_replace(' ', '', $work->price_service);
                $summ_service_ic = $summ_service_ic + (float) str_replace(' ', '', $work->summ);
            }

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
            $readed = status_application::where('id_user', '=', Auth::user()->id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['application']++;
            $readed = status_chat::where('id_user', '=', Auth::user()->id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['chat']++;
            $readed = status_message::where('id_user', '=', Auth::user()->id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['message']++;
            $application->eco_delta_full = Number::Currency($eco_delta_full, 'RUR', 'ru');
            $application->summ_order = Number::Currency($summ_order, 'RUR', 'ru');
            $application->summ_ic_full = Number::Currency(isset($estimates->price_repair_full) ? (float) $estimates->price_repair_full : $summ_ic_full, 'RUR', 'ru');
            $application->summ_service = Number::Currency($summ_service, 'RUR', 'ru');
            $application->summ_full =
                Number::Currency($summ_service + $summ_order, 'RUR', 'ru');
            $application->proc = $proc;
            $application->count = $count;
        }
        return $applications;
    }
    public function applications(Request $request)
    {
        $only_me = 0;
        $only_me = $request->get('only_me');

        if (Gate::allows('admin') || $only_me) {
            $applications = application::where(function ($query) {
                $query
                    ->where('id_status', '<', '12')
                    ->where('id_status', '<>', '6')
                    ->where('id_status', '<>', '7');
            })

                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')


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
                    'our_companies.our_name',
                    'users.name as MRK_name',
                    'cities.name as city'
                )
                ->get()

                ->sortDesc();
        } elseif (Gate::allows('service')) {
            $applications = application::where(function ($query) {
                $query
                    ->where('id_status', '<', '12')
                    ->where('id_status', '<>', '6')
                    ->where('id_status', '<>', '7');;
            })
                ->where('applications.id_service', '=', service::where('id_user', '=', Auth::user()->id)->first()->id)
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
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
                    'our_companies.our_name',
                    'users.name as MRK_name',
                    'cities.name as city'
                )->get()->sortDesc();
        } else {
            $applications = application::where(function ($query) {
                $query

                    ->where('id_status', '<', '12')
                    ->where('id_status', '<>', '6')
                    ->where('id_status', '<>', '7');;
            })
                ->where(function ($query) {
                    $query->where('applications.id_MRK', '=', Auth::user()->id)
                        ->orWhere('applications.id_spm', '=', Auth::user()->id);
                })
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
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
                    'our_companies.our_name',
                    'users.name as MRK_name',
                    'cities.name as city'
                )->get()->sortDesc();
            // dd($applications);
        }
        $current = carbon::now();
        foreach ($applications as $application) {

            $application->count_hour = 48 - $current->diffInHours($application->created_at);
            $application->max_hour = 48;
            $application->created = $application->created_at->toFormattedDateString();
            $application->count_five = 5 - $current->diffInHours($application->check_date);
            $doc = attachment::where('id_application', '=', $application->id)->where('id_status', '=', 2)->first();
            if (isset($doc->id_status))
                $application->doc = $doc->id_status;
            else
                $application->doc = 0;
        }
        // dd($applications);
        $applications = $this->configure($applications);
        session(['rout' => 'applications']);
        return view('applications', ['applications' => $applications, 'only_me' => $only_me],);
    }

    public function failures()
    {


        $applications = DB::table('applications')
            ->where('id_status', '=', '7')
            ->orwhere('id_status', '=', '6')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

            ->leftjoin('services', 'applications.id_service', '=', 'services.id')
            ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
            ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
            ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
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
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city'
            )->get()->sortDesc();
        // dd($applications);
        $current = carbon::now();
        foreach ($applications as $application) {
            $application->count_hour = 48 - $current->diffInHours($application->created_at);
            $application->max_hour = 48;
            $doc = attachment::where('id_application', '=', $application->id)->where('id_status', '=', 2)->first();
            if (isset($doc->id_status))
                $application->doc = $doc->id_status;
            else
                $application->doc = 0;
        }
        $applications = $this->configure($applications);
        session(['rout' => 'failures']);


        return view('applications', ['applications' => $applications,],);
    }

    public function find(Request $request)
    {

        $data = $this->validate($request, [
            'find' => 'required',
        ]);

        $applications = DB::table('applications')
            ->where('gnz', 'LIKE', '%' . $data['find'] . '%')
            ->orwhere('numberInsuranceCompanyApplication', 'LIKE', '%' . $data['find'] . '%')
            ->orwhere('ClientName', 'LIKE', '%' . $data['find'] . '%')
            ->orwhere('CarModel', 'LIKE', '%' . $data['find'] . '%')
            ->orwhere('ClientPhone', 'LIKE', '%' . $data['find'] . '%')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

            ->leftjoin('services', 'applications.id_service', '=', 'services.id')
            ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
            ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
            ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
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
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city'
            )->get()->sortDesc();
        // dd($applications);
        $current = carbon::now();
        foreach ($applications as $application) {
            $application->count_hour = 48 - $current->diffInHours($application->created_at);
            $application->max_hour = 48;

            $application->count_five = 5 - $current->diffInHours($application->check_date);
            $doc = attachment::where('id_application', '=', $application->id)->where('id_status', '=', 2)->first();
            if (isset($doc->id_status))
                $application->doc = $doc->id_status;
            else
                $application->doc = 0;
        }
        $applications = $this->configure($applications);
        session(['rout' => '']);


        return view('applications', ['applications' => $applications,],);
    }

    public function lawyers()
    {


        $applications = DB::table('applications')
            ->where('id_status', '=', '26')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

            ->leftjoin('services', 'applications.id_service', '=', 'services.id')
            ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
            ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
            ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
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
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city'
            )->get()->sortDesc();
        // dd($applications);

        session(['rout' => 'lawyers']);
        $current = carbon::now();
        foreach ($applications as $application) {
            $application->count_hour = 48 - $current->diffInHours($application->created_at);
            $application->max_hour = 48;
            $doc = attachment::where('id_application', '=', $application->id)->where('id_status', '=', 2)->first();
            if (isset($doc->id_status))
                $application->doc = $doc->id_status;
            else
                $application->doc = 0;
        }
        $applications = $this->configure($applications);
        return view('applications', ['applications' => $applications],);
    }

    public function archive()
    {
        $applications = DB::table('applications')
            ->where('id_status', '=', '25')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

            ->leftjoin('services', 'applications.id_service', '=', 'services.id')
            ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
            ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
            ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
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
                'our_companies.our_name',
                'users.name as MRK_name',
                'cities.name as city'
            )->get()->sortDesc();
        // dd($applications);

        session(['rout' => 'archive']);

        $current = carbon::now();
        foreach ($applications as $application) {
            $application->count_hour = 1;
            $application->max_hour = 1000;
            $doc = attachment::where('id_application', '=', $application->id)->where('id_status', '=', 2)->first();
            if (isset($doc->id_status))
                $application->doc = $doc->id_status;
            else
                $application->doc = 0;
        }
        $applications = $this->configure($applications);
        return view('applications', ['applications' => $applications],);
    }

    public function directions(Request $request)
    {
        $only_me = 0;
        $only_me = $request->get('only_me');

        if (Gate::allows('admin') || $only_me) {

            $applications = application::where(function ($query) {
                $query->where('id_status', '>', '11')
                    ->where('id_status', '<', '25');
            })

                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
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
                    'our_companies.our_name',
                    'users.name as MRK_name',
                    'cities.name as city'
                )->get()->sortDesc();
        } else if (Auth::user()->GroupId == 9) {
            $applications = application::where(function ($query) {
                $query->where('id_status', '>', '11')

                    ->where('id_status', '<', '25');
            })
                ->where(function ($query) {
                    $query->Where('applications.id_service', '=', service::where('id_user', '=', Auth::user()->id)->first()->id);
                })
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
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
                    'our_companies.our_name',
                    'users.name as MRK_name',
                    'cities.name as city'
                )->get()->sortDesc();
        } else {
            $applications = application::where(function ($query) {
                $query->where('id_status', '>', '11')
                    ->where('id_status', '<', '25');
            })
                ->where(function ($query) {
                    $query->where('applications.id_MRK', '=', Auth::user()->id)
                        ->orWhere('applications.id_spm', '=', Auth::user()->id);
                })
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
                ->leftjoin('users', 'applications.id_MRK', '=', 'users.id')
                ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
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
                    'our_companies.our_name',
                    'users.name as MRK_name',
                    'cities.name as city'
                )->get()->sortDesc();

            // dd($applications);
        }
        $applications = $this->configure($applications);
        session(['rout' => 'directions']);
        //echo (session('rout'));



        $current = carbon::now();
        foreach ($applications as $application) {
            if ($application->id_status < 11) {
                $application->count_hour = 48 - $current->diffInHours($application->created_at);
                $application->max_hour = 48;
            }
            if ($application->id_status == 11) {
                $application->count_hour = 168 - $current->diffInHours($application->check_date);
                $application->max_hour = 168;
            }
            if ($application->id_status == 19) {
                $application->count_hour = 72 - $current->diffInHours($application->check_date);
                $application->max_hour = 72;
            }
            if ($application->id_status == 22) {
                $application->count_hour = 42 - $current->diffInDays($application->check_date);
                $application->max_hour = 42;
            }
            if ($application->id_status > 10 && $application->id_status != 18 && $application->id_status != 21) {
                $application->count_hour = $current->diffInDays($application->end_date);
                $start = carbon::create($application->napr_date);
                $application->max_hour = $start->diffInDays($application->end_date);
            }
            $doc = attachment::where('id_application', '=', $application->id)->where('id_status', '=', 2)->first();
            if (isset($doc->id_status))
                $application->doc = $doc->id_status;
            else
                $application->doc = 0;
        }
        return view('applications', ['applications' => $applications, 'only_me' => $only_me],);
    }
    public function waiting(Request $request)
    {
        $only_me = 0;
        $only_me = $request->get('only_me');

        if (Gate::allows('admin') || $only_me) {

            $applications = application::where(function ($query) {
                $query->where('id_status', '>', '7')
                    ->where('id_status', '<', '12');
            })

                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
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
                    'our_companies.our_name'
                )->get()->sortDesc();
        } else if (Auth::user()->GroupId == 9) {
            $applications = application::where(function ($query) {
                $query->where('id_status', '>', '7')
                    ->where('id_status', '<', '12');
            })
                ->where(function ($query) {
                    $query->Where('applications.id_service', '=', service::where('id_user', '=', Auth::user()->id)->first()->id);
                })
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
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
                    'our_companies.our_name'
                )->get()->sortDesc();
        } else {
            $applications = application::where(function ($query) {
                $query->where('id_status', '>', '7')
                    ->where('id_status', '<', '12');
            })
                ->where(function ($query) {
                    $query->where('applications.id_MRK', '=', Auth::user()->id)
                        ->orWhere('applications.id_spm', '=', Auth::user()->id);
                })
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
                ->leftjoin('services', 'applications.id_service', '=', 'services.id')
                ->leftjoin('lawyers', 'applications.id_lawyer', '=', 'lawyers.id')
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
                    'our_companies.our_name'
                )->get()->sortDesc();

            // dd($applications);
        }
        $applications = $this->configure($applications);
        session(['rout' => 'waiting']);
        //echo (session('rout'));



        $current = carbon::now();
        foreach ($applications as $application) {
            if ($application->id_status < 11) {
                $application->count_hour = 48 - $current->diffInHours($application->created_at);
                $application->max_hour = 48;
            }
            if ($application->id_status == 11) {
                $application->count_hour = 168 - $current->diffInHours($application->check_date);
                $application->max_hour = 168;
            }
            if ($application->id_status == 19) {
                $application->count_hour = 72 - $current->diffInHours($application->check_date);
                $application->max_hour = 72;
            }
            if ($application->id_status == 22) {
                $application->count_hour = 42 - $current->diffInDays($application->check_date);
                $application->max_hour = 42;
            }
            if ($application->id_status > 10 && $application->id_status != 18 && $application->id_status != 21) {
                $application->count_hour = $current->diffInDays($application->end_date);
                $start = carbon::create($application->napr_date);
                $application->max_hour = $start->diffInDays($application->end_date);
            }
        }
        return view('applications', ['applications' => $applications, 'only_me' => $only_me],);
    }






    public function application(Request $request, $id)
    {
        session(['current' => $id]);
        $hide_menu = $request->get('hide_menu');
        // echo (session('rout'));
        $application = DB::table('applications')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')

            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
            ->leftjoin('services', 'applications.id_service', '=', 'services.id')
            ->leftjoin('users as spms', 'applications.id_spm', '=', 'spms.id')
            ->leftjoin('users as mrks', 'applications.id_mrk', '=', 'mrks.id')
            ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
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
        $mrk = DB::table('users')
            ->where('id', '=', $application->id_MRK)
            ->first();

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


        return view('application.index', ['application' => $application, 'id' => $id, 'empty' => $empty, 'hide_menu' => $hide_menu]);
    }
    //
    public function edit($id)
    {

        $spms = DB::table('users')
            ->where('GroupId', '=', 6)
            ->get();

        $services = DB::table('services')->get();
        $lawyers = DB::table('lawyers')->get();

        $InsuranceCompany = new InsuranceCompany;
        $application = DB::table('applications')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->select('applications.*', 'insurance_companies.name')
            ->where('applications.id', '=', $id)
            ->first();
        $cities = DB::table('cities')
            ->join('city_companies', 'cities.id', '=', 'city_companies.id_city')
            ->where('city_companies.id_company', '=', $application->our_company)
            ->select('cities.*', 'city_companies.id_city as city_companies_id')
            ->get();
        $mrks = DB::table('users')
            ->join('mrk_companies', 'users.id', '=', 'mrk_companies.id_mrk')
            ->where('users.GroupId', '=', 5)
            ->where('mrk_companies.id_company', '=', $application->our_company)
            ->select('users.*', 'users.id as user_id')
            ->get();
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
                'cities' => $cities
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
                'CarModel' => 'required|string',
                'gnz' => 'nullable|string',
                'vin' => 'nullable|string',
                'ClientName' => 'nullable|string',
                'ClientPhone' => 'required|string',
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
                'end_date' => 'nullable|date',
                'wait_sp' => 'nullable|string',
                'ClientPasport_number' => 'nullable|string',
                'ClientPasport_struct' => 'nullable|string',
                'ClientPasport_date' => 'nullable|date',
                'id_city' => 'nullable|integer',
                'pred' => 'nullable|string',
                'pred_phone' => 'nullable|string'

            ]);
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
        return view('application.create', ['InsuranceCompany' => $InsuranceCompany->all()]);
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
            'DTPdate' => 'nullable|date'

        ]);
        $application = new application;
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_status = 2;
        $application->id_status = 2;




        foreach ($data as $key => $value) {
            $application->$key = $value;
        }
        $application->save();

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
        $application = application::findOrFail($id);
        $application->delete();
        return redirect()
            ->route('applications');
    }
    public function history($id)
    {
        $histories = DB::table('histories')
            ->where('id_application', '=', $id)
            ->join('application_statuses', 'histories.id_status', '=', 'application_statuses.id')
            ->join('users', 'histories.id_user', '=', 'users.id')
            ->select('histories.*', 'application_statuses.StatusName', 'users.name')
            ->get()->sortDesc();
        // dd($rewiew->all()) ;
        return view('application.history', ['histories' => $histories->all()], ['id' => $id]);
    }

    public function message($id)
    {
        $notify = notify::where('id_application', '=', $id)
            ->where('id_user', '=', Auth::user()->id)
            ->where('id_text', '=', '1')->get()->all();
        foreach ($notify as $notif) {
            $notif->delete();
        }

        $status_message = status_message::where('id_user', '=', Auth::user()->id)->where('id_application', '=', $id)
            ->where('is_readed', '=', '0')->get();
        foreach ($status_message as $status) {
            $status->is_readed = 1;
            $status->save();
        }

        $messages = DB::table('messages')
            ->where('application_id', '=', $id)
            // ->leftJoin('attachments', 'attachments.message_id', '=', 'messages.id')
            // ->select('messages.*', 'attachments.file_name', 'attachments.file_size', 'attachments.location')
            ->get()->sortDesc();
        foreach ($messages as $message) {
            $message->attachment = DB::table('attachments')
                ->where('message_id', '=', $message->id)
                ->get();
            $message->from = DB::table('addresses')
                ->where('message_id', '=', $message->id)
                ->where('type', '=', 'from')
                ->first();
            $message->to = DB::table('addresses')
                ->where('message_id', '=', $message->id)
                ->where('type', '=', 'to')
                ->get();
        }
        // dd($messages);
        // $application = application::findOrFail($id);
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

            $attachments = attachment::where('id_application', '=', $id)->get()->all();
        } catch (\Exception $e) {
            $message_id = 0;
            $subject = '';
            $to_email = '';
            $attachments = [];
        }
        foreach ($messages as $message) {
            if (isset($message->application_id)) {
                $body = $message->body_html;
                $body_start = mb_strpos($body, '<body');
                $body_end = mb_strpos($body, '</body>');
                if ($body_start) {
                    $body = mb_substr($body, $body_start, $body_end - $body_start);
                }
                $body = str_replace('color', '', $body);

                $body = str_replace('font-size:', '', $body);
                $body = str_replace('cid:', '/storage/files/' . $message->application_id . '/', $body);
                $body = str_replace('gif', 'gif"', $body);
                $body = str_replace('png', 'png"', $body);
                $loc = Attachment::where('message_id', '=', $message->id)
                    ->where('file_name', 'like', '%image001.png%')
                    ->first();
                $location = isset($loc->location) ? $loc->location : '';
                $body = str_replace('image001.png', $location, $body);
                for ($i = 2; $i < 10; $i++) {
                    $loc = Attachment::where('message_id', '=', $message->id)
                        ->where('file_name', 'like', '%image00' . $i . '.gif%')
                        ->first();
                    $location = isset($loc->location) ? $loc->location : '';
                    $body = str_replace('image00' . $i . '.gif', $location, $body);
                }
                $message->body = $body;
            }
        }


        return view('application.mail', ['messages' => $messages->all(), 'id' => $id, 'to_email' => $to_email, 'attach' => $attachments, 'subject' => $subject]);
    }
    public function chat(Request $request, $id)
    {
        $status_chat = status_chat::where('id_user', '=', Auth::user()->id)->where('id_application', '=', $id)
            ->where('is_readed', '=', '0')->get();
        foreach ($status_chat as $status) {
            $status->is_readed = 1;
            $status->save();
        }
        $notify = notify::where('id_application', '=', $id)
            ->where('id_user', '=', Auth::user()->id)
            ->where('id_text', '=', '2')->get()->all();
        foreach ($notify as $notif) {
            $notif->delete();
        }

        $users = DB::table('users')->get();
        if ($request->input('only_me') || Gate::allows('service')) {
            $chats = DB::table('chats')
                ->where('id_application', '=', $id)
                ->where(function ($query) {
                    $query->where('to', '=', Auth::user()->id)
                        ->orWhere('from', '=', Auth::user()->id);
                })
                ->leftjoin('users as userfrom', 'chats.from', '=', 'userfrom.id')
                ->leftjoin('users as userto', 'chats.to', '=', 'userto.id')
                ->select('chats.*', 'userfrom.name as from_name', 'userto.name as to_name')
                ->get()->sortDesc();
        } else {
            $chats = DB::table('chats')
                ->where('id_application', '=', $id)
                ->leftjoin('users as userfrom', 'chats.from', '=', 'userfrom.id')
                ->leftjoin('users as userto', 'chats.to', '=', 'userto.id')
                ->select('chats.*', 'userfrom.name as from_name', 'userto.name as to_name')
                ->get()->sortDesc();
        }        // dd($rewiew->all()) ;
        return view('application.chat', ['chats' => $chats->all(), 'users' => $users->all(), 'id' => $id, 'only_me' => $request->input('only_me')]);
    }
    public function chat_sent(Request $request, $id)
    {
        $chat = new chat;
        $author = User::findOrFail($request->from);
        $recipient = User::findOrFail($request->to);
        $chat->id_application = $id;
        $chat->from = $request->from;
        $chat->to = $request->to;
        $chat->message = $request->message;
        $chat->save();


        $notify = new notify;
        $notify->id_user = $request->to;
        $notify->id_application = $id;
        $notify->id_text = 2;
        $notify->link = "/chat/";

        $notify->save();
        $status_chat = new status_chat;
        $status_chat->id_user = $request->to;
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
        }

        // $qs = new QueueSenderChat('aleksmakarov5@yandex.ru', $id, $author->name, $request->message);
        // $this->dispatch($qs);
        Mail::to('aleksmakarov5@yandex.ru')->queue(new newmessage($id, $author->name, $request->message));

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
        } else {
            rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
            exec('pdftohtml storage/files/' . $id . '/work.pdf  -p -i');
            rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);

            $html = 'works.html';
            $file = file_get_contents('storage/files/' . $id . '/' . $html);
            $file = html_entity_decode(strip_tags($file, ['br']));
            // dd($file);



            $app = Application::findorfail($id);
            if ($app->id_insuranceCompany == 1) {
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
                $application_end = mb_strpos($file, 'Точное');
                // echo $application_end . 'Раб_фин<br /><br />';
                $application = mb_substr($file, $application_start + 5, $application_end - $application_start - 18);
                $application_array = explode('<br/>', $application);
                // dd($application_array);
                if ($smeta) {
                    $applications['CarModel'] = $application_array[1];
                    $applications['gnz'] = $application_array[11];
                    $applications['vin'] = $application_array[3];
                    $applications['vehicle_passport'] = $application_array[7];
                    $applications['Color'] = $application_array[15];
                    $applications['mileage'] = $application_array[5];
                    $applications['DTPdate'] = $application_array[9];
                    if (isset($application_array[20]))
                        $applications['ClientName'] = $application_array[20];
                    else
                        $applications['ClientName'] = $application_array[19];
                } else {
                    $applications['ClientName'] = $application_array[2];
                    $applications['gnz'] = $application_array[18];
                    $applications['vin'] = $application_array[23];
                    $applications['CarModel'] = $application_array[17];
                    $applications['dogovor'] = $application_array[19];
                    $applications['mnf_year'] = $application_array[24];
                    $applications['mileage'] = $application_array[25];
                    $applications['ub_number'] = $application_array[1];
                    $applications['CarModel'] = mb_substr($application_array[11], mb_strpos($application_array[11], '/') + 1, mb_strlen($application_array[11]) - mb_strpos($application_array[11], '/'));
                }

                // dd($applications);
            } else if ($app->id_insuranceCompany == 3) {
                $file = preg_replace("(\u{A0})", ' ', $file);

                $file = str_replace("/\u{A0}/", '', $file);
                $application_start = mb_strpos($file, '<br/>Объект');
                $application_end = mb_strpos($file, 'Заключение');
                $application = mb_substr($file, $application_start + 20, $application_end - $application_start);
                $smeta = false;
                if ($application_start)
                    $smeta = true;

                $application_array = explode('<br/>', $application);
                if (isset($application_array['1'])) {
                    $applications['CarModel'] = $application_array[15];
                    $applications['gnz'] = $application_array[2];
                    $applications['vin'] = $application_array[9];
                    $applications['Color'] = $application_array[45];
                    $applications['mnf_year'] = $application_array[27];
                    $applications['mileage'] = (int) $application_array[33];
                    $applications['vehicle_passport'] = $application_array[68];
                    $applications['ClientName'] = $application_array[74];
                    //dd($application_array);
                }

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

                    $estimate_start = mb_strpos($file, 'стоимость восстановительного ремонта');
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
                    'price_sp_full' => $request->price_sp_full,
                    'price_sp_half' => $request->price_sp_half,
                    'price_repair' => $request->price_repair,
                    'price_paint' => $request->price_paint,
                    'price_zip' => $request->price_zip,
                    'price_work' => $request->price_work,
                    'price_repair_full' => $request->price_repair_full,
                    'price_repair_half' => $request->price_repair_half
                ]
            );
        }
        $application = application::findOrFail($id);
        if ($application->id_status < 2) {
            $application->id_status = 2;
            $history = new history;
            $history->id_user = Auth::user()->id;
            $history->id_status = 2;
            $history->id_application = $id;
            $history->save();
        }
        $data = $this->validate($request, [
            'CarModel' => 'required|string',
            'gnz' => 'nullable|string',
            'vin' => 'nullable|string',
            'ClientName' => 'nullable|string',
            'ClientPhone' => 'nullable|string',
            'mileage' => 'nullable|integer',
            'vehicle_passport' => 'nullable|string',
            'Color' => 'nullable|string',
            'ClientPassport' => 'nullable|string',
            'DTPdate' => 'nullable|date',
            'id_MRK' => 'nullable|integer',
            'id_spm' => 'nullable|integer',
            'dogovor' => 'nullable|string',
            'mnf_year' => 'nullable|string',
            'ub_number' => 'nullable|string'




        ]);
        // dd($data);
        foreach ($data as $key => $value) {
            if ($value !== '') {
                if ($key == 'DTPdate') {
                    $value = date('Y-m-d', strtotime($value));
                }
                $application->$key = $value;
            }
        }
        $application->save();

        return redirect()->route('application', $id);
    }
    public function send_mrk($id, $id_MRK)
    {
        $user = User::find($id_MRK);
        Mail::to('aleksmakarov5@yandex.ru')->queue(new mrknotify($id));
        $application = application::findOrFail($id);
        $application->id_status = 3;
        $application->check_date = carbon::now();
        $application->save();
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_status = 3;
        $history->id_application = $id;
        $history->save();
        $user = user::findorfail($id_MRK);
        $notify = new notify;
        $notify->id_user = $user->id;
        $notify->id_application = $id;
        $notify->id_text = 3;
        $notify->link = "/application/";
        $notify->save();
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
        }
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


                Mail::to('aleksmakarov5@yandex.ru')->queue(new mrknotify($id));
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

                    $notify->save();
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

                    $notify->save();
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
                }
                break;
            case 5:
                $user = user::findorfail($application->id_MRK);

                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 3;
                $notify->link = "/application/";

                $notify->save();
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

                break;
            case 6:
                $user = user::findorfail($application->id_spm);

                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 3;
                $notify->link = "/application/";

                $notify->save();
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

                break;
            case 8:
                $users = user::where('GroupId', '=', $status_group)->get()->all();
                foreach ($users as $user) {
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 3;
                    $notify->link = "/application/";

                    $notify->save();
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
                    Mail::to('aleksmakarov5@yandex.ru')->queue(new mrknotify($id));
                    $application->save();
                    $history->save();
                    return redirect()->route('applications');
                }
            case 2: {
                    $application->id_status = 5;

                    $application->save();

                    Mail::to('aleksmakarov5@yandex.ru')->queue(new mrknotify($id));

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

                    $notify->save();
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

                    $notify->save();
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
                }
                break;
            case 5:
                $user = user::findorfail($application->id_MRK);

                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 3;
                $notify->link = "/application/";

                $notify->save();
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

                break;
            case 6:
                $user = user::findorfail($application->id_spm);

                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 3;
                $notify->link = "/application/";

                $notify->save();
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

                break;
            case 8:
                $users = user::where('GroupId', '=', $status_group)->get()->all();
                foreach ($users as $user) {
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 3;
                    $notify->link = "/application/";

                    $notify->save();
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
                }
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

        $data["email"] = "aleksmakarov5@yandex.ru";
        $data["title"] = $request->subject ?? $application->numberInsuranceCompanyApplication;
        $data["body"] = $request->message;
        $data["from"] = OurCompany::where('id', '=', $application->our_company)->first()->our_email;
        $data["name"] = OurCompany::where('id', '=', $application->our_company)->first()->our_name;
        $data["mailer"] = OurCompany::where('id', '=', $application->our_company)->first()->our_mailer;

        Mail_send::mailer($data['mailer'])->send('email.myTestMail', $data, function ($message) use ($data) {
            $message->to($data["email"], $data["email"])
                ->from($data['from'], $data['name'])
                ->subject($data["title"]);
        });
        $application->id_status = 25;
        $history = new history;
        $messages = new message();
        $messages->application_id = $id;
        $messages->body_html = $request->message;
        $messages->message_date = date('Y-m-d H:i:s');


        $messages->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'from';
        $address->email = 'robot@mandarinportal.ru';
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
        $data["email"] = "aleksmakarov5@yandex.ru";
        $data["title"] = $request->subject;
        $data["body"] = $request->message;
        $data["from"] = OurCompany::where('id', '=', $application->our_company)->first()->our_email;
        $data["name"] = OurCompany::where('id', '=', $application->our_company)->first()->our_name;
        $data["mailer"] = OurCompany::where('id', '=', $application->our_company)->first()->our_mailer;
        $files = [];
        for ($i = 0; $i < count($request->location); $i++) {
            $files[] = public_path('/storage/files/' . $id . '/' . $request->location[$i]);
        }


        Mail_send::send('email.myTestMail', $data, function ($message) use ($data, $files) {
            $message->to($data["email"], $data["email"])
                ->from('robot@mandarinportal.ru', 'Мандарин')
                ->subject($data["title"]);
            foreach ($files as $file) {
                $message->attach($file);
            }
        });

        $application->id_status = 11;
        $application->check_date = carbon::now();
        $history = new history;

        $messages = new message();
        $messages->application_id = $id;
        $messages->subject = message::where('application_id', '=', $id)->first()->subject;
        $messages->body_html = $request->message;
        $messages->message_date = date('Y-m-d H:i:s');


        $messages->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'from';
        $address->email = 'robot@mandarinportal.ru';
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

        $application->id_status = $status_id;
        if ($status_id == 12) {
            $today = WorkCalendar::now();
            $today->addWorkDays(30);
            $application->end_date = $today;

            $application->napr_date = carbon::now();

            // dd($today, $application->end_date);
        }
        // dd ($application->id_status);
        $data["email"] = "aleksmakarov5@yandex.ru";
        $data["title"] = 'По заявке №' . $id . ' изменен статус';
        $data["body"] = 'По заявке №' . $id . ' ' . Auth::user()->name . ' установил статус; "' . ApplicationStatus::findOrFail($status_id)->StatusName . ' "';
        $data["from"] = OurCompany::where('id', '=', $application->our_company)->first()->our_email;
        $data["name"] = OurCompany::where('id', '=', $application->our_company)->first()->our_name;
        $data["mailer"] = OurCompany::where('id', '=', $application->our_company)->first()->our_mailer;

        Mail_send::mailer($data['mailer'])->send('email.myTestMail', $data, function ($message) use ($data) {
            $message->to($data["email"], $data["email"])
                ->from($data['from'], $data['name'])
                ->subject($data["title"]);
        });

        $history->id_status = $status_id;

        $application->save();
        $history->save();
        $notify_id = 0;
        $status_group = ApplicationStatus::findOrFail($status_id)->current_group;
        switch ($status_group) {
            case 2:
                $users = user::where('GroupId', '=', $status_group)->get()->all();
                foreach ($users as $user) {
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 3;
                    $notify->link = "/application/";

                    $notify->save();
                    $notify_id = $notify->id;
                    $status_application = new status_application;
                    $status_application->id_user = $user->id;
                    $status_application->id_application = $id;
                    $status_application->is_readed = false;
                    $status_application->save();
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

                    $notify->save();
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
                }
                break;
            case 5:
                $user = user::findorfail($application->id_MRK);

                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 3;
                $notify->link = "/application/";

                $notify->save();
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

                break;
            case 6:
                $user = user::findorfail($application->id_spm);

                $notify = new notify;
                $notify->id_user = $user->id;
                $notify->id_application = $id;
                $notify->id_text = 3;
                $notify->link = "/application/";

                $notify->save();
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


                break;
            case 8:
                $users = user::where('GroupId', '=', $status_group)->get()->all();
                foreach ($users as $user) {
                    $notify = new notify;
                    $notify->id_user = $user->id;
                    $notify->id_application = $id;
                    $notify->id_text = 3;
                    $notify->link = "/application/";

                    $notify->save();
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
                }
                break;
        }
        $notification = DB::table('notifies')->where('notifies.id', '=', $notify_id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
            ->select('notifies.*', 'text_notifies.text')->first();
        broadcast(new MessageSent($notification));
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

    public function answer_ic_mrk_sent(Request $request, $id)
    {
        // dd($request);

        // Mail::to('aleksmakarov5@yandex.ru')->queue(new ic_answer($id, $request->subject, $request->message, $request->location));
        $data["email"] = "aleksmakarov5@yandex.ru";

        $data["title"] = $request->subject;
        $data["body"] = $request->message;
        $files = [];
        for ($i = 0; $i < count($request->location); $i++) {
            $files[] = public_path('/storage/files/' . $id . '/' . $request->location[$i]);
        }


        Mail_send::send('email.myTestMail', $data, function ($message) use ($data, $files) {
            $message->to($data["email"], $data["email"])
                ->from('robot@mandarinportal.ru', 'Мандарин')
                ->subject($data["title"]);
            foreach ($files as $file) {
                $message->attach($file);
            }
        });

        $application = application::findOrFail($id);
        $application->id_status = 19;
        $application->check_date = carbon::now();
        $history = new history;

        $messages = new message();
        $messages->application_id = $id;
        $messages->subject = message::where('application_id', '=', $id)->first()->subject;
        $messages->body_html = $request->message;
        $messages->message_date = date('Y-m-d H:i:s');


        $messages->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'from';
        $address->email = 'robot@mandarinportal.ru';
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
        $data["email"] = "aleksmakarov5@yandex.ru";

        $data["title"] = $request->subject;
        $data["body"] = $request->message;
        $files = [];
        for ($i = 0; $i < count($request->location); $i++) {
            $files[] = public_path('/storage/files/' . $id . '/' . $request->location[$i]);
        }


        Mail_send::send('email.myTestMail', $data, function ($message) use ($data, $files) {
            $message->to($data["email"], $data["email"])
                ->from('robot@mandarinportal.ru', 'Мандарин')
                ->subject($data["title"]);
            foreach ($files as $file) {
                $message->attach($file);
            }
        });
        $application = application::findOrFail($id);
        $application->id_status = 22;
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_status = 22;
        $history->id_application = $id;
        $history->comment = $request->comment;
        $history->save();
        $messages = new message();
        $messages->application_id = $id;
        $messages->subject = message::where('application_id', '=', $id)->first()->subject;
        $messages->body_html = $request->message;
        $messages->message_date = date('Y-m-d H:i:s');


        $messages->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'from';
        $address->email = 'robot@mandarinportal.ru';
        $address->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'to';
        $address->email = $request->to;
        $address->save();
        $history->id_user = Auth::user()->id;
        $history->id_application = $id;
        $history->id_status = 22;
        $history->save();
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
        return redirect()->back();
    }
    public function add_mail($type, $id, $message_id)
    {
        $applications = application::where('our_company', '=', $id)->get()->sortDesc()->all();
        return view('application.add_mail', [
            'applications' => $applications,
            'message_id' => $message_id,
            'type' => $type,
            'id' => $id,
        ]);
    }
    public function new_mail($type, $id, $message_id, $id_application)
    {
        $message = message::findOrFail($message_id);
        $message->application_id = $id_application;
        $message->save();
        $ttachments = attachment::where('message_id', '=', $message_id);
        foreach ($ttachments as $ttachment) {
            $ttachment->id_application = $id_application;
            $ttachment->save();
            $file = 'public/storage/files/temp/' . $ttachment->location;
            rename($file, 'public/storage/files/' . $id_application . '/' . $ttachment->location);
        }

        return redirect()->route('mail_app', ['id' => $id_application]);
    }
    public function combine($id_comp, $id)
    {
        $applications = application::where('our_company', '=', $id_comp)->get()->sortDesc()->all();
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
}
