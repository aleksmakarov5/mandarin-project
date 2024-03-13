<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\application;
use App\Jobs\QueueSenderChat;
use App\Models\User;
use App\Models\InsuranceCompany;
use App\Models\history;
use App\Models\chat;
use App\Models\failure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\message;
use App\Models\address;
use App\Mail\user\mrknotify;
use App\Mail\user\newmessage;

use Illuminate\Validation\Rules\Can;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Psy\Command\HistoryCommand;

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
                ->get();
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
                ->get();
            // dd($applications);
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
            ->get();
        // dd($applications);


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
            ->get();
        // dd($applications);

        session(['rout' => 'lawyers']);


        return view('applications', ['applications' => $applications],);
    }

    public function archive()
    {


        $applications = DB::table('applications')
            ->where('id_status', '=', '24')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

            ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group')
            ->get();
        // dd($applications);

        session(['rout' => 'archive']);


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
                ->get();
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
                ->get();
            // dd($applications);
        }
        session(['rout' => 'directions']);
        //echo (session('rout'));




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
                'ClientPhone' => 'nullable|string',
                'id_MRK' => 'nullable|integer',
                'id_spm' => 'nullable|integer',
                'mileage' => 'nullable|integer',
                'vehicle_passport' => 'nullable|string',
                'Color' => 'nullable|string',
                'ClientPasport' => 'nullable|string',
                'DTPdate' => 'nullable|date'

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
            ->get();
        // dd($rewiew->all()) ;
        return view('application.history', ['histories' => $histories->all()], ['id' => $id]);
    }

    public function message($id)
    {

        $messages = DB::table('messages')
            ->where('application_id', '=', $id)
            // ->leftJoin('attachments', 'attachments.message_id', '=', 'messages.id')
            // ->select('messages.*', 'attachments.file_name', 'attachments.file_size', 'attachments.location')
            ->get();
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

        exec('pdftohtml storage/files/' . $request['location'] . ' -p -i');
        $html = substr($request['location'], 0, strlen($request['location']) - 4) . 's.html';
        $file = file_get_contents('storage/files/' . $html);
        $file = html_entity_decode(strip_tags($file, ['br']));
        $application_start = mb_strpos($file, 'Марка, модель');
        $application_end = mb_strpos($file, 'Точное');
        // echo $application_end . 'Раб_фин<br /><br />';
        $application = mb_substr($file, $application_start + 5, $application_end - $application_start - 18);
        $application_array = explode('<br/>', $application);
        // dd($application_array);
        return view('application.data_select', ['applications' => $application_array, 'id' => $id]);
    }
    public function data_save(Request $request, $id)
    {

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
            'DTPdate' => 'nullable|date'

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
    public function change_status(Request $request, $id)
    {
        $application = application::findOrFail($id);
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_application = $id;
        switch ($request->variant) {
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
    public function change_status_km(Request $request, $id)
    {
        $application = application::findOrFail($id);
        $history = new history;
        $history->id_user = Auth::user()->id;
        $history->id_application = $id;
        switch ($request->variant) {
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
                    $history->id_status = 5;
                    $application->save();

                    Mail::to('aleksmakarov5@yandex.ru')->queue(new mrknotify($id));
                    $history->save();
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
        foreach ($variants as $key => $value) {
            $failure = new failure;
            $failure->id_application = $id;
            $failure->id_var = $value;
            $failure->save();
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
        $users = DB::table('users')->get();

        return view('application.answer_ic', ['users' => $users->all(), 'fail_vars' => $fail_vars->all(), 'application' => $application, 'id' => $id]);
    }
    public function answer_ic_sent(Request $request, $id)
    {
        $application = application::findOrFail($id);

        Mail::to('aleksmakarov5@yandex.ru')->queue(new newmessage($id, 'robot@mandarinportal.ru', $request->message));
        $application->id_status = 24;
        $history = new history;
        $messages = new message();
        $messages->application_id = $id;
        $messages->body_html = $request->message;
        $messages->message_date = time();


        $messages->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'from';
        $address->email = 'robot@mandarinportal.ru';
        $address->save();
        $address = new address;
        $address->message_id = $messages->id;
        $address->type = 'to';
        $address->email = 'aleksmakarov5@yandex.ru';
        $address->save();
        $history->id_user = Auth::user()->id;
        $history->id_application = $id;
        $history->id_status = 24;
        $application->save();
        return redirect()
            ->route('archive');
    }
}
