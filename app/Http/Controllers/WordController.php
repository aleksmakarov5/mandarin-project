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
    $application = DB::table('applications')
      ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
      ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
      ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName')
      ->where('applications.id', '=', $id)
      ->first();
    $templateProcessor = new TemplateProcessor('storage/doc_template/' . $application->id_insuranceCompany . '/hda.docx');
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

    $templateProcessor->cloneRowAndSetValues('PartName', $array);
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
    $document->id_status = 11;
    $document->save();
    return redirect()->route('document', $id);
  }

  public function export_dk($id)
  {
    $application = DB::table('applications')
      ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
      ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
      ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName')
      ->where('applications.id', '=', $id)
      ->first();
    $templateProcessor = new TemplateProcessor('storage/doc_template/' . $application->id_insuranceCompany . '/dk.docx');
    //dd($application);
    $number = date('Hi');
    $month = date('m');
    $month_name = '';
    switch ($month) {
      case 1:
        $month_name = 'января';
        break;
      case 2:
        $month_name = 'февраля';
        break;
      case 3:
        $month_name = 'марта';
        break;
      case 4:
        $month_name = 'апреля';
        break;
      case 5:
        $month_name = 'мая';
        break;
      case 6:
        $month_name = 'июня';
        break;
      case 7:
        $month_name = 'июля';
        break;
      case 8:
        $month_name = 'августа';
        break;
      case 9:
        $month_name = 'сентября';
        break;
      case 10:
        $month_name = 'октября';
        break;
      case 11:
        $month_name = 'ноября';
        break;
      case 12:
        $month_name = 'декабря';
        break;
    }
    $date = date('d') . ' ' . $month_name . ' ' . date('Y');


    $templateProcessor->setValue('Client', $application->ClientName);
    $templateProcessor->setValue('number', $number);
    $templateProcessor->setValue('Date', $date);
    $templateProcessor->setValue('CarModel', $application->CarModel);
    $templateProcessor->setValue('gnz', $application->gnz);
    $templateProcessor->setValue('vin', $application->vin);
    $troubleshooting = DB::table('troubleshootings')->where('id_application', '=', $id)->get();
    $array = [];
    $i = 1;


    //  dd ($array);

    $filename = "Договор о проведении восстановительного ремонта №" . date('d-m-y_hms') . ".docx";

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
    $document->file_name = "Договор о проведении восстановительного ремонта №" . $number . ' от ' . $date . ".docx";
    $document->message_id = 0;
    $document->mime_type = mime_content_type($pathToSave);
    $document->id_application = $id;
    $document->file_size = filesize($pathToSave);
    $document->location = $filename;
    $document->id_status = 2;
    $document->save();
    return redirect()->route('document', $id);
  }

  public function export_ap($id, $status_id)
  {
    $application = DB::table('applications')
      ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
      ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
      ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName')
      ->where('applications.id', '=', $id)
      ->first();
    $templateProcessor = new TemplateProcessor('storage/doc_template/' . $application->id_insuranceCompany . '/ap.docx');
    //dd($application);
    $number = date('Hi');
    $month = date('m');
    $month_name = '';
    switch ($month) {
      case 1:
        $month_name = 'января';
        break;
      case 2:
        $month_name = 'февраля';
        break;
      case 3:
        $month_name = 'марта';
        break;
      case 4:
        $month_name = 'апреля';
        break;
      case 5:
        $month_name = 'мая';
        break;
      case 6:
        $month_name = 'июня';
        break;
      case 7:
        $month_name = 'июля';
        break;
      case 8:
        $month_name = 'августа';
        break;
      case 9:
        $month_name = 'сентября';
        break;
      case 10:
        $month_name = 'октября';
        break;
      case 11:
        $month_name = 'ноября';
        break;
      case 12:
        $month_name = 'декабря';
        break;
    }
    $date = date('d') . ' ' . $month_name . ' ' . date('Y');


    $templateProcessor->setValue('id_insuranceCompany', $application->numberInsuranceCompanyApplication);
    $templateProcessor->setValue('Client', $application->ClientName);
    $templateProcessor->setValue('number', $number);
    $templateProcessor->setValue('Date', $date);
    $templateProcessor->setValue('CarModel', $application->CarModel);
    $templateProcessor->setValue('gnz', $application->gnz);
    $templateProcessor->setValue('vin', $application->vin);
    $troubleshooting = DB::table('troubleshootings')->where('id_application', '=', $id)->get();
    $array = [];
    $i = 1;


    //  dd ($array);

    $filename = "АКТ ПРИЕМА ЛЕГКОВОГО АВТОМОБИЛЯ №" . date('d-m-y_hms') . ".docx";

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
    $document->file_name = "АКТ ПРИЕМА ЛЕГКОВОГО АВТОМОБИЛЯ №" . $number . ' от ' . $date . ".docx";
    $document->message_id = 0;
    $document->mime_type = mime_content_type($pathToSave);
    $document->id_application = $id;
    $document->file_size = filesize($pathToSave);
    $document->location = $filename;
    $document->id_status = $status_id;
    $document->save();
    return redirect()->route('document', $id);
  }

  public function export_as($id, $status_id)
  {
    $application = DB::table('applications')
      ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
      ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
      ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName')
      ->where('applications.id', '=', $id)
      ->first();
    $templateProcessor = new TemplateProcessor('storage/doc_template/' . $application->id_insuranceCompany . '/ap.docx');
    //dd($application);
    $number = date('Hi');
    $month = date('m');
    $month_name = '';
    switch ($month) {
      case 1:
        $month_name = 'января';
        break;
      case 2:
        $month_name = 'февраля';
        break;
      case 3:
        $month_name = 'марта';
        break;
      case 4:
        $month_name = 'апреля';
        break;
      case 5:
        $month_name = 'мая';
        break;
      case 6:
        $month_name = 'июня';
        break;
      case 7:
        $month_name = 'июля';
        break;
      case 8:
        $month_name = 'августа';
        break;
      case 9:
        $month_name = 'сентября';
        break;
      case 10:
        $month_name = 'октября';
        break;
      case 11:
        $month_name = 'ноября';
        break;
      case 12:
        $month_name = 'декабря';
        break;
    }
    $date = date('d') . ' ' . $month_name . ' ' . date('Y');


    $templateProcessor->setValue('id_insuranceCompany', $application->numberInsuranceCompanyApplication);
    $templateProcessor->setValue('Client', $application->ClientName);
    $templateProcessor->setValue('number', $number);
    $templateProcessor->setValue('Date', $date);
    $templateProcessor->setValue('CarModel', $application->CarModel);
    $templateProcessor->setValue('gnz', $application->gnz);
    $templateProcessor->setValue('vin', $application->vin);

    $i = 1;


    //  dd ($array);

    $filename = "Приемо-сдаточный Акт №" . date('d-m-y_hms') . ".docx";

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
    $document->file_name = "Приемо-сдаточный Акт №" . $number . ' от ' . $date . ".docx";
    $document->message_id = 0;
    $document->mime_type = mime_content_type($pathToSave);
    $document->id_application = $id;
    $document->file_size = filesize($pathToSave);
    $document->location = $filename;
    $document->id_status = $status_id;
    $document->save();
    return redirect()->route('document', $id);
  }

  public function export_if($id)
  {
    $application = DB::table('applications')
      ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
      ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
      ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName')
      ->where('applications.id', '=', $id)
      ->first();
    $templateProcessor = new TemplateProcessor('storage/doc_template/' . $application->id_insuranceCompany . '/if.docx');
    //dd($application);
    $number = date('Hi');
    $month = date('m');
    $month_name = '';
    switch ($month) {
      case 1:
        $month_name = 'января';
        break;
      case 2:
        $month_name = 'февраля';
        break;
      case 3:
        $month_name = 'марта';
        break;
      case 4:
        $month_name = 'апреля';
        break;
      case 5:
        $month_name = 'мая';
        break;
      case 6:
        $month_name = 'июня';
        break;
      case 7:
        $month_name = 'июля';
        break;
      case 8:
        $month_name = 'августа';
        break;
      case 9:
        $month_name = 'сентября';
        break;
      case 10:
        $month_name = 'октября';
        break;
      case 11:
        $month_name = 'ноября';
        break;
      case 12:
        $month_name = 'декабря';
        break;
    }
    $date = date('d') . ' ' . $month_name . ' ' . date('Y');


    $templateProcessor->setValue('Client', $application->ClientName);
    $templateProcessor->setValue('number', $application->numberInsuranceCompanyApplication . '/' . $application->ub_number);
    $templateProcessor->setValue('Date', $date);
    $templateProcessor->setValue('CarModel', $application->CarModel);
    $templateProcessor->setValue('gnz', $application->gnz);
    $templateProcessor->setValue('vin', $application->vin);
    $templateProcessor->setValue('start_date', $application->start_date);
    $templateProcessor->setValue('end_date', $application->end_date);
    $templateProcessor->setValue('wait_sp', $application->wait_sp);
    $templateProcessor->setValue('pass_ser', $application->ClientPasport);
    $templateProcessor->setValue('pass_num', $application->ClientPasport_number);
    $templateProcessor->setValue('pass_date', $application->ClientPasport_date);
    $templateProcessor->setValue('pass_struct', $application->ClientPasport_struct);

    $i = 1;


    //  dd ($array);

    $filename = "Информационный акт №" . date('d-m-y_hms') . ".docx";

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
    $document->file_name = "Информационный акт №" . $number . ' от ' . $date . ".docx";
    $document->message_id = 0;
    $document->mime_type = mime_content_type($pathToSave);
    $document->id_application = $id;
    $document->file_size = filesize($pathToSave);
    $document->location = $filename;
    $document->id_status = 6;
    $document->save();
    return redirect()->route('document', $id);
  }

  public function export_ds($id)
  {
    $application = DB::table('applications')
      ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
      ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
      ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName')
      ->where('applications.id', '=', $id)
      ->first();
    $templateProcessor = new TemplateProcessor('storage/doc_template/' . $application->id_insuranceCompany . '/ds.docx');
    //dd($application);
    $number = date('Hi');
    $month = date('m');
    $month_name = '';
    switch ($month) {
      case 1:
        $month_name = 'января';
        break;
      case 2:
        $month_name = 'февраля';
        break;
      case 3:
        $month_name = 'марта';
        break;
      case 4:
        $month_name = 'апреля';
        break;
      case 5:
        $month_name = 'мая';
        break;
      case 6:
        $month_name = 'июня';
        break;
      case 7:
        $month_name = 'июля';
        break;
      case 8:
        $month_name = 'августа';
        break;
      case 9:
        $month_name = 'сентября';
        break;
      case 10:
        $month_name = 'октября';
        break;
      case 11:
        $month_name = 'ноября';
        break;
      case 12:
        $month_name = 'декабря';
        break;
    }
    $date = date('d') . ' ' . $month_name . ' ' . date('Y');


    $templateProcessor->setValue('Client', $application->ClientName);
    $templateProcessor->setValue('number', $application->numberInsuranceCompanyApplication . '/' . $application->ub_number);
    $templateProcessor->setValue('Date', $date);
    $templateProcessor->setValue('CarModel', $application->CarModel);
    $templateProcessor->setValue('gnz', $application->gnz);
    $templateProcessor->setValue('vin', $application->vin);
    $templateProcessor->setValue('start_date', $application->start_date);
    $templateProcessor->setValue('end_date', $application->end_date);
    $templateProcessor->setValue('wait_sp', $application->wait_sp);
    $templateProcessor->setValue('pass_ser', $application->ClientPasport);
    $templateProcessor->setValue('pass_num', $application->ClientPasport_number);
    $templateProcessor->setValue('pass_date', $application->ClientPasport_date);
    $templateProcessor->setValue('pass_struct', $application->ClientPasport_struct);
    $array = [];
    $SpareParts = DB::table('spare_parts')
      ->where('id_application', '=', $id)
      ->where('selected_mrk', '>', '0')
      ->where('selected_mrk', '<', '4')
      ->get();
    $i = 1;

    foreach ($SpareParts as $sparepart) {
      $array[] = array('id_sp' => str($i), 'SparePart' => $sparepart->name);
      $i++;
    }
    $templateProcessor->cloneRowAndSetValues('id_sp', $array);
    $array1 = [];
    $Works = DB::table('works')
      ->where('id_application', '=', $id)
      ->where('price_service', '<>', '0')
      ->get();
    $i = 1;
    foreach ($Works as $work) {
      $array1[] = array('num' => str($i), 'Work' => str_replace('<', 'МЕНЕЕ ', $work->name));
      $i++;
    }

    $templateProcessor->cloneRowAndSetValues('num', $array1);




    // dd($array, $array1);

    $filename = "Дополнительное соглашение №" . date('d-m-y_hms') . ".docx";

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
    // dd($templateProcessor);
    $templateProcessor->saveAs($pathToSave);
    $document = new Attachment();
    $document->file_name = "Дополнительное соглашение №" . $number . ' от ' . $date . ".docx";
    $document->message_id = 0;
    $document->mime_type = mime_content_type($pathToSave);
    $document->id_application = $id;
    $document->file_size = filesize($pathToSave);
    $document->location = $filename;
    $document->id_status = 4;
    $document->save();
    return redirect()->route('document', $id);
  }

  public function export_zn($id)
  {
    $application = DB::table('applications')
      ->join('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
      ->join('application_statuses', 'applications.id_status', '=', 'application_statuses.id')
      ->select('applications.*', 'insurance_companies.name', 'application_statuses.StatusName')
      ->where('applications.id', '=', $id)
      ->first();
    $templateProcessor = new TemplateProcessor('storage/doc_template/' . $application->id_insuranceCompany . '/zn.docx');
    //dd($application);
    $num = date('Hi');
    $month = date('m');
    $month_name = '';
    switch ($month) {
      case 1:
        $month_name = 'января';
        break;
      case 2:
        $month_name = 'февраля';
        break;
      case 3:
        $month_name = 'марта';
        break;
      case 4:
        $month_name = 'апреля';
        break;
      case 5:
        $month_name = 'мая';
        break;
      case 6:
        $month_name = 'июня';
        break;
      case 7:
        $month_name = 'июля';
        break;
      case 8:
        $month_name = 'августа';
        break;
      case 9:
        $month_name = 'сентября';
        break;
      case 10:
        $month_name = 'октября';
        break;
      case 11:
        $month_name = 'ноября';
        break;
      case 12:
        $month_name = 'декабря';
        break;
    }
    $date = date('d') . ' ' . $month_name . ' ' . date('Y');


    $templateProcessor->setValue('Client', $application->ClientName);
    $templateProcessor->setValue('num', $num);
    $templateProcessor->setValue('number', $application->numberInsuranceCompanyApplication . '/' . $application->ub_number);
    $templateProcessor->setValue('Date', $date);
    $templateProcessor->setValue('mnf_date', $application->mnf_year);
    $templateProcessor->setValue('CarModel', $application->CarModel);
    $templateProcessor->setValue('gnz', $application->gnz);
    $templateProcessor->setValue('vin', $application->vin);
    $array = [];
    $SpareParts = DB::table('spare_parts')
      ->where('id_application', '=', $id)
      ->where('price', '<>', '0')
      ->get();
    $i = 1;

    foreach ($SpareParts as $sparepart) {
      $array[] = array(
        'SparePart' => $sparepart->name, 'price' => $sparepart->price, 'count' => $sparepart->count, 'summ' => $sparepart->price_ic_full,
        'iznos' => $sparepart->iznos, 'sum_half' => $sparepart->price_ic_half, 'part_number' => $sparepart->part_number
      );
    }
    $templateProcessor->cloneRowAndSetValues('SparePart', $array);
    $array1 = [];
    $Works = DB::table('works')
      ->where('id_application', '=', $id)
      ->where('price', '<>', '0')
      ->get();

    foreach ($Works as $work) {
      $array1[] = array('name' => str_replace('<', 'МЕНЕЕ ', $work->name), 'price_w' => $work->price, 'count_hours' => $work->count_hours, 'sum_w' => $work->summ);
    }
    // dd($array1);

    $templateProcessor->cloneRowAndSetValues('name', $array1);




    // dd($array, $array1);

    $filename = "Заказ-наряд №" . date('d-m-y_hms') . ".docx";

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
    // dd($templateProcessor);
    $templateProcessor->saveAs($pathToSave);
    $document = new Attachment();
    $document->file_name = "Заказ-наряд №" . $num . ' от ' . $date . ".docx";
    $document->message_id = 0;
    $document->mime_type = mime_content_type($pathToSave);
    $document->id_application = $id;
    $document->file_size = filesize($pathToSave);
    $document->location = $filename;
    $document->id_status = 16;
    $document->save();
    return redirect()->route('document', $id);
  }
}
