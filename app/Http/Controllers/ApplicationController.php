<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\application;
use App\Models\User;
use App\Models\InsuranceCompany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\message;
use App\Mail\user\mrknotify;
use Illuminate\Validation\Rules\Can;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function applications()
    {

        if (Gate::allows('admin')) {
            $applications = DB::table('applications')
            ->where('id_status','<','5')
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group')
                ->get();
            // dd($applications);

        } else {
            $applications = DB::table('applications')
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
                ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group')
            
                ->where('applications.id_MRK', '=', Auth::user()->id)
                ->orWhere('applications.id_spm', '=', Auth::user()->id)
                ->get();
        }
        return view('applications', ['applications' => $applications], );

    }

     public function failures()
    {

        
            $applications = DB::table('applications')
            ->where('id_status','=','7')
            ->orwhere('id_status','=','8')
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

                ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group')
                ->get();
            // dd($applications);

       
      
        
        return view('applications', ['applications' => $applications], );

    }

    public function lawyers()
    {

        
            $applications = DB::table('applications')
            ->where('id_status','=','25')
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

                ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group')
                ->get();
            // dd($applications);

       
      
        
        return view('applications', ['applications' => $applications], );

    }

     public function directions()
    {

        
            $applications = DB::table('applications')
            ->where('id_status','=','6')
                ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
                ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')

                ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName', 'application_statuses.current_group')
                ->get();
            // dd($applications);

       
      
        
        return view('applications', ['applications' => $applications], );

    }

    



    public function application($id)
    {
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
        if ($request->ur) {
            $application->id_MRK = null;
            $application->id_spm = null;
            $application->id_status = 25;



        } else {
            if ($application->id_status < 2 || $application->id_status == 25) {
                $application->id_status = 2;
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

        $application->id_status = 2;

        foreach ($data as $key => $value) {
            $application->$key = $value;
        }
        $application->save();
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
    public function message($id)
    {
        $messages = DB::table('messages')->where('application_id', '=', $id)->get();
        // dd($rewiew->all()) ;
        return view('application.mail', ['messages' => $messages->all()], ['id' => $id]);
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
        Mail::to('aleksmakarov5@yandex.ru')->send(new mrknotify($id));
        $application = application::findOrFail($id);
            $application->id_status = 3;
            $application->save();
        
        return redirect()->route('applications');

    }
    public function change_status(Request $request, $id)
    {
        $application = application::findOrFail($id);

        switch ($request->variant) {
            case 1:
                $application->id_status = 4;
                // dd ($application->id_status);
                Mail::to('aleksmakarov5@yandex.ru')->send(new mrknotify($id));
                break;
            case 2:
                $application->id_status = 6;
                $application->id_MRK = null;
                $application->id_spm = null;
                break;
            case 3:
                $application->id_status = 7;

                $application->id_MRK = null;
                $application->id_spm = null;
                break;
                case 4:
                    $application->id_status = 8;
                $application->id_MRK = null;
                $application->id_spm = null;
                break;

              

        }
    $application->save();
       return redirect()->route('applications');
    }
}
