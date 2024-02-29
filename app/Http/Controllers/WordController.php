<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\DB;
use App\Models\troubleshooting;


class WordController extends Controller
{
    public function export_hda($id)
    {
     $templateProcessor = new TemplateProcessor('storage/doc_template/hidden_damage_act.docx');
      $application = DB::table('applications')
            ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')

            ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
            ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName')
            ->where('applications.id', '=', $id)
            ->first();   
            //dd($application);
     $templateProcessor->setValue('id_insuranceCompany', $application->id_insuranceCompany);
      $templateProcessor->setValue('CarModel', $application->CarModel);
     $templateProcessor->setValue('gnz', $application->gnz);
     $templateProcessor->setValue('vin', $application->vin);
    //   $troubleshooting = troubleshooting::findOrFail($id);
    //   dd ($troubleshooting);
    //    $templateProcessor->cloneRowAndSetValues('id_row', $troubleshooting);
   //  $filepath = $templateProcessor->save();

      //  echo $filepath;
        
    //    dd($templateProcessor);
       $pathToSave = '/public/files';
$templateProcessor->saveAs($pathToSave);
return redirect()->route('troubleshooting', $id);
    }
}
