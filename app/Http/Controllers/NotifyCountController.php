<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\application;
use App\Models\status_application;
use App\Models\status_chat;
use App\Models\status_message;

use Illuminate\Support\Facades\Auth;


class NotifyCountController extends Controller
{
    public function applications($user_id)
    {
        $count = ['application' => 0, 'chat' => 0, 'message' => 0];
        $applications = application::where(function ($query) {
            $query
                ->where('id_status', '<', '12')
                ->where('id_status', '<>', '6')
                ->where('id_status', '<>', '7');
        })

            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
            ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group', 'our_companies.our_name')
            ->get()
            ->sortDesc();
        foreach ($applications as $application) {
            $readed = status_application::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['application']++;
            $readed = status_chat::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['chat']++;
            $readed = status_message::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['message']++;
        }
        return $count;
    }
    public function directions($user_id)
    {
        $count = ['application' => 0, 'chat' => 0, 'message' => 0];
        $applications = application::where(function ($query) {
            $query->where('id_status', '>', '11')
                ->where('id_status', '<', '25');
        })

            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
            ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group', 'our_companies.our_name')
            ->get()->sortDesc();
        foreach ($applications as $application) {
            $readed = status_application::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['application']++;
            $readed = status_chat::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['chat']++;
            $readed = status_message::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['message']++;
        }
        return $count;
    }
    public function waiting($user_id)
    {
        $count = ['application' => 0, 'chat' => 0, 'message' => 0];
        $applications = application::where(function ($query) {
            $query->where('id_status', '>', '7')
                ->where('id_status', '<', '12');
        })

            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
            ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group', 'our_companies.our_name')
            ->get()->sortDesc();
        foreach ($applications as $application) {
            $readed = status_application::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['application']++;
            $readed = status_chat::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['chat']++;
            $readed = status_message::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['message']++;
        }
        return $count;
    }
    public function failures($user_id)
    {

        $applications = DB::table('applications')
            ->where('id_status', '=', '7')
            ->orwhere('id_status', '=', '6')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

            ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group', 'our_companies.our_name')
            ->get()->sortDesc();
        $count = ['application' => 0, 'chat' => 0, 'message' => 0];
        foreach ($applications as $application) {
            $readed = status_application::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['application']++;
            $readed = status_chat::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['chat']++;
            $readed = status_message::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['message']++;
        }
        return $count;
    }

    public function lawyers($user_id)
    {
        $count = ['application' => 0, 'chat' => 0, 'message' => 0];
        $applications = DB::table('applications')
            ->where('id_status', '=', '26')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

            ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group', 'our_companies.our_name')
            ->get()->sortDesc();
        foreach ($applications as $application) {
            $readed = status_application::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['application']++;
            $readed = status_chat::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['chat']++;
            $readed = status_message::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['message']++;
        }
        return $count;
    }

    public function archive($user_id)
    {
        $count = ['application' => 0, 'chat' => 0, 'message' => 0];
        $applications = DB::table('applications')
            ->where('id_status', '=', '25')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

            ->join('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group', 'our_companies.our_name')
            ->get()->sortDesc();
        foreach ($applications as $application) {
            $readed = status_application::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['application']++;
            $readed = status_chat::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['chat']++;
            $readed = status_message::where('id_user', '=', $user_id)->where('id_application', '=', $application->id)
                ->where('is_readed', '=', '0')->get();
            foreach ($readed as $read)
                $count['message']++;
        }
        return $count;
    }

    public function application($id, $user_id)
    {
        $count = ['application' => 0, 'chat' => 0, 'message' => 0];
        $readed = status_application::where('id_user', '=', $user_id)->where('id_application', '=', $id)
            ->where('is_readed', '=', '0')->get();
        foreach ($readed as $read)
            $count['application']++;
        $readed = status_chat::where('id_user', '=', $user_id)->where('id_application', '=', $id)
            ->where('is_readed', '=', '0')->get();
        foreach ($readed as $read)
            $count['chat']++;
        $readed = status_message::where('id_user', '=', $user_id)->where('id_application', '=', $id)
            ->where('is_readed', '=', '0')->get();
        foreach ($readed as $read)
            $count['message']++;
        return $count;
    }
}
