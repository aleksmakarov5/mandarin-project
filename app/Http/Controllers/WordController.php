<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\DB;
use App\Models\attachment;
use App\Models\estimate;
use App\Models\application;
use App\Models\InsuranceCompany;
use App\Models\troubleshooting;
use Jenssegers\Date\Date;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class WordController extends Controller
{
    public function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) return $f5;
        $n = $n % 10;
        if ($n > 1 && $n < 5) return $f2;
        if ($n == 1) return $f1;
        return $f5;
    }
    public function num2str($num)
    {
        $nul = 'ноль';
        $ten = array(
            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        );
        $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
        $unit = array( // Units
            array('копейка', 'копейки', 'копеек',   1),
            array('рубль', 'рубля', 'рублей', 0),
            array('тысяча', 'тысячи', 'тысяч', 1),
            array('миллион', 'миллиона', 'миллионов', 0),
            array('миллиард', 'милиарда', 'миллиардов', 0),
        );
        //
        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
                else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) $out[] = $this->morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            } //foreach
        } else $out[] = $nul;
        $out[] = $this->morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
        $out[] = $kop . ' ' . $this->morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    public function month2str($month)
    {
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
        return $month_name;
    }
    public function export_doc($id, $template, $filename, $doc_type)
    {


        $location = $filename . date('d-m-y_hms') . ".docx";
        $application = DB::table('applications')
            ->leftjoin('cities', 'applications.id_city', '=', 'cities.id')
            ->leftjoin('our_companies', 'applications.our_company', '=', 'our_companies.id')
            ->leftjoin('legal_entities', 'our_companies.id_legal', '=', 'legal_entities.id')
            ->leftjoin('services', 'applications.id_service', '=', 'services.id')
            ->leftjoin('insurance_companies', 'applications.id_insuranceCompany', '=', 'insurance_companies.id')
            ->select('applications.*', 'cities.name as city', 'legal_entities.*', 'services.service_description', 'our_companies.our_email', 'insurance_companies.name as ic_name', 'insurance_companies.ic_address', 'insurance_companies.ic_fullname', 'insurance_companies.ic_inn', 'insurance_companies.ic_kpp')
            ->where('applications.id', '=', $id)
            ->first();
        // dd($application);
        $ic = InsuranceCompany::find($application->id_insuranceCompany);
        if ($doc_type == 31 || $doc_type == 35) {
            if ($doc_type == 35)
                $template = $application->ub_type ? 'pzn_casco.docx' : 'pzn_osago.docx';
            $templateProcessor = new TemplateProcessor('storage/doc_template/' . $template);
        } else
            $templateProcessor = new TemplateProcessor('storage/doc_template/' . $application->id_insuranceCompany . '/' . $template);
        $estimate = estimate::where('id_application', '=', $id)->first();
        $mrk = DB::table('users')->where('id', '=', $application->id_MRK)->first();
        $mrk_name = '_________';
        $mrk_full = '_________';
        $mrk_phone = '_____________';
        if (isset($mrk->name)) {
            $mrk_full = $mrk->name;
            $mrk_phone = $mrk->phone;
            $mrk_name = $mrk_full;
            $mrk_n = explode(' ', $mrk_name);
            $mrk_name = $mrk_n[0] . ' ' . mb_substr($mrk_n[1], 0, 1, 'UTF-8') . '.';
            if (isset($mrk_n[2]))
                $mrk_name .= ' ' . mb_substr($mrk_n[2], 0, 1, 'UTF-8') . '.';
        }
        $nds = str2number($application->ub_type ? $estimate->price_repair_half : $estimate->price_repair_full) * 0.05;
        $summ_nds = str2number($application->ub_type ? $estimate->price_repair_half : $estimate->price_repair_full) + $nds;
        //dd($application);
        $fio = '';
        if ($application->director) {
            $fio = ($application->director == ' ') ? $application->Ip_name : $application->director;

            $fio = explode(' ', $fio);
            $fio = $fio[0] . ' ' . mb_substr($fio[1], 0, 1, 'UTF-8') . '. ' . mb_substr($fio[2], 0, 1, 'UTF-8') . '.';
        }
        $number = $id;

        $sch_number = 'MG-' . $id . "/" . mb_substr($application->city, 0, 1);

        // dd($sch_number);
        $templateProcessor->setValue('Client', $application->ClientName ?? '________________________________');
        $templateProcessor->setValue('sch_number', $sch_number);

        $templateProcessor->setValue('mrk', $mrk_name);

        $templateProcessor->setValue('mrk_full', $mrk_full);
        $templateProcessor->setValue('mrk_phone', $mrk_phone);
        $templateProcessor->setValue('ic_fullname', $application->ic_fullname);
        $templateProcessor->setValue('number', $number);
        $templateProcessor->setValue('Date', Date::parse()->format('j F Y'));
        $templateProcessor->setValue('CarModel', $application->CarModel);
        $templateProcessor->setValue('gnz', $application->gnz);
        $templateProcessor->setValue('in_face', $application->in_face);
        $templateProcessor->setValue('vin', $application->vin);
        $templateProcessor->setValue('city', $application->city);
        $templateProcessor->setValue('pass_ser', $application->ClientPasport ?? "______");
        $templateProcessor->setValue('pass_num', $application->ClientPasport_number ?? "_____________");
        $templateProcessor->setValue('pass_date',  $application->ClientPasport_date ? Date::parse($application->ClientPasport_date)->format('j F Yг.') : "___ _______________ 20_____г.");
        $templateProcessor->setValue('pass_struct', $application->ClientPasport_struct ?? "_______________________________ _______________________________ _______________________________");
        $templateProcessor->setValue('ub_number', $application->numberInsuranceCompanyApplication);
        $templateProcessor->setValue('mnf_date', isset($application->mnf_year) ? $application->mnf_year : '');
        $templateProcessor->setValue('mnf_year', isset($application->mnf_year) ? $application->mnf_year : '');
        $templateProcessor->setValue('id_insuranceCompany', $application->numberInsuranceCompanyApplication);
        $templateProcessor->setValue('start_date', $application->start_date ? Date::parse($application->start_date)->format('j F Yг.') : "___ _______________ 20_____г.");
        $templateProcessor->setValue('end_date', $application->end_date ? Date::parse($application->end_date)->format('j F Yг.') : "___ _______________ 20_____г.");
        $templateProcessor->setValue('wait_sp', $application->wait_sp);
        $templateProcessor->setValue('phone', $application->ClientPhone);
        $templateProcessor->setValue('birthday',  $application->birthday ? Date::parse($application->birthday)->format('j F Yг.') : "_____ _______________ 20_____г.");
        $templateProcessor->setValue('DTPdate',  $application->DTPdate ? Date::parse($application->DTPdate)->format('j F Yг.') : "_____ _______________ 20_____г.");
        $templateProcessor->setValue('vehicle_passport', $application->vehicle_passport);
        $templateProcessor->setValue('bank', $application->bank);
        $templateProcessor->setValue('inn', $application->inn);
        $templateProcessor->setValue('kpp', $application->kpp);
        $templateProcessor->setValue('ogrn', $application->ogrn);
        $templateProcessor->setValue('okpo', $application->okpo);
        $templateProcessor->setValue('sch_korr', $application->sch_korr);
        $templateProcessor->setValue('bik', $application->bik);
        $templateProcessor->setValue('sch_numb', $application->sch_numb);
        $templateProcessor->setValue('fullname', $application->fullname);
        $templateProcessor->setValue('address', $application->address);
        $templateProcessor->setValue('director', $application->director);
        $templateProcessor->setValue('name', $application->name);
        $templateProcessor->setValue('in_face', $application->in_face);
        $templateProcessor->setValue('service_description', $application->service_description);
        $templateProcessor->setValue('fio', $fio);
        $templateProcessor->setValue('email', $application->our_email);
        if ($application->ub_type)
            $templateProcessor->setValue('osn', $ic->osn_casco);
        else
            $templateProcessor->setValue('osn', $ic->osn_osago);




        if ($doc_type == 11) {
            $troubleshooting = DB::table('troubleshootings')->where('id_application', '=', $id)->get();
            $array = [];
            $i = 1;

            foreach ($troubleshooting as $trouble) {
                $array[] = array('id_row' => $i, 'PartName' => $trouble->PartName . ' - ' . $trouble->Сonclusion, 'Work' => $trouble->Work);
                $i++;
            }
            // dd($array);

            $templateProcessor->cloneRowAndSetValues('PartName', $array);
        }

        if ($doc_type == 4 || $doc_type == 31) {
            $array = [];
            $SpareParts = DB::table('spare_parts')
                ->where('id_application', '=', $id)
                ->where('selected_mrk', '>', '0')
                ->where('selected_mrk', '<', '5')
                ->get();
            $i = 1;

            foreach ($SpareParts as $sparepart) {

                $array[] = array('id_sp' => str($i), 'SparePart' => $sparepart->name);
                $i++;
            }
            $templateProcessor->cloneRowAndSetValues('id_sp', $array);
            $array1 = [];
            $Works = DB::table('plan_works')
                ->where('id_application', '=', $id)
                ->get();
            $i = 1;
            foreach ($Works as $work) {
                $array1[] = array('num' => str($i), 'Work' => str_replace('<', 'МЕНЕЕ ', $work->name));
                $i++;
            }

            $templateProcessor->cloneRowAndSetValues('num', $array1);
        }

        if ($doc_type == 18) {
            $app = application::find($id);
            $app->finance_statuses_id = 9;
            $app->ic_answer_date = date('Y-m-d H:i:s');
            $app->save();
            $array = [];
            $SpareParts = DB::table('spare_parts')
                ->where('id_application', '=', $id)
                ->where('price_ic_full', '<>', '0')
                ->get();
            $i = 1;

            foreach ($SpareParts as $sparepart) {
                $array[] = array(
                    'SparePart' => $sparepart->name,
                    'price' => in_money(
                        $sparepart->price
                    ),
                    'count' => $sparepart->count,
                    'summ' => in_money(
                        $sparepart->price_ic_full
                    ),
                    'iznos' => $sparepart->iznos,
                    'sum_half' => in_money(
                        $sparepart->price_ic_half
                    ),
                    'part_number' => $sparepart->part_number
                );
            }
            try {
                $templateProcessor->cloneRowAndSetValues('SparePart', $array);
            } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
            }
            $array1 = [];
            $Works = DB::table('works')
                ->where('id_application', '=', $id)
                ->where('price', '<>', '0')
                ->orderBy('npp')
                ->get();

            foreach ($Works as $work) {
                $array1[] = array(
                    'work_name' => str_replace('<', 'МЕНЕЕ ', $work->name),
                    'price_w' => str_replace('<', 'МЕНЕЕ ', in_money($work->price)),
                    'count_hours' => str_replace('<', 'МЕНЕЕ ', dec2($work->count_hours)),
                    'sum_w' => str_replace('<', 'МЕНЕЕ ', in_money($work->summ))
                );
            }
            // dd($array1);
            $estimate = estimate::where('id_application', '=', $id)->first();
            try {
                $templateProcessor->cloneRowAndSetValues('work_name', $array1);
            } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
            }
            if (isset($estimate->price_repair_full)) {
                $templateProcessor->setValue('price_repair', in_money($estimate->price_repair));
                $templateProcessor->setValue('price_paint', in_money($estimate->price_paint));
                $templateProcessor->setValue('price_zip', in_money($estimate->price_zip));
                $templateProcessor->setValue('price_work', in_money($estimate->price_work));
                $templateProcessor->setValue('price_sp_full', in_money($application->ub_type ? $estimate->price_sp_half : $estimate->price_sp_full));
                // $templateProcessor->setValue('price_repair_full', in_money($estimate->price_repair_full));
                // $estimate->zn_number = $number . ' от ' . Date::parse()->format('j F Y') . 'г.';
                // $estimate->save();
            }
        }

        if ($doc_type == 35) {
            $array = [];
            $SpareParts = DB::table('spare_parts')
                ->where('id_application', '=', $id)
                ->get();
            $i = 1;
            // dd($SpareParts);
            $price_sp_full = 0;
            $price_sp_half = 0;
            foreach ($SpareParts as $sparepart) {
                $array[] = array(
                    'SparePart' => (($sparepart->price_sto == 0) || (in_money($sparepart->price_ic_full, 0) == in_money($sparepart->price_sto, 0))) || $application->ub_type ? $sparepart->name : bold_text($sparepart->name),
                    'count' => $sparepart->count,
                    'summ' => (($sparepart->price_sto == 0) || (in_money($sparepart->price_ic_full, 0) == in_money($sparepart->price_sto, 0))) || $application->ub_type ? in_money($sparepart->price_ic_full) : bold_text(in_money($sparepart->price_sto)),
                    'iznos' => $sparepart->iznos,
                    'sum_half' => in_money($sparepart->price_ic_half),
                    'part_number' => (($sparepart->price_sto == 0) || (in_money($sparepart->price_ic_full, 0) == in_money($sparepart->price_sto, 0))) || $application->ub_type ? $sparepart->part_number : bold_text($sparepart->part_number)
                );
                $price_sp_full
                    += $sparepart->price_sto == 0 || $application->ub_type
                    ? str2number($sparepart->price_ic_full)
                    : str2number($sparepart->price_sto);
                $price_sp_half += str2number($sparepart->price_ic_half);
            }
            $price_sp_full_md2 = $price_sp_full * 1.02;
            // dd($array);
            $templateProcessor->cloneRowAndSetValues('SparePart', $array);
            $array1 = [];
            $Works = DB::table('works')
                ->where('id_application', '=', $id)
                ->where('summ', '<>', '0')
                ->get();
            $price_repair = 0;
            foreach ($Works as $work) {
                $array1[] = array(
                    'work_name' => str2number(
                        $work->count_hours_pzn
                    ) != 0 ? bold_text(
                        str_replace('<', 'МЕНЕЕ ', $work->name)
                    ) : str_replace('<', 'МЕНЕЕ ', $work->name),
                    'price_w' => str2number(
                        $work->count_hours_pzn
                    ) != 0 ? bold_text(
                        in_money(str2number($work->price))
                    ) : in_money(str2number($work->price)),
                    'count_hours' => str2number(
                        $work->count_hours_pzn
                    ) != 0 ? bold_text(dec2($work->count_hours_pzn))  : dec2(str2number($work->count_hours)),
                    'sum_w' => str2number(
                        $work->count_hours_pzn
                    ) != 0 ? bold_text(in_money(str2number($work->count_hours_pzn) * str2number($work->price))) : in_money(str2number(($work->summ)))
                );
                $price_repair += isset($work->count_hours_pzn) && str2number($work->count_hours_pzn) != 0 ? str2number($work->count_hours_pzn) * str2number($work->price) : str2number($work->summ);
            }
            // dd($array1);
            $estimate = estimate::where('id_application', '=', $id)->first();
            $price_repair_half = $price_repair + str2number((isset($estimate->price_zip) ? str2number($estimate->price_zip) : 0)) + $price_sp_half;
            $price_repair_full = $price_repair + str2number((isset($estimate->price_zip) ? str2number($estimate->price_zip) : 0)) + $price_sp_full;
            $nds = str2number($application->ub_type ? $price_repair_half : $price_repair_full) * 0.05;
            $summ_nds = str2number($application->ub_type ? $price_repair_half : $price_repair_full) + $nds;


            $price_repair_full_md2 = $price_sp_full_md2 +
                (isset($estimate->price_zip) ? str2number($estimate->price_zip) : 0)
                + $price_repair;


            // $price_repair_full = str2number($estimate->price_repair_full);
            $templateProcessor->cloneRowAndSetValues('work_name', $array1);
            $templateProcessor->setValue('price_repair', in_money($price_repair));
            $templateProcessor->setValue('price_zip', in_money(isset($estimate->price_zip) ? str2number($estimate->price_zip) : 0));
            $templateProcessor->setValue('price_sp_full', in_money($application->ub_type ? $price_sp_half : $price_sp_full));
            $templateProcessor->setValue('price_sp_full_md2', in_money($price_sp_full_md2));
            $templateProcessor->setValue('price_repair_full', in_money($application->ub_type ? $price_repair_half : $price_repair_full));
            $templateProcessor->setValue('price_repair_full_md2', in_money($price_repair_full_md2));

            $templateProcessor->setValue('summ_str', $this->num2str($summ_nds));
        }
        $templateProcessor->setValue('summ_nds', in_money($summ_nds));
        $templateProcessor->setValue('nds', in_money($nds));
        $price_sp_full = 0;
        $price_repair_full = 0;
        if ($doc_type == 18 || $doc_type == 37) {
            $SpareParts = DB::table('spare_parts')
                ->where('id_application', '=', $id)
                ->where('price_ic_full', '<>', '0')
                ->get();
            $i = 1;
            // dd($SpareParts);

            foreach ($SpareParts as $sparepart) {

                $price_sp_full
                    +=
                    str2number($sparepart->price_ic_full);
            }
            $price_sp_full_md2 = $price_sp_full * 1.02;
            // dd($array);
            $price_sp_full = $application->ub_type ?  $estimate->price_sp_half : $price_sp_full;
            $Works = DB::table('works')
                ->where('id_application', '=', $id)
                ->where('summ', '<>', '0')
                ->get();
            $price_repair = 0;
            foreach ($Works as $work) {

                $price_repair += str2number($work->summ);
            }
            // dd($array1);
            $estimate = estimate::where('id_application', '=', $id)->first();


            $price_repair_full_md2 = $price_sp_full_md2 +
                (isset($estimate->price_zip) ? str2number($estimate->price_zip) : 0)
                + $price_repair;

            $price_repair_full = $price_sp_full +
                (isset($estimate->price_zip) ? str2number($estimate->price_zip) : 0)
                + $price_repair;
            $price_repair_full = str2number($estimate->price_repair_full);
            $price_sp_full = $application->ub_type ?  $estimate->price_sp_half : $price_sp_full;
            $price_repair_full = $application->ub_type ?  $estimate->price_repair_half : $price_repair_full;


            $templateProcessor->setValue('zn_num', $estimate->zn_number);
            $templateProcessor->setValue('price_repair_full', in_money($price_repair_full));
            $templateProcessor->setValue('summ_str', $this->num2str($summ_nds));
        }



        //  dd ($array);

        $dir = 'storage/files/' . $id;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $i = 1;
        if (file_exists($dir . '/' . $location)) {
            while (file_exists($dir . '/' . $location))
                $location .= '(' . $i++ . ')';
        }
        if ($doc_type == 35) {
            if ($application->id_insuranceCompany == 3) {
                $file_save = 'public/files/' . $id . '/таблица_запчастей_' . date('d-m-y_hms') . ".xlsx";
                Excel::store(new ReportExport($id), $file_save);
                $file_save = $dir . '/таблица_запчастей_' . date('d-m-y_hms') . ".xlsx";
                $document = new Attachment();
                $document->file_name = 'таблица_запчастей_' . $number . ' от ' . Date::parse()->format('j F Y') . ".xlsx";
                $document->message_id = 0;
                $document->mime_type = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                $document->id_application = $id;
                $document->file_size = 0;
                // sleep(100);
                try {
                    $document->file_size = filesize($file_save);
                } catch (\Exception $e) {
                    sleep(100);
                    $document->file_size = filesize($file_save);
                }
                $document->location = 'таблица_запчастей_' . date('d-m-y_hms') . ".xlsx";
                $document->id_status = $doc_type;
                $document->save();
            }
        }


        $pathToSave = $dir . '/' . $location;
        $templateProcessor->saveAs($pathToSave);
        $document = new Attachment();
        $document->file_name = $filename . $number . ' от ' . Date::parse()->format('j F Y') . ".docx";
        $document->message_id = 0;
        $document->mime_type = mime_content_type($pathToSave);
        $document->id_application = $id;
        $document->file_size = filesize($pathToSave);
        $document->location = $location;
        $document->id_status = $doc_type;
        if ($doc_type != 18)
            $document->sto_view = true;
        $document->save();
        exec('chmod 755 "' . $pathToSave . '"');
        exec('libreoffice --headless --convert-to pdf "' . $pathToSave . '" --outdir ' . $dir);
        if ($doc_type == 18) {
            $location = "УПД" . date('d-m-y_hms') . ".docx";
            $filename = "УПД_";
            $number = $id;
            $templateProcessor = new TemplateProcessor('storage/doc_template/upd.docx');
            //dd($application);
            $fio = '';
            if ($application->ub_type)
                $templateProcessor->setValue('osn', $ic->osn_casco);
            else
                $templateProcessor->setValue('osn', $ic->osn_osago);
            $templateProcessor->setValue('dolzh', $application->dolzh);
            $templateProcessor->setValue('ogrnip', $application->ogrnip);
            $templateProcessor->setValue('Buh', $application->Buh);
            $templateProcessor->setValue('Ip_name', $application->Ip_name);
            $templateProcessor->setValue('Director', $application->director);

            if ($application->director) {
                $fio = ($application->director == ' ') ? $application->Ip_name : $application->director;
                $fio = explode(' ', $fio);
                $fio = $fio[0] . ' ' . mb_substr($fio[1], 0, 1, 'UTF-8') . '. ' . mb_substr($fio[2], 0, 1, 'UTF-8') . '.';
            }

            $number = $id;
            // dd($number);
            $sch_number = 'MG-' . $id . "/" . mb_substr($application->city, 0, 1);

            // dd($sch_number);
            $estimate = estimate::where('id_application', '=', $id)->first();

            if (isset($estimate->price_repair)) {
                $templateProcessor->setValue('zn_num', $estimate->zn_number);
                $templateProcessor->setValue('price_repair_full', in_money($estimate->price_repair_full));
                $templateProcessor->setValue('summ_nds', in_money($summ_nds));
                $templateProcessor->setValue('nds', in_money($nds));
                $templateProcessor->setValue('summ_str', $this->num2str($summ_nds));
            }
            $templateProcessor->setValue('Client', $application->ClientName ?? '_____________________________________________________________');
            $templateProcessor->setValue('sch_number', $sch_number);
            $templateProcessor->setValue('number', $number);
            $templateProcessor->setValue('Date', Date::parse()->format('j F Y'));
            $templateProcessor->setValue('CarModel', $application->CarModel);
            $templateProcessor->setValue('gnz', $application->gnz);
            $templateProcessor->setValue('vin', $application->vin);
            $templateProcessor->setValue('city', $application->city);
            $templateProcessor->setValue('pass_ser', $application->ClientPasport ?? "______");
            $templateProcessor->setValue('pass_num', $application->ClientPasport_number ?? "_____________");
            $templateProcessor->setValue('pass_date', $application->ClientPasport_date ? Date::parse($application->ClientPasport_date)->format('j F Yг.') : "___ _______________ 20_____г.");
            $templateProcessor->setValue('pass_struct', $application->ClientPasport_struct ?? "_______________________________ _______________________________ _______________________________");
            $templateProcessor->setValue('ub_number', $application->numberInsuranceCompanyApplication);
            $templateProcessor->setValue('mnf_year', $application->mnf_year);
            $templateProcessor->setValue('id_insuranceCompany', $application->numberInsuranceCompanyApplication);
            $templateProcessor->setValue('start_date', $application->start_date ? Date::parse($application->start_date)->format('j F Yг.') : "___ _______________ 20_____г.");
            $templateProcessor->setValue('end_date', $application->end_date ? Date::parse($application->end_date)->format('j F Yг.') : "___ _______________ 20_____г.");
            $templateProcessor->setValue('wait_sp', $application->wait_sp);
            $templateProcessor->setValue('phone', $application->ClientPhone);
            $templateProcessor->setValue('birthday', $application->birthday ? Date::parse($application->birthday)->format('j F Yг.') : "_____ _______________ 20_____г.");
            $templateProcessor->setValue('DTPdate', $application->DTPdate ? Date::parse($application->DTPdate)->format('j F Yг.') : "_____ _______________ 20_____г.");
            $templateProcessor->setValue('napr_date', $application->napr_date ? Date::parse($application->napr_date)->format('j F Yг.') : "_____ _______________ 20_____г.");
            $templateProcessor->setValue('vehicle_passport', $application->vehicle_passport);
            $templateProcessor->setValue('bank', $application->bank);
            $templateProcessor->setValue('inn', $application->inn);
            $templateProcessor->setValue('kpp', $application->kpp);
            $templateProcessor->setValue('ogrn', $application->ogrn);
            $templateProcessor->setValue('okpo', $application->okpo);

            $templateProcessor->setValue('sch_korr', $application->sch_korr);
            $templateProcessor->setValue('bik', $application->bik);
            $templateProcessor->setValue('sch_numb', $application->sch_numb);
            $templateProcessor->setValue('fullname', $application->fullname);
            $templateProcessor->setValue('address', $application->address);
            $templateProcessor->setValue('director', $application->director);
            $templateProcessor->setValue('name', $application->name);
            $templateProcessor->setValue('ic_name', $application->ic_name);
            $templateProcessor->setValue('in_face', $application->in_face);
            $templateProcessor->setValue('service_description', $application->service_description);
            $templateProcessor->setValue('fio', $fio);
            $templateProcessor->setValue('email', $application->our_email);
            $templateProcessor->setValue('ic_fullname', $application->ic_fullname);
            $templateProcessor->setValue('ic_address', $application->ic_address);
            $templateProcessor->setValue('ic_inn', $application->ic_inn);
            $templateProcessor->setValue('ic_kpp', $application->ic_kpp);
            $pathToSave = $dir . '/' . $location;
            $templateProcessor->saveAs($pathToSave);
            $document = new Attachment();
            $document->file_name = $filename . $number . ' от ' . Date::parse()->format('j F Y') . ".docx";
            $document->message_id = 0;
            $document->mime_type = mime_content_type($pathToSave);
            $document->id_application = $id;
            $document->file_size = filesize($pathToSave);
            $document->location = $location;
            $document->id_status = $doc_type;
            $document->save();
            exec('chmod 755 "' . $pathToSave . '"');
            exec('libreoffice --headless --convert-to pdf "' . $pathToSave . '" --outdir ' . $dir);

            if ($application->id_insuranceCompany == 3) {


                $location = "Счет_дефектовка" . date('d-m-y_hms') . ".docx";
                $filename = "Счет_дефектовка";
                $templateProcessor = new TemplateProcessor('storage/doc_template/' . $application->id_insuranceCompany . '/sch_def.docx');
                //dd($application);
                $fio = '';
                if ($application->director) {
                    $fio = ($application->director == ' ') ? $application->Ip_name : $application->director;

                    $fio = explode(' ', $fio);
                    $fio = $fio[0] . ' ' . mb_substr($fio[1], 0, 1, 'UTF-8') . '. ' . mb_substr($fio[2], 0, 1, 'UTF-8') . '.';
                }
                $number = $id;
                $sch_number = 'MG-' . $id . "/" . mb_substr($application->city, 0, 1);
                // dd($sch_number);
                $estimate = estimate::where('id_application', '=', $id)->first();

                if (isset($estimate->price_repair)) {
                    $nds = (isset($estimate->add_pay) ? $estimate->add_pay : 0) * 0.05;
                    $summ_nds = (isset($estimate->add_pay) ? $estimate->add_pay : 0) + $nds;
                    $templateProcessor->setValue('zn_num', $estimate->zn_number);

                    $templateProcessor->setValue('nds', $nds);
                    $templateProcessor->setValue('summ_nds', $summ_nds);
                    $templateProcessor->setValue('price_repair_full', in_money(isset($estimate->add_pay) ? $estimate->add_pay : 0));
                    $templateProcessor->setValue('summ_str', $this->num2str($summ_nds));
                }
                $templateProcessor->setValue('Client', $application->ClientName ?? '_____________________________________________________________');
                $templateProcessor->setValue('sch_number', $sch_number);
                $templateProcessor->setValue('number', $number);
                $templateProcessor->setValue('Date', Date::parse()->format('j F Y'));
                $templateProcessor->setValue('CarModel', $application->CarModel);
                $templateProcessor->setValue('gnz', $application->gnz);
                $templateProcessor->setValue('vin', $application->vin);
                $templateProcessor->setValue('city', $application->city);
                $templateProcessor->setValue('pass_ser', $application->ClientPasport ?? "______");
                $templateProcessor->setValue('pass_num', $application->ClientPasport_number ?? "_____________");
                $templateProcessor->setValue('pass_date', $application->ClientPasport_date ? Date::parse($application->ClientPasport_date)->format('j F Yг.') : "___ _______________ 20_____г.");
                $templateProcessor->setValue('pass_struct', $application->ClientPasport_struct ?? "_______________________________ _______________________________ _______________________________");
                $templateProcessor->setValue('ub_number', $application->numberInsuranceCompanyApplication);
                $templateProcessor->setValue('mnf_year', $application->mnf_year);
                $templateProcessor->setValue('id_insuranceCompany', $application->numberInsuranceCompanyApplication);
                $templateProcessor->setValue('start_date', $application->start_date ? Date::parse($application->start_date)->format('j F Yг.') : "___ _______________ 20_____г.");
                $templateProcessor->setValue('end_date', $application->end_date ? Date::parse($application->end_date)->format('j F Yг.') : "___ _______________ 20_____г.");
                $templateProcessor->setValue('wait_sp', $application->wait_sp);
                $templateProcessor->setValue('phone', $application->ClientPhone);
                $templateProcessor->setValue('birthday', $application->birthday ? Date::parse($application->birthday)->format('j F Yг.') : "_____ _______________ 20_____г.");
                $templateProcessor->setValue('DTPdate', $application->DTPdate ? Date::parse($application->DTPdate)->format('j F Yг.') : "_____ _______________ 20_____г.");
                $templateProcessor->setValue('vehicle_passport', $application->vehicle_passport);
                $templateProcessor->setValue('bank', $application->bank);
                $templateProcessor->setValue('inn', $application->inn);
                $templateProcessor->setValue('kpp', $application->kpp);
                $templateProcessor->setValue('ogrn', $application->ogrn);
                $templateProcessor->setValue('okpo', $application->okpo);
                $templateProcessor->setValue('sch_korr', $application->sch_korr);
                $templateProcessor->setValue('bik', $application->bik);
                $templateProcessor->setValue('sch_numb', $application->sch_numb);
                $templateProcessor->setValue('fullname', $application->fullname);
                $templateProcessor->setValue('address', $application->address);
                $templateProcessor->setValue('director', $application->director);
                $templateProcessor->setValue('name', $application->name);
                $templateProcessor->setValue('in_face', $application->in_face);
                $templateProcessor->setValue('service_description', $application->service_description);
                $templateProcessor->setValue('fio', $fio);
                $templateProcessor->setValue('email', $application->our_email);
                $pathToSave = $dir . '/' . $location;
                $templateProcessor->saveAs($pathToSave);
                $document = new Attachment();
                $document->file_name = $filename . $number . ' от ' . Date::parse()->format('j F Y') . ".docx";
                $document->message_id = 0;
                $document->mime_type = mime_content_type($pathToSave);
                $document->id_application = $id;
                $document->file_size = filesize($pathToSave);
                $document->location = $location;
                $document->id_status = $doc_type;
                $document->save();
                exec('chmod 755 "' . $pathToSave . '"');
                exec('libreoffice --headless --convert-to pdf "' . $pathToSave . '" --outdir ' . $dir);
                $location = "УПД_дефектовка" . date('d-m-y_hms') . ".docx";
                $filename = "УПД_дефектовка_";
                $number = $id;
                $templateProcessor = new TemplateProcessor('storage/doc_template/upd_def.docx');
                //dd($application);
                $fio = '';
                if ($application->ub_type)
                    $templateProcessor->setValue('osn', $ic->osn_casco);
                else
                    $templateProcessor->setValue('osn', $ic->osn_osago);
                $templateProcessor->setValue('dolzh', !$application->ip ? 'Директор' : 'Индивидуальный предприниматель');

                if ($application->director) {
                    $fio = ($application->director == ' ') ? $application->Ip_name : $application->director;

                    $fio = explode(' ', $fio);
                    $fio = $fio[0] . ' ' . mb_substr($fio[1], 0, 1, 'UTF-8') . '. ' . mb_substr($fio[2], 0, 1, 'UTF-8') . '.';
                }

                $number = $id;
                // dd($number);
                $sch_number = 'MG-' . $id . "/" . mb_substr($application->city, 0, 1);

                // dd($sch_number);
                $estimate = estimate::where('id_application', '=', $id)->first();

                if (isset($estimate->price_repair)) {
                    $templateProcessor->setValue('zn_num', $estimate->zn_number);
                    $templateProcessor->setValue('price_repair_full', in_money($estimate->add_pay));
                    $templateProcessor->setValue('summ_nds', in_money($summ_nds));
                    $templateProcessor->setValue('nds', in_money($nds));
                    $templateProcessor->setValue('summ_str', $this->num2str($summ_nds));
                }
                $templateProcessor->setValue('Client', $application->ClientName ?? '_____________________________________________________________');
                $templateProcessor->setValue('sch_number', $sch_number);
                $templateProcessor->setValue('number', $number);
                $templateProcessor->setValue('Date', Date::parse()->format('j F Y'));
                $templateProcessor->setValue('CarModel', $application->CarModel);
                $templateProcessor->setValue('gnz', $application->gnz);
                $templateProcessor->setValue('vin', $application->vin);
                $templateProcessor->setValue('city', $application->city);
                $templateProcessor->setValue('pass_ser', $application->ClientPasport ?? "______");
                $templateProcessor->setValue('pass_num', $application->ClientPasport_number ?? "_____________");
                $templateProcessor->setValue('pass_date', $application->ClientPasport_date ? Date::parse($application->ClientPasport_date)->format('j F Yг.') : "___ _______________ 20_____г.");
                $templateProcessor->setValue('pass_struct', $application->ClientPasport_struct ?? "_______________________________ _______________________________ _______________________________");
                $templateProcessor->setValue('ub_number', $application->numberInsuranceCompanyApplication);
                $templateProcessor->setValue('mnf_year', $application->mnf_year);
                $templateProcessor->setValue('id_insuranceCompany', $application->numberInsuranceCompanyApplication);
                $templateProcessor->setValue('start_date', $application->start_date ? Date::parse($application->start_date)->format('j F Yг.') : "___ _______________ 20_____г.");
                $templateProcessor->setValue('end_date', $application->end_date ? Date::parse($application->end_date)->format('j F Yг.') : "___ _______________ 20_____г.");
                $templateProcessor->setValue('wait_sp', $application->wait_sp);
                $templateProcessor->setValue('phone', $application->ClientPhone);
                $templateProcessor->setValue('birthday', $application->birthday ? Date::parse($application->birthday)->format('j F Yг.') : "_____ _______________ 20_____г.");
                $templateProcessor->setValue('DTPdate', $application->DTPdate ? Date::parse($application->DTPdate)->format('j F Yг.') : "_____ _______________ 20_____г.");
                $templateProcessor->setValue('napr_date', $application->napr_date ? Date::parse($application->napr_date)->format('j F Yг.') : "_____ _______________ 20_____г.");
                $templateProcessor->setValue('vehicle_passport', $application->vehicle_passport);
                $templateProcessor->setValue('bank', $application->bank);
                $templateProcessor->setValue('inn', $application->inn);
                $templateProcessor->setValue('kpp', $application->kpp);
                $templateProcessor->setValue('ogrn', $application->ogrn);
                $templateProcessor->setValue('okpo', $application->okpo);

                $templateProcessor->setValue('sch_korr', $application->sch_korr);
                $templateProcessor->setValue('bik', $application->bik);
                $templateProcessor->setValue('sch_numb', $application->sch_numb);
                $templateProcessor->setValue('fullname', $application->fullname);
                $templateProcessor->setValue('address', $application->address);
                $templateProcessor->setValue('director', $application->director);
                $templateProcessor->setValue('name', $application->name);
                $templateProcessor->setValue('in_face', $application->in_face);
                $templateProcessor->setValue('service_description', $application->service_description);
                $templateProcessor->setValue('fio', $fio);
                $templateProcessor->setValue('email', $application->our_email);
                $templateProcessor->setValue('ic_fullname', $application->ic_fullname);
                $templateProcessor->setValue('ic_address', $application->ic_address);
                $templateProcessor->setValue('ic_inn', $application->ic_inn);
                $templateProcessor->setValue('ic_kpp', $application->ic_kpp);
                $pathToSave = $dir . '/' . $location;
                $templateProcessor->saveAs($pathToSave);
                $document = new Attachment();
                $document->file_name = $filename . $number . ' от ' . Date::parse()->format('j F Y') . ".docx";
                $document->message_id = 0;
                $document->mime_type = mime_content_type($pathToSave);
                $document->id_application = $id;
                $document->file_size = filesize($pathToSave);
                $document->location = $location;
                $document->id_status = $doc_type;
                $document->save();
                exec('chmod 755 "' . $pathToSave . '"');
                exec('libreoffice --headless --convert-to pdf "' . $pathToSave . '" --outdir ' . $dir);
            } else {
                // if ($application->id_insuranceCompany != 1) {
                //     $location = "Акт_выполненных_работ_" . date('d-m-y_hms') . ".docx";
                //     $filename = "Акт_выполненных_работ_";
                //     $number = $id;
                //     $templateProcessor = new TemplateProcessor('storage/doc_template/' . $application->id_insuranceCompany . '/avr.docx');
                //     //dd($application);
                //     $fio = '';
                //     if ($application->ub_type)
                //         $templateProcessor->setValue('osn', $ic->osn_casco);
                //     else
                //         $templateProcessor->setValue('osn', $ic->osn_osago);


                //     if ($application->director) {
                //         $fio = $application->director;
                //         $fio = explode(' ', $fio);
                //         $fio = $fio[0] . ' ' . mb_substr($fio[1], 0, 1, 'UTF-8') . '. ' . mb_substr($fio[2], 0, 1, 'UTF-8') . '.';
                //     }
                //     $number = $id;
                //     // dd($number);
                //     $sch_number = 'MG-' . $id . "/" . mb_substr($application->city, 0, 1);

                //     // dd($sch_number);
                //     $estimate = estimate::where('id_application', '=', $id)->first();

                //     if (isset($estimate->price_repair)) {
                //         $templateProcessor->setValue('zn_num', $estimate->zn_number);
                //         $templateProcessor->setValue('price_repair_full', in_money($estimate->price_repair_full));
                //         $templateProcessor->setValue('summ_nds', in_money($summ_nds));
                //         $templateProcessor->setValue('nds', in_money($nds));
                //         $templateProcessor->setValue('summ_str', $this->num2str($summ_nds));
                //     }
                //     $templateProcessor->setValue('Client', $application->ClientName ?? '_____________________________________________________________');
                //     $templateProcessor->setValue('sch_number', $sch_number);
                //     $templateProcessor->setValue('number', $number);
                //     $templateProcessor->setValue('Date', Date::parse()->format('j F Y'));
                //     $templateProcessor->setValue('CarModel', $application->CarModel);
                //     $templateProcessor->setValue('gnz', $application->gnz);
                //     $templateProcessor->setValue('vin', $application->vin);
                //     $templateProcessor->setValue('city', $application->city);
                //     $templateProcessor->setValue('pass_ser', $application->ClientPasport ?? "______");
                //     $templateProcessor->setValue('pass_num', $application->ClientPasport_number ?? "_____________");
                //     $templateProcessor->setValue('pass_date', $application->ClientPasport_date ? Date::parse($application->ClientPasport_date)->format('j F Yг.') : "___ _______________ 20_____г.");
                //     $templateProcessor->setValue('pass_struct', $application->ClientPasport_struct ?? "_______________________________ _______________________________ _______________________________");
                //     $templateProcessor->setValue('ub_number', $application->numberInsuranceCompanyApplication);
                //     $templateProcessor->setValue('mnf_year', $application->mnf_year);
                //     $templateProcessor->setValue('id_insuranceCompany', $application->numberInsuranceCompanyApplication);
                //     $templateProcessor->setValue('start_date', $application->start_date ? Date::parse($application->start_date)->format('j F Yг.') : "___ _______________ 20_____г.");
                //     $templateProcessor->setValue('end_date', $application->end_date ? Date::parse($application->end_date)->format('j F Yг.') : "___ _______________ 20_____г.");
                //     $templateProcessor->setValue('wait_sp', $application->wait_sp);
                //     $templateProcessor->setValue('phone', $application->ClientPhone);
                //     $templateProcessor->setValue('birthday', $application->birthday ? Date::parse($application->birthday)->format('j F Yг.') : "_____ _______________ 20_____г.");
                //     $templateProcessor->setValue('DTPdate', $application->DTPdate ? Date::parse($application->DTPdate)->format('j F Yг.') : "_____ _______________ 20_____г.");
                //     $templateProcessor->setValue('napr_date', $application->napr_date ? Date::parse($application->napr_date)->format('j F Yг.') : "_____ _______________ 20_____г.");
                //     $templateProcessor->setValue('vehicle_passport', $application->vehicle_passport);
                //     $templateProcessor->setValue('bank', $application->bank);
                //     $templateProcessor->setValue('inn', $application->inn);
                //     $templateProcessor->setValue('kpp', $application->kpp);
                //     $templateProcessor->setValue('ogrn', $application->ogrn);
                //     $templateProcessor->setValue('okpo', $application->okpo);

                //     $templateProcessor->setValue('sch_korr', $application->sch_korr);
                //     $templateProcessor->setValue('bik', $application->bik);
                //     $templateProcessor->setValue('sch_numb', $application->sch_numb);
                //     $templateProcessor->setValue('fullname', $application->fullname);
                //     $templateProcessor->setValue('address', $application->address);
                //     $templateProcessor->setValue('director', $application->director);
                //     $templateProcessor->setValue('name', $application->name);
                //     $templateProcessor->setValue('in_face', $application->in_face);
                //     $templateProcessor->setValue('service_description', $application->service_description);
                //     $templateProcessor->setValue('fio', $fio);
                //     $templateProcessor->setValue('email', $application->our_email);
                //     $pathToSave = $dir . '/' . $location;
                //     $templateProcessor->saveAs($pathToSave);
                //     $document = new Attachment();
                //     $document->file_name = $filename . $number . ' от ' . Date::parse()->format('j F Y') . ".docx";
                //     $document->message_id = 0;
                //     $document->mime_type = mime_content_type($pathToSave);
                //     $document->id_application = $id;
                //     $document->file_size = filesize($pathToSave);
                //     $document->location = $location;
                //     $document->id_status = $doc_type;
                //     $document->save();
                //     exec('chmod 755 "' . $pathToSave . '"');
                //     exec('libreoffice --headless --convert-to pdf "' . $pathToSave . '" --outdir ' . $dir);
                // }
            }
        }
    }

    /**
     * Склоняем словоформу
     * @ author runcore
     */


    public function export_hda($id)
    {
        $template = 'hda.docx';
        $filename = "Акт скрытых повреждений №";
        $doc_type = 11;
        $this->export_doc($id, $template, $filename,  $doc_type);
        return redirect()->route('document', $id);
    }

    public function export_dk($id)
    {
        $app = application::findOrFail($id);
        if ($app->id_status < 9)
            $app->id_status = 9;
        $app->save();
        $template = 'dk.docx';
        $filename = "Договор о проведении восстановительного ремонта №";
        $doc_type = 2;
        $this->export_doc($id, $template, $filename,  $doc_type);
        return redirect()->route('document', $id);
    }

    public function export_ap($id, $status_id)
    {
        $template = 'ap.docx';
        $filename = "Акт приема автомобиля №";
        $doc_type = $status_id;
        $this->export_doc($id, $template, $filename,  $doc_type);
        return redirect()->route('document', $id);
    }

    public function export_as($id, $status_id)
    {
        $template = 'as.docx';
        $filename = "АКТ СДАЧИ ЛЕГКОВОГО АВТОМОБИЛЯ №";
        $doc_type = $status_id;
        $this->export_doc($id, $template, $filename,  $doc_type);
        return redirect()->route('document', $id);
    }

    public function export_if($id)
    {
        $template = 'if.docx';
        $filename = "Информационный акт №";
        $doc_type = 6;
        $this->export_doc($id, $template, $filename,  $doc_type);
        return redirect()->route('document', $id);
    }
    public function export_sogl($id)
    {
        $template = 'sogl.docx';
        $filename = "СОГЛАШЕНИЕ О РЕМОНТЕ №";
        $doc_type = 39;
        $this->export_doc($id, $template, $filename,  $doc_type);
        return redirect()->route('document', $id);
    }
    public function export_zayav($id)
    {
        $template = 'zayav.docx';
        $filename = "Заявление о страховом возмещении №";
        $doc_type = 38;
        $this->export_doc($id, $template, $filename,  $doc_type);
        return redirect()->route('document', $id);
    }

    public function export_ds($id)
    {
        $template = 'ds.docx';
        $filename = "Дополнительное соглашение №";
        $doc_type = 4;
        $this->export_doc($id, $template, $filename,  $doc_type);
        return redirect()->route('document', $id);
    }

    public function export_zn($id)
    {
        $template = 'zn.docx';
        $filename = "Заказ-наряд №";
        $doc_type = 16;
        $this->export_doc($id, $template, $filename,  $doc_type);
        return redirect()->route('document', $id);
    }

    public function export_pzn($id)
    {
        $template = 'pzn.docx';
        $filename = "Предварительный заказ-наряд №";
        $doc_type = 35;
        $this->export_doc($id, $template, $filename,  $doc_type);
        return redirect()->route('document', $id);
    }
    public function export_sch($id)
    {
        $template = 'sch.docx';
        $filename = "Счет №";
        $doc_type = 18;
        $this->export_doc($id, $template, $filename,  $doc_type);
        return redirect()->route('document', $id);
    }
    public function export_rv($id)
    {
        $template = 'rv.docx';
        $filename = "Ремонтная ведомость ";
        $doc_type = 31;
        $this->export_doc($id, $template, $filename,  $doc_type);
        return redirect()->route('document', $id);
    }
    public function create_all($id, $status_id)
    {
        $this->export_dk($id);
        $this->export_ds($id);
        $this->export_if($id);
        $this->export_ap($id, $status_id);
        $this->export_as($id, $status_id);
        $this->export_hda($id);
        $this->export_zn($id);
        $this->export_sch($id);
        return redirect()->route('document', $id);
    }
    public function export($id)
    {
        $spareparts = DB::table('spare_parts')
            ->where('id_application', '=', $id)
            ->leftJoin(
                'sparepart_statuses',
                'sparepart_statuses.id',
                '=',
                'spare_parts.status_id'
            )
            ->select('spare_parts.*', 'sparepart_statuses.StatusName')
            ->get();

        // return view('exportReport', ['spareparts' => $spareparts]);
        // return Excel::download(new ReportExport($id), 'Запчасти.xlsx');
    }
}
