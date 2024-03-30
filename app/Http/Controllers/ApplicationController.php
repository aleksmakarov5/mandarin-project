<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\application;
use App\Jobs\QueueSenderChat;
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
use App\Mail\user\mrknotify;
use App\Mail\user\newmessage;
use App\Mail\user\ic_answer;
use Carbon\Carbon;

use Illuminate\Validation\Rules\Can;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Psy\Command\HistoryCommand;
use Illuminate\Support\Facades\Mail as Mail_send;

class ApplicationController extends Controller
{

    public function applications()
    {


        if (Gate::allows('admin')) {
            $applications = application::where(function ($query) {
                $query
                    ->where('id_status', '<', '6');
            })

                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')


                ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group')
                ->get()
                ->sortDesc();
        } else {
            $applications = application::where(function ($query) {
                $query
                    ->where('id_status', '<', '6');
            })
                ->where(function ($query) {
                    $query->where('applications.id_MRK', '=', Auth::user()->id)
                        ->orWhere('applications.id_spm', '=', Auth::user()->id);
                })
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

                ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group')
                ->get()->sortDesc();
            // dd($applications);
        }
        $current = carbon::now();
        foreach ($applications as $application) {
            $application->count_hour = 48 - $current->diffInHours($application->created_at);
            $application->max_hour = 48;
        }
        session(['rout' => 'applications']);
        return view('applications', ['applications' => $applications],);
    }

    public function failures()
    {


        $applications = DB::table('applications')
            ->where('id_status', '=', '7')
            ->orwhere('id_status', '=', '6')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

            ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group')
            ->get()->sortDesc();
        // dd($applications);
        $current = carbon::now();
        foreach ($applications as $application) {
            $application->count_hour = 48 - $current->diffInHours($application->created_at);
            $application->max_hour = 48;
        }

        session(['rout' => 'failures']);


        return view('applications', ['applications' => $applications],);
    }

    public function lawyers()
    {


        $applications = DB::table('applications')
            ->where('id_status', '=', '25')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

            ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group')
            ->get()->sortDesc();
        // dd($applications);

        session(['rout' => 'lawyers']);
        $current = carbon::now();
        foreach ($applications as $application) {
            $application->count_hour = 48 - $current->diffInHours($application->created_at);
            $application->max_hour = 48;
        }

        return view('applications', ['applications' => $applications],);
    }

    public function archive()
    {


        $applications = DB::table('applications')
            ->where('id_status', '=', '24')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

            ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group')
            ->get()->sortDesc();
        // dd($applications);

        session(['rout' => 'archive']);

        $current = carbon::now();
        foreach ($applications as $application) {
            $application->count_hour = 1;
            $application->max_hour = 1000;
        }
        return view('applications', ['applications' => $applications],);
    }

    public function directions()
    {

        if (Gate::allows('admin')) {
            $applications = application::where(function ($query) {
                $query->where('id_status', '>', '7')
                    ->where('id_status', '<', '24');
            })

                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

                ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group')
                ->get()->sortDesc();
        } else {
            $applications = application::where(function ($query) {
                $query->where('id_status', '>', '7')
                    ->where('id_status', '<', '24');
            })
                ->where(function ($query) {
                    $query->where('applications.id_MRK', '=', Auth::user()->id)
                        ->orWhere('applications.id_spm', '=', Auth::user()->id);
                })
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

                ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group')
                ->get()->sortDesc();

            // dd($applications);
        }
        session(['rout' => 'directions']);
        //echo (session('rout'));



        $current = carbon::now();
        foreach ($applications as $application) {
            if ($application->id_status < 10) {
                $application->count_hour = 48 - $current->diffInHours($application->created_at);
                $application->max_hour = 48;
            }
            if ($application->id_status == 10) {
                $application->count_hour = 168 - $current->diffInHours($application->check_date);
                $application->max_hour = 168;
            }
            if ($application->id_status == 18) {
                $application->count_hour = 72 - $current->diffInHours($application->check_date);
                $application->max_hour = 72;
            } else {
                $application->count_hour = 1;
                $application->max_hour = 1000;
            }
        }
        return view('applications', ['applications' => $applications],);
    }







    public function application($id)
    {
        // echo (session('rout'));
        $application = DB::table('applications')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')

            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
            ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName')
            ->where('applications.id', '=', $id)
            ->first();
        //   dd($application);
        $mrk = DB::table('users')
            ->where('id', '=', $application->id_MRK)
            ->first();
        $mrkname = '';
        if (isset($mrk->name)) {
            $mrkname = $mrk->name;
        }



        $spm
            = DB::table('users')
            ->where('id', '=', $application->id_spm)
            ->first();
        $spmname = '';
        if (isset($spm->name)) {
            $spmname = $spm->name;
        }
        $empty = 0;
        foreach ($application as $key => $value) {
            if ($key != 'ClientPasport')
                if ($value == null) {
                    $empty = 1;
                }
        }


        return view('application.index', ['application' => $application, 'id' => $id, 'mrk' => $mrkname, 'spm' => $spmname, 'empty' => $empty]);
    }
    //
    public function edit($id)
    {
        $mrks = DB::table('users')
            ->where('GroupId', '=', 5)
            ->get();
        $spms = DB::table('users')
            ->where('GroupId', '=', 6)
            ->get();
        $InsuranceCompany = new InsuranceCompany;
        $application = DB::table('applications')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->select('applications.*', 'insurance_companies.name')
            ->where('applications.id', '=', $id)
            ->first();
        return view(
            'application.edit',
            [
                'application' => $application,
                'mrks' => $mrks,
                'InsuranceCompany' => $InsuranceCompany->all(),
                'spms' => $spms
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
            $application->id_status = 25;
            $history->id_status = 25;
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
                'ClientPhone' => 'required|string',
                'id_MRK' => 'nullable|integer',
                'id_spm' => 'nullable|integer',
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
            'CarModel' => 'nullable|string',
            'gnz' => 'nullable|string',
            'vin' => 'nullable|string',
            'ClientName' => 'nullable|string',
            'ClientPhone' => 'nullable|string',
            'id_MRK' => 'nullable|integer',
            'id_spm' => 'nullable|integer',
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
        return view('application.mail', ['messages' => $messages->all()], ['id' => $id]);
    }
    public function chat(Request $request, $id)
    {

        $users = DB::table('users')->get();
        if ($request->input('only_me')) {
            $chats = DB::table('chats')
                ->where('id_application', '=', $id)
                ->where(function ($query) {
                    $query->where('to', '=', Auth::user()->id)
                        ->orWhere('from', '=', Auth::user()->id);
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
        }        // dd($rewiew->all()) ;
        return view('application.chat', ['chats' => $chats->all(), 'users' => $users->all(), 'id' => $id, 'only_me' => $request->input('only_me')]);
    }
    public function chat_sent(Request $request, $id)
    {
        $chat = new chat;
        $author = User::findOrFail($request->from);
        $chat->id_application = $id;
        $chat->from = $request->from;
        $chat->to = $request->to;
        $chat->message = $request->message;
        $chat->save();
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
        rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
        exec('pdftohtml storage/files/' . $id . '/work.pdf  -p -i');
        rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);

        $html = 'works.html';
        $file = file_get_contents('storage/files/' . $id . '/'  . $html);
        $file = html_entity_decode(strip_tags($file, ['br']));

        $application_start = mb_strpos($file, 'Марка, модель');
        $estimate_start = mb_strpos($file, 'стоимость узлов и деталей');
        $estimate_end = mb_strlen($file);
        $estimate = '';
        $price = [];
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
        $smeta = false;
        if ($application_start)
            $smeta = true;

        // echo $application_start;
        $application_end = mb_strpos($file, 'Точное');
        // echo $application_end . 'Раб_фин<br /><br />';
        $application = mb_substr($file, $application_start + 5, $application_end - $application_start - 18);
        $application_array = explode('<br/>', $application);
        // dd($application_array);
        return view('application.data_select', ['applications' => $application_array, 'id' => $id, 'smeta' => $smeta, 'price' => $price]);
    }
    public function data_save(Request $request, $id)
    {

        if (isset($request->price_sp_full)) {
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
            'CarModel' => 'nullable|string',
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
            if ($key == 'DTPdate') {
                $value = date('Y-m-d', strtotime($value));
            }
            $application->$key = $value;
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
        $application->save();
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_status = 3;
        $history->id_application = $id;
        $history->save();
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
                    $application->id_status = 8;
                    $history->id_status = 8;
                    Mail::to('aleksmakarov5@yandex.ru')->queue(new mrknotify($id));
                    $application->save();
                    $history->save();
                    return redirect()->route('directions');
                }
            case 2: {
                    $application->id_status = 5;

                    $application->save();

                    Mail::to('aleksmakarov5@yandex.ru')->queue(new mrknotify($id));

                    return redirect()->route('answer_fail_sel', $id);
                }
        }
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
        if (isset($variants[0])) {
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
        $message_id = message::where('application_id', '=', $id)->first()->id;
        $subject =
            message::where('application_id', '=', $id)->first()->subject;

        $message_id = message::where('application_id', '=', $id)->first()->id;
        $to = DB::table('addresses')
            ->where('message_id', '=', $message_id)
            ->where('type', '=', 'from')
            ->first()
            ->email;

        return view('application.answer_ic', ['subject' => $subject, 'to' => $to, 'fail_vars' => $fail_vars->all(), 'application' => $application, 'id' => $id]);
    }
    public function answer_ic_sent(Request $request, $id)
    {
        $application = application::findOrFail($id);

        $data["email"] = "aleksmakarov5@yandex.ru";

        $data["title"] = $request->subject;
        $data["body"] = $request->message;




        Mail_send::send('email.myTestMail', $data, function ($message) use ($data) {
            $message->to($data["email"], $data["email"])
                ->from('robot@mandarinportal.ru', 'Мандарин')
                ->subject($data["title"]);
        });
        $application->id_status = 24;
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
        $history->id_status = 24;
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
        $application->id_status = 9;
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_status = 9;
        $history->id_application = $id;
        $history->comment = $request->comment;
        $history->save();

        $application->save();

        // dd($variants);
        if (isset($variants[0])) {
            foreach ($variants as $key => $value) {
                $tru = new tru;
                $tru->id_application = $id;
                $tru->id_var = $value;
                $tru->save();
            }
        }
        return redirect()->route('directions');
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

        return view('application.answer_ic_true', ['subject' => $subject, 'true_vars' => $true_vars->all(), 'application' => $application, 'id' => $id, 'to' => $to]);
    }

    public function answer_ic_true_sent(Request $request, $id)
    {
        $application = application::findOrFail($id);
        $data["email"] = "aleksmakarov5@yandex.ru";

        $data["title"] = $request->subject;
        $data["body"] = $request->message;




        Mail_send::send('email.myTestMail', $data, function ($message) use ($data) {
            $message->to($data["email"], $data["email"])
                ->from('robot@mandarinportal.ru', 'Мандарин')
                ->subject($data["title"]);
        });

        $application->id_status = 10;
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
        $history->id_status = 10;
        $history->save();
        $application->save();
        return redirect()
            ->route('directions');
    }

    public function change_st($id, $status_id)
    {
        $application = application::findOrFail($id);
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_application = $id;

        $application->id_status = $status_id;
        // dd ($application->id_status);
        $data["email"] = "aleksmakarov5@yandex.ru";
        $data["title"] = 'По заявке №' . $id . ' изменен статус';
        $data["body"] = 'По заявке №' . $id . ' ' . Auth::user()->name . ' установил статус; "' . ApplicationStatus::findOrFail($status_id)->StatusName . ' "';

        Mail_send::send('email.myTestMail', $data, function ($message) use ($data) {
            $message->to($data["email"], $data["email"])
                ->from('robot@mandarinportal.ru', 'Мандарин')
                ->subject($data["title"]);
        });

        $history->id_status = $status_id;

        $application->save();
        $history->save();
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

        return view('application.answer_ic_mrk', ['users' => $users->all(),  'application' => $application, 'id' => $id, 'to' => $to, 'attach' => $attachments, 'subject' => $subject]);
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
        $application->id_status = 18;
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
        $history->id_status = 18;
        $history->save();
        $application->save();
        return redirect()->route('directions');
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
        $application->id_status = 21;
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_status = 21;
        $history->id_application = $id;
        $history->comment = $request->comment;
        $history->save();
        $application->save();
        return redirect()->route('directions');
    }
}
