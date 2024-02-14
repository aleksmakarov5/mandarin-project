<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\application;
use App\Models\InsuranceCompany;
use Illuminate\Support\Facades\DB;
use App\Models\message;



class ApplicationController extends Controller
{
    //
    public function edit($id)
    {
        $InsuranceCompany = new InsuranceCompany;
        $application = DB::table('applications')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->select('applications.*', 'insurance_companies.name')
            ->where('applications.id', '=', $id)
            ->first();
        return view('application.edit', ['application' => $application], ['InsuranceCompany' => $InsuranceCompany->all()]);
    }
    public function update(Request $request, $id)
    {
        $application = application::findOrFail($id);

        $data = $this->validate($request, [
            'id_insuranceCompany' => 'required',
            'numberInsuranceCompanyApplication' => 'required',
            'CarModel' => 'nullable|string',
            'gnz' => 'nullable|string',
            'vin' => 'nullable|string',
            'ClientName' => 'nullable|string',
            'ClientPhone' => 'nullable|string',

        ]);
        // dd($data);
        foreach ($data as $key => $value) {
            $application->$key = $value;
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

        ]);
        $application = new application;

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
}
