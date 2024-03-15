<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\DB;
use App\Models\attachment;
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
    $number = date('Hi');
    $date = date('d-m-y');

    $templateProcessor->setValue('id_insuranceCompany', $application->numberInsuranceCompanyApplication);
    $templateProcessor->setValue('number', $number);
    $templateProcessor->setValue('date', $date);
    $templateProcessor->setValue('CarModel', $application->CarModel);
    $templateProcessor->setValue('gnz', $application->gnz);
    $templateProcessor->setValue('vin', $application->vin);
    $troubleshooting = DB::table('troubleshootings')->where('id_application', '=', $id)->get();
    $array = [];
    $i = 1;

    foreach ($troubleshooting as $trouble) {
      $array[] = array('id_row' => str($i), 'PartName' => $trouble->PartName, 'Work' => $trouble->Work, 'ExpertApinion' => $trouble->ExpertApinion, 'Сonclusion' => $trouble->Сonclusion);
      $i++;
    }
    //  dd ($array);

    $templateProcessor->cloneRowAndSetValues('id_row', $array);
    $filename = "Акт скрытых повреждений №" . date('d-m-y_hms') . ".docx";

    $dir = 'storage/files/' . $id;
    if (!is_dir($dir)) {
      mkdir($dir, 0777, true);
    }
    $i = 1;
    if (file_exists($dir . '/' . $filename)) {
      while (file_exists($dir . '/' . $filename))
        $filename .= '(' . $i++ . ')';
    }
    $pathToSave = $dir . '/' . $filename;
    $templateProcessor->saveAs($pathToSave);
    $document = new Attachment();
    $document->file_name = "Акт скрытых повреждений №" . $number . ' от ' . $date . ".docx";
    $document->message_id = 0;
    $document->mime_type = mime_content_type($pathToSave);
    $document->id_application = $id;
    $document->file_size = filesize($pathToSave);
    $document->location = $filename;
    $document->save();



    return redirect()->route('document', $id);
  }
}
