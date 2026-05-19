<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\work;
use App\Models\application;




class WorkController extends Controller
{
    public function count($id)
    {
        $spareparts = DB::table('spare_parts')->where('id_application', '=', $id)->get();
        $estimates = DB::table('estimates')->where('id_application', '=', $id)->first();
        $Works_serv = DB::table('works')
            ->where('id_application', '=', $id)
            ->where('price_service', '<>', 0)
            ->get();
        $summ_service = 0;
        foreach ($Works_serv as $work) {
            $summ_service = $summ_service + (int) str_replace(' ', '', $work->price_service);
        }


        // dd($rewiew->all()) ;
        $summ_ic_full = 0;
        $summ_ic_half = 0;
        $summ_order = 0;
        $delta_full = 0;
        $delta_half = 0;
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
        if (isset($estimates->price_repair_full)) {
            $delta_full = $estimates->price_repair_full - $summ_order - $summ_service;
        } else {
            $delta_full =  $summ_ic_full - $summ_order - $summ_service;
        }
        if (isset($estimates->price_repair_half)) {
            $delta_half = $estimates->price_repair_half - $summ_order - $summ_service;
        } else {
            $delta_half =  $summ_ic_half - $summ_order - $summ_service;
        }


        return [
            'delta_full' => $delta_full,
            'delta_half' => $delta_half,
        ];
    }
    public function Work($id)
    {
        $eco = '';

        $Works_ic = DB::table('works')
            ->where('id_application', '=', $id)
            ->where('summ', '<>', 0)
            ->get();
        $Works_serv = DB::table('works')
            ->where('id_application', '=', $id)
            ->where('price_service', '<>', 0)
            ->get();
        // dd($rewiew->all()) ;
        $summ_ic = 0;
        $summ_service = 0;
        $summ_pzn = 0;
        foreach ($Works_ic as $work) {
            $summ_ic = $summ_ic + str2number($work->summ);
            $summ_pzn += isset($work->count_hours_pzn) ? $work->count_hours_pzn * str2number($work->price) : str2number($work->summ);
        }
        foreach ($Works_serv as $work) {
            $summ_service = $summ_service + (int)str_replace(' ', '', $work->price_service);
        }
        $delta = $summ_service - $summ_ic;
        return view('Work.index', [
            'Works' => $Works_ic->all(),
            "Works_serv" => $Works_serv->all(),
            'id' => $id,
            'delta' => $delta,
            'summ_ic' => $summ_ic,
            'summ_service' => $summ_service,
            'eco' => $eco,
            'summ_pzn' => $summ_pzn,
        ]);
    }

    public function create($id)
    {
        $Work = new Work;
        return view('Work.create', ['Work' => $Work->all()], ['id' => $id]);
    }

    public function store(Request $request)
    {
        $Work = new Work;
        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required',
            'summ' => 'required',
            'price_service' => 'nullable',
            'author' => 'nullable',


        ]);

        foreach ($data as $key => $value) {
            $Work->$key = $value;
        }
        $Work->save();
        return redirect()
            ->route('SparePart', $Work->id_application);
    }

    public function edit($id)
    {
        $Work = Work::findOrFail($id);

        return view('SparePart.edit', ['Work' => $Work]);
    }
    public function update(Request $request, $id)
    {
        $Work = Work::findOrFail($id);

        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required',
            'summ' => 'nullable',
            'price_service' => 'nullable',
            'count_hours_pzn' => 'nullable'

        ]);
        $data['count_hours_pzn'] = round(str2number($data['count_hours_pzn']), 2);


        // dd($data);
        foreach ($data as $key => $val) {
            $Work->$key = $val;
        }
        $Work->save();
        return redirect()
            ->to(route('SparePart', $Work->id_application) . '#' . $request->id);
    }

    public function doc_load($id)
    {
        $documents = DB::table('attachments')
            ->where('id_application', '=', $id)
            ->get();
        return view('Work.doc_load', ['id' => $id], ['documents' => $documents]);
    }

    public function data_load(Request $request, $id)
    {

        session(['works' => []]);
        session(['works_paint' => []]);
        $type = 0;
        if (pathinfo($request['location'], PATHINFO_EXTENSION) == 'doc' || pathinfo($request['location'], PATHINFO_EXTENSION) == 'docx' || pathinfo($request['location'], PATHINFO_EXTENSION) == 'rtf') {
            if (!file_exists('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.html')) {
                $pathToSave = 'storage/files/' . $id . '/' . $request['location'];
                $dir = 'storage/files/' . $id;
                exec('chmod 755 "' . $pathToSave . '"');
                $comand = 'libreoffice --headless --convert-to html "' . $pathToSave . '" --outdir ' . $dir;
                exec($comand);
                // dd($comand);
            }
            $request['location'] = pathinfo($request['location'], PATHINFO_FILENAME) . '.html';
            rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/works.html');
        }
        //dd($request['iskl']);
        else {
            rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
            exec('pdftohtml storage/files/' . $id . '/work.pdf  -p -i');
            rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);
        }
        //dd($request['iskl']);


        $html = 'works.html';
        $app = Application::findorfail($id);
        if ($request['id_ic'])
            $app->id_insuranceCompany = 3;
        //  include('storage/files/'.$id.'/' . $html);
        $file = file_get_contents('storage/files/' . $id . '/' . $html);
        if (mb_strpos($file, 'zn-'))
            $app->id_insuranceCompany = 1;

        $id_ic = $app->id_insuranceCompany;
        $file = html_entity_decode($file);
        $file = str_replace('<50%', 'менее 50%', $file);

        // dd($file);
        $reset = 0;
        $works = [];
        if (mb_strpos($file, "1\t")) {
            $id_ic = 7;
            $file = str_replace("</td>", '<br/>', $file);
            for ($i = 1; $i < 300; $i++) {
                $file = str_replace($i . "\t", $i . '<br/>', $file);
            }
            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);
            $id_ic = 7;
            $work_start = mb_strpos($file, 'н-час');
            // echo $work_start . 'Раб_Старт<br /><br />';


            $work_end = mb_strpos($file, 'стоимость ремонта');
            // echo $work_end . 'Раб_фин<br /><br />';
            $work = mb_substr($file, $work_start + 30, $work_end - $work_start - 67);
            // $file = mb_substr($file, mb_strpos($file, 'www.autoins.ru') + 14, mb_strlen($file));

            $work_array = explode('<br/>', $work);
            // dd($work_array, $work_end);

            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    // trim($work_array[$i + 1]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма" ||
                    mb_strpos($work_array[$i], "Сумма") ||
                    mb_strpos($work_array[$i], "н-час") ||
                    trim($work_array[$i]) == "ИТОГО:"



                ) {
                } else {

                    $works[$j++] = trim($work_array[$i]);
                }


                // echo $work_array[$i] . '<br />';
            }


            // dd($works, $work_array);
            $works = make_work($works);
            session(['works' => $works]);
        } elseif ((mb_strpos($file, 'по определению стоимости восстановительного ремонта транспортного средства') != 0) && mb_strpos($file, 'Источник') == 0) {
            $work_start = mb_strpos($file, 'н-час') + 14;
            if ($work_start) {
                // echo $work_start . 'Раб_Старт<br /><br />';


                $work_end = mb_strpos($file, 'стоимость ремонта/замены:');

                // echo $work_end . 'Раб_фин<br /><br />';
                $work = mb_substr($file, $work_start, $work_end - $work_start - 24);
                $work_array = explode('<br/>', $work);
                // dd($work_array);
                $j = 0;

                for ($i = 0; $i < count($work_array) - 1; $i++) {
                    if (
                        trim($work_array[$i]) == "Наименование" ||
                        trim($work_array[$i]) == "Норматив," ||
                        trim($work_array[$i]) == "Сумма, руб."
                        ||
                        trim($work_array[$i]) == "Цена ед., руб." ||
                        trim($work_array[$i]) == "н-час"


                    ) {
                    } else


                        $works[$j++] = $work_array[$i];
                    // echo $work_array[$i] . '<br />';
                }
                // dd($works);
                $works = make_work($works);
                session(['works' => $works]);
            }
        } elseif ((mb_strpos($file, 'Фаворит') != 0)) {

            $id_ic = 1;
            $file = str_replace("</td>", '<br/>', $file);
            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);

            $file = str_replace("\u{A0}", ' ', $file);
            $start = 'Стоимость ремонтных воздействий';
            if (!$start)
                $start = 'Работы';
            // $start = mb_strpos($file, $start);
            // dd($start);

            $file = mb_substr($file, mb_strpos($file, $start));
            // dd($file);
            $work_start = mb_strpos($file, 'Сумма');
            // echo $work_start . 'Раб_Старт<br /><br />';


            $work_end = mb_strpos($file, 'Итого');
            // echo $work_end . 'Раб_фин<br /><br />';
            $work = mb_substr($file, $work_start, $work_end - $work_start);
            // $file = mb_substr($file, mb_strpos($file, 'www.autoins.ru') + 14, mb_strlen($file));

            $work_array = explode('<br/>', $work);


            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб." ||
                    trim($work_array[$i]) == "Сумма, Рубли" ||
                    trim($work_array[$i]) == "износом, Рубли" ||
                    trim($work_array[$i]) == "Сумма" ||
                    trim($work_array[$i]) == "1,00"
                    || trim($work_array[$i]) == "1"
                    || trim($work_array[$i]) == "2"
                    || trim($work_array[$i]) == "3"
                    || trim($work_array[$i]) == "4"
                    || trim($work_array[$i]) == "5"
                    || trim($work_array[$i]) == "6"
                    || trim($work_array[$i]) == "7"
                    || trim($work_array[$i]) == "8"
                    || trim($work_array[$i]) == "9"
                    || trim($work_array[$i]) == "10"
                    || trim($work_array[$i]) == "11"
                    || trim($work_array[$i]) == "12"
                    || trim($work_array[$i]) == "13"
                    || trim($work_array[$i]) == "14"
                    || trim($work_array[$i]) == "15"
                    || trim($work_array[$i]) == "16"
                    || trim($work_array[$i]) == "17"
                    || trim($work_array[$i]) == "18"
                    || trim($work_array[$i]) == "19"
                    || trim($work_array[$i]) == "20"
                    || trim($work_array[$i]) == "21"
                    || trim($work_array[$i]) == "22"
                    || trim($work_array[$i]) == "23"
                    || trim($work_array[$i]) == "24"
                    || trim($work_array[$i]) == "25"
                    || trim($work_array[$i]) == "26"
                    || trim($work_array[$i]) == "27"
                    || trim($work_array[$i]) == "28"
                    || trim($work_array[$i]) == "29"
                    || trim($work_array[$i]) == "30"
                    || trim($work_array[$i]) == "31"
                    || trim($work_array[$i]) == "32"
                    || trim($work_array[$i]) == "33"
                    || trim($work_array[$i]) == "34"
                    || trim($work_array[$i]) == "35"
                    || trim($work_array[$i]) == "36"
                    || trim($work_array[$i]) == "37"

                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма" ||
                    trim($work_array[$i]) == "KN" ||
                    trim($work_array[$i]) == "БН" ||
                    trim($work_array[$i]) == "Работы" ||
                    trim($work_array[$i]) == "№ прогр." ||
                    mb_strpos($work_array[$i], "Сумма") ||
                    mb_strpos($work_array[$i], "прогр") ||
                    mb_strpos($work_array[$i], "Норматив") ||
                    mb_strpos($work_array[$i], "траница")
                    || !trim($work_array[$i])
                    ||
                    mb_strpos($work_array[$i], "KN")



                ) {
                } else {
                    // if (($i - 3) % 5 != 0)
                    $works[$j++] = $work_array[$i];
                }


                // echo $work_array[$i] . '<br />';
            }
            $work_array = $works;
            // dd($work_array);
            $works = [];
            $j = 0;
            for ($i = 0; $i < count($work_array); $i++) {

                $works[$j++] = trim($work_array[$i]);
            }

            $works = make_work($works);

            // dd($works, $work_array);
            session(['works' => $works]);

            $file = mb_substr($file, mb_strpos($file, 'Стоимость работ по окраске/контролю'));
            $work_start = mb_strpos($file, 'Сумма');
            // echo $work_start . 'Раб_Старт<br /><br />';


            // echo $work_start . 'Раб_Старт<br /><br />';


            $work = '';
            if ($work_start) {
                $work_end = mb_strpos($file, 'Итого');
                $work = mb_substr($file, $work_start, $work_end - $work_start);
            }


            $work_array = [];
            $works = [];
            $work_array = explode('<br/>', $work);
            // dd($work_array, $work_end);

            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб." ||
                    trim($work_array[$i]) == "Сумма" ||
                    trim($work_array[$i]) == "час" ||
                    trim($work_array[$i]) == "1,00"
                    || trim($work_array[$i]) == "1"
                    || trim($work_array[$i]) == "2"
                    || trim($work_array[$i]) == "3"
                    || trim($work_array[$i]) == "4"
                    || trim($work_array[$i]) == "5"
                    || trim($work_array[$i]) == "6"
                    || trim($work_array[$i]) == "7"
                    || trim($work_array[$i]) == "8"
                    || trim($work_array[$i]) == "9"
                    || trim($work_array[$i]) == "10"
                    || trim($work_array[$i]) == "11"
                    || trim($work_array[$i]) == "12"
                    || trim($work_array[$i]) == "13"
                    || trim($work_array[$i]) == "14"
                    || trim($work_array[$i]) == "15"
                    || trim($work_array[$i]) == "16"
                    || trim($work_array[$i]) == "17"
                    || trim($work_array[$i]) == "18"
                    || trim($work_array[$i]) == "19"
                    || trim($work_array[$i]) == "20"
                    || trim($work_array[$i]) == "21"
                    || trim($work_array[$i]) == "22"
                    || trim($work_array[$i]) == "23"
                    || trim($work_array[$i]) == "24"
                    || trim($work_array[$i]) == "25"
                    || trim($work_array[$i]) == "26"
                    || trim($work_array[$i]) == "27"
                    || trim($work_array[$i]) == "28"
                    || trim($work_array[$i]) == "29"
                    || trim($work_array[$i]) == "30"
                    || trim($work_array[$i]) == "31"
                    || trim($work_array[$i]) == "32"
                    || trim($work_array[$i]) == "33"
                    || trim($work_array[$i]) == "34"
                    || trim($work_array[$i]) == "35"
                    || trim($work_array[$i]) == "36"
                    || trim($work_array[$i]) == "37"

                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма" ||
                    trim($work_array[$i]) == "KN" ||
                    trim($work_array[$i]) == "БН" ||
                    trim($work_array[$i]) == "Работы" ||
                    trim($work_array[$i]) == "№ прогр." ||
                    mb_strpos($work_array[$i], "Сумма") ||
                    mb_strpos($work_array[$i], "прогр") ||
                    mb_strpos($work_array[$i], "Норматив") ||
                    mb_strpos($work_array[$i], "траница")
                    || !trim($work_array[$i])
                    ||
                    mb_strpos($work_array[$i], "KN")




                ) {
                } else {

                    $works[$j++] = $work_array[$i];
                }


                // echo $work_array[$i] . '<br />';
            }
            // dd($works);

            $works = make_work($works);
            session(['works_paint' => $works]);
            // echo $SparePart_array[$i] . '<br />';


            // dd($SparePart_array);
        } elseif ((mb_strpos($file, 'АПЭКС') != 0)) {
            // dd($file);
            $id_ic = 1;
            $file = str_replace("</td>", '<br/>', $file);
            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);

            $file = str_replace("\u{A0}", ' ', $file);
            $file = mb_substr($file, mb_strpos($file, 'Арматурные'));

            $work_start = mb_strpos($file, 'н-час') + 25;
            // echo $work_start . 'Раб_Старт<br /><br />';


            $work_end = mb_strpos($file, 'ИТОГО');
            // echo $work_end . 'Раб_фин<br /><br />';
            $work = mb_substr($file, $work_start, $work_end - $work_start);
            // $file = mb_substr($file, mb_strpos($file, 'www.autoins.ru') + 14, mb_strlen($file));

            $work_array = explode('<br/>', $work);
            // dd($work_array, $work_end);

            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма" ||
                    trim($work_array[$i]) == "KN" ||
                    trim($work_array[$i]) == "БН" ||
                    trim($work_array[$i]) == "Работы" ||
                    trim($work_array[$i]) == "№ прогр." ||
                    mb_strpos($work_array[$i], "Сумма") ||
                    mb_strpos($work_array[$i], "прогр") ||
                    mb_strpos($work_array[$i], "Норматив") ||
                    mb_strpos($work_array[$i], "траница")
                    || !trim($work_array[$i])
                    ||
                    mb_strpos($work_array[$i], "KN")



                ) {
                } else {
                    // if (($i - 3) % 5 != 0)
                    $works[$j++] = $work_array[$i];
                }


                // echo $work_array[$i] . '<br />';
            }
            $work_array = $works;
            // dd($work_array);
            $works = [];
            $j = 0;
            for ($i = 0; $i < count($work_array); $i++) {

                $works[$j++] = trim($work_array[$i]);
            }


            // dd($works, $work_array);
            $works = make_work($works);
            session(['works' => $works]);

            $file = mb_substr($file, mb_strpos($file, 'Малярные'));
            $work_start = mb_strpos($file, 'н-час') + 25;
            // echo $work_start . 'Раб_Старт<br /><br />';


            $work = '';
            if ($work_start) {
                $work_end = mb_strpos($file, 'ИТОГО');
                $work = mb_substr($file, $work_start, $work_end - $work_start);
            }


            $work_array = [];
            $works = [];
            $work_array = explode('<br/>', $work);
            // dd($work_array, $work_end);

            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма" ||
                    trim($work_array[$i]) == "KN" ||
                    trim($work_array[$i]) == "БН" ||
                    trim($work_array[$i]) == "Работы" ||
                    trim($work_array[$i]) == "Окраска" ||
                    trim($work_array[$i]) == "№ прогр." ||
                    trim($work_array[$i]) == "БН" ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "№ прогр." ||
                    mb_strpos($work_array[$i], "Сумма") ||
                    mb_strpos($work_array[$i], "прогр") ||
                    mb_strpos($work_array[$i], "Норматив") ||
                    mb_strpos($work_array[$i], "руб") ||
                    mb_strpos($work_array[$i], "краска") ||
                    mb_strpos($work_array[$i], "KN") ||
                    mb_strpos($work_array[$i], "траница") ||
                    !trim($work_array[$i])



                ) {
                } else {

                    $works[$j++] = $work_array[$i];
                }


                // echo $work_array[$i] . '<br />';
            }
            // dd($works);

            $works = make_work($works);
            session(['works_paint' => $works]);
            // echo $SparePart_array[$i] . '<br />';


            // dd($SparePart_array);
        } elseif ((mb_strpos($file, 'Эксперт: _______________/') != 0)) {
            // dd($file);
            $id_ic = 1;
            $file = str_replace("</td>", '<br/>', $file);
            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);


            $work_start = mb_strpos($file, 'Работы');
            // echo $work_start . 'Раб_Старт<br /><br />';


            $work_end = mb_strpos($file, 'ремонта/замены');
            // echo $work_end . 'Раб_фин<br /><br />';
            $work = mb_substr($file, $work_start, $work_end - $work_start);
            // $file = mb_substr($file, mb_strpos($file, 'www.autoins.ru') + 14, mb_strlen($file));

            $work_array = explode('<br/>', $work);
            // dd($work_array, $work_end);

            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма" ||
                    trim($work_array[$i]) == "KN" ||
                    trim($work_array[$i]) == "БН" ||
                    trim($work_array[$i]) == "Работы" ||
                    trim($work_array[$i]) == "№ прогр." ||
                    mb_strpos($work_array[$i], "Сумма") ||
                    mb_strpos($work_array[$i], "прогр") ||
                    mb_strpos($work_array[$i], "Норматив") ||
                    mb_strpos($work_array[$i], "KN")



                ) {
                } else {
                    // if (($i - 3) % 5 != 0)
                    $works[$j++] = $work_array[$i];
                }


                // echo $work_array[$i] . '<br />';
            }
            $work_array = $works;
            // dd($work_array);
            $works = [];
            $j = 0;
            for ($i = 0; $i < count($work_array); $i++) {

                $works[$j++] = trim($work_array[$i]);
            }


            $works = make_work($works);
            // dd($works, $work_array);
            session(['works' => $works]);

            $work_start = mb_strpos($file, 'Окраска');
            // echo $work_start . 'Раб_Старт<br /><br />';
            $work = '';
            if ($work_start) {
                $work_end = mb_strpos($file, 'стоимость окраски');
                $work = mb_substr($file, $work_start, $work_end - $work_start);
            }


            $work_array = [];
            $works = [];
            $work_array = explode('<br/>', $work);
            // dd($work_array, $work_end);

            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма" ||
                    trim($work_array[$i]) == "KN" ||
                    trim($work_array[$i]) == "БН" ||
                    trim($work_array[$i]) == "Работы" ||
                    trim($work_array[$i]) == "Окраска" ||
                    trim($work_array[$i]) == "№ прогр." ||
                    trim($work_array[$i]) == "БН" ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "№ прогр." ||
                    mb_strpos($work_array[$i], "Сумма") ||
                    mb_strpos($work_array[$i], "прогр") ||
                    mb_strpos($work_array[$i], "Норматив") ||
                    mb_strpos($work_array[$i], "руб") ||
                    mb_strpos($work_array[$i], "краска") ||
                    mb_strpos($work_array[$i], "KN")



                ) {
                } else {

                    $works[$j++] = $work_array[$i];
                }


                // echo $work_array[$i] . '<br />';
            }
            // dd($works);
            $works = make_work($works);
            session(['works_paint' => $works]);
            // echo $SparePart_array[$i] . '<br />';


            // dd($SparePart_array);
        } elseif ((mb_strpos($file, 'Ананьин') != 0) || (mb_strpos($file, 'Мандарин') != 0)) {
            $id_ic = 5;
            $file = str_replace("</td>", '<br/>', $file);
            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);
            // dd($file);

            $type = 2;
            $work_start = mb_strpos($file, 'Ремонтные работы');
            // echo $work_start . 'Раб_Старт<br /><br />';


            $work_end = mb_strpos($file, 'ремонтных');
            // echo $work_end . 'Раб_фин<br /><br />';
            $work = mb_substr($file, $work_start + 115, $work_end - $work_start - 67);
            // $file = mb_substr($file, mb_strpos($file, 'www.autoins.ru') + 14, mb_strlen($file));

            $work_array = explode('<br/>', $work);
            // dd($work_array, $work_end);

            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма" ||
                    mb_strpos($work_array[$i], "Сумма")



                ) {
                } else {
                    // if (($i - 3) % 5 != 0)
                    $works[$j++] = $work_array[$i];
                }


                // echo $work_array[$i] . '<br />';
            }
            $work_array = $works;
            $works = [];
            $j = 0;
            for ($i = 0; $i < count($work_array); $i++) {
                if (($i - 3) % 5 != 0)
                    $works[$j++] = trim($work_array[$i]);
            }

            $works = make_work($works);

            // dd($works, $work_array);
            session(['works' => $works]);
            $work_start = mb_strpos($file, 'Окрасочные');
            // echo $work_start . 'Раб_Старт<br /><br />';
            $work = '';
            if ($work_start) {
                $work_end = mb_strpos($file, 'окрасочных');
                $work = mb_substr($file, $work_start + 101, $work_end - $work_start - 140);
            }


            $work_array = [];
            $works = [];
            $work_array = explode('<br/>', $work);
            // dd($work_array, $work_end);

            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма"



                ) {
                } else {
                    if (($i - 4) % 5 != 0)
                        $works[$j++] = $work_array[$i];
                }


                // echo $work_array[$i] . '<br />';
            }
            // dd($works);
            $works = make_work($works);
            session(['works_paint' => $works]);
            // echo $SparePart_array[$i] . '<br />';


            // dd($SparePart_array);
        } else if ((mb_strpos($file, 'одометр') != 0) && !(mb_strpos($file, 'одометра'))) {
            // $file = str_replace("\u{A0}\u{A0}", '<br/>', $file);
            // $file = str_replace("\u{A0}", ' ', $file);
            $file = str_replace("</td>", '<br/>', $file);

            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);

            $type = 2;
            $work_start = mb_strpos($file, 'Ремонтные работы');
            // echo $work_start . 'Раб_Старт<br /><br />';


            $work_end = mb_strpos($file, 'ремонтных');
            // echo $work_end . 'Раб_фин<br /><br />';
            $work = mb_substr($file, $work_start + 115, $work_end - $work_start - 67);
            $file = mb_substr($file, mb_strpos($file, 'www.autoins.ru') + 14, mb_strlen($file));
            // dd($file);

            $work_array = explode('<br/>', $work);
            // dd($work_array, $work_end);

            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма" ||
                    mb_strpos($work_array[$i], "Сумма")



                ) {
                } else {
                    // if (($i - 3) % 5 != 0)
                    $works[$j++] = $work_array[$i];
                }


                // echo $work_array[$i] . '<br />';
            }

            $work_array = $works;
            $works = [];
            $j = 0;
            for ($i = 0; $i < count($work_array); $i++) {
                if (($i - 3) % 5 != 0)
                    $works[$j++] = trim($work_array[$i]);
            }
            // dd($works, $work_array);

            $works = make_work($works);
            session(['works' => $works]);
            $work_start = mb_strpos($file, 'Окрасочные работы');
            // echo $work_start . 'Раб_Старт<br /><br />';
            $work = '';
            if ($work_start) {
                $work_end = mb_strpos($file, 'окрасочных работ');

                $work = mb_substr($file, $work_start + 101, $work_end - $work_start - 140);
            }

            $work_array = [];
            $works = [];
            $work_array = explode('<br/>', $work);
            // dd($work_array, $work_end);

            $j = 0;
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Цена" ||
                    trim($work_array[$i]) == "Нормо-час" ||
                    trim($work_array[$i]) == "Кол-во" ||
                    trim($work_array[$i]) == "Сумма"



                ) {
                } else {
                    if (($i - 4) % 5 != 0)
                        $works[$j++] = $work_array[$i];
                }


                // echo $work_array[$i] . '<br />';
            }
            // dd($works);
            $works = make_work($works);
            session(['works_paint' => $works]);
            // echo $SparePart_array[$i] . '<br />';


            // dd($SparePart_array);
        } else if (mb_strpos($file, 'до сотен') != 0 && mb_strpos($file, 'Новгород') == 0 && mb_strpos($file, 'АВС-Экспертиза') == 0 && mb_strpos($file, 'АВТОТЕХ') == 0) {
            // $file = str_replace("\u{A0}\u{A0}", '<br/>', $file);
            // $file = str_replace("\u{A0}", ' ', $file);
            $file = str_replace("</td>", '<br/>', $file);

            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);
            // dd($file);
            $work_start = mb_strpos($file, 'Норматив, н-');
            // echo $work_start . 'Раб_Старт<br /><br />';


            $work_end = mb_strpos($file, 'стоимость ремонта/замены:');
            // echo $work_end . 'Раб_фин<br /><br />';
            $work = mb_substr($file, $work_start + 15, $work_end - $work_start - 24);
            $work_array = explode('<br/>', $work);
            // dd($work_array);
            $j = 0;

            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "Наименование" ||
                    mb_strpos("Наименование", $work_array[$i])  ||
                    mb_strpos("наименование", $work_array[$i])  ||
                    mb_strpos("час", $work_array[$i])  ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Коэфф." ||
                    trim($work_array[$i]) == "Сумма," ||
                    trim($work_array[$i]) == "-"
                    ||
                    trim($work_array[$i]) == "руб." ||
                    trim($work_array[$i]) == "час" ||
                    trim($work_array[$i]) == "-час" ||
                    trim($work_array[$i]) == "Коэфф." ||
                    trim($work_array[$i]) == "Сумма, руб." ||
                    trim($work_array[$i]) == ""


                ) {
                } else


                    $works[$j++] = $work_array[$i];
                // echo $work_array[$i] . '<br />';
            }
            $work_start = mb_strpos($file, 'Окраска');
            // echo $work_start . 'Раб_Старт<br /><br />';


            $work_end = mb_strpos($file, 'стоимость окраски/контроля:');
            // echo $work_end . 'Раб_фин<br /><br />';
            $work = mb_substr($file, $work_start + 11, $work_end - $work_start - 24);
            $work_array = explode('<br/>', $work);
            for ($i = 0; $i < count($work_array) - 1; $i++) {
                if (
                    trim($work_array[$i]) == "> Наименование" ||
                    trim($work_array[$i]) == "Наименование" ||
                    trim($work_array[$i]) == "Норматив, н-час" ||
                    trim($work_array[$i]) == "Норматив," ||
                    trim($work_array[$i]) == "Сумма, руб."
                    ||
                    trim($work_array[$i]) == "Цена ед., руб." ||
                    trim($work_array[$i]) == "н-час" ||
                    trim($work_array[$i]) == "Коэфф." ||
                    trim($work_array[$i]) == "Сумма," ||
                    trim($work_array[$i]) == "-"
                    ||
                    trim($work_array[$i]) == "руб." ||
                    trim($work_array[$i]) == "час" ||
                    trim($work_array[$i]) == "Цена ед.," ||
                    trim($work_array[$i]) == "Норматив, н-" ||
                    mb_strpos("Наименование", $work_array[$i])  ||
                    mb_strpos("наименование", $work_array[$i])  ||
                    trim($work_array[$i]) == "Коэфф." ||
                    trim($work_array[$i]) == "Сумма, руб." ||
                    trim($work_array[$i]) == ""



                ) {
                } else


                    $works[$j++] = $work_array[$i];
                // echo $work_array[$i] . '<br />';
            }
            // dd($works);

            $works = make_work($works);
            session(['works' => $works]);
        } else {

            if ($app->id_insuranceCompany == 5 || $app->id_insuranceCompany == 1) {
                $file = html_entity_decode(strip_tags($file, ['br']));
                $file = preg_replace("/\u{A0}/", ' ', $file);
                $file = preg_replace("/\n/", ' ', $file);
                $work_start = mb_strpos($file, 'н-час');
                // echo $work_start . 'Раб_Старт<br /><br />';


                $work_end = mb_strpos($file, 'стоимость ремонта/замены:');
                // echo $work_end . 'Раб_фин<br /><br />';
                $work = mb_substr($file, $work_start + 11, $work_end - $work_start - 24);
                $work_array = explode('<br/>', $work);
                // dd($work_array);
                $j = 0;

                for ($i = 0; $i < count($work_array) - 1; $i++) {
                    if (
                        trim($work_array[$i]) == "Наименование" ||
                        trim($work_array[$i]) == "Норматив," ||
                        trim($work_array[$i]) == "Сумма, руб."
                        ||
                        trim($work_array[$i]) == "Цена ед., руб." ||
                        trim($work_array[$i]) == "н-час"


                    ) {
                    } else


                        $works[$j++] = $work_array[$i];
                    // echo $work_array[$i] . '<br />';
                }
                // dd($works);

                $works = make_work($works);
                session(['works' => $works]);
            } else if (($app->id_insuranceCompany == 3) || ($app->id_insuranceCompany == 7)) {

                $file = html_entity_decode(strip_tags($file, ['br']));
                $file = preg_replace("/\u{A0}/", ' ', $file);
                $file = preg_replace("/\n/", ' ', $file);
                $work_start = mb_strpos($file, 'н-час');
                if ($work_start) {
                    // echo $work_start . 'Раб_Старт<br /><br />';


                    $work_end = mb_strpos($file, 'стоимость ремонта/замены:');

                    // echo $work_end . 'Раб_фин<br /><br />';
                    $work = mb_substr($file, $work_start + 11, $work_end - $work_start - 24);
                    $work_array = explode('<br/>', $work);
                    // dd($work_array);
                    $j = 0;

                    for ($i = 0; $i < count($work_array) - 1; $i++) {
                        if (
                            trim($work_array[$i]) == "Наименование" ||
                            trim($work_array[$i]) == "Норматив," ||
                            trim($work_array[$i]) == "Сумма, руб."
                            ||
                            trim($work_array[$i]) == "Цена ед., руб." ||
                            trim($work_array[$i]) == "н-час"


                        ) {
                        } else


                            $works[$j++] = $work_array[$i];
                        // echo $work_array[$i] . '<br />';
                    }
                    // dd($works);
                    $works = make_work($works);
                    session(['works' => $works]);
                } else if (mb_strpos($file, 'www.autoins.ru')) {
                    $file = mb_substr($file, mb_strpos($file, 'Ремонтные работы, рубли'), mb_strlen($file));
                    // dd($file);
                    $work_start = mb_strpos($file, 'Ремонтные работы, рубли');
                    // echo $work_start . 'Раб_Старт<br /><br />';


                    $work_end = mb_strpos($file, 'www.autoins.ru');
                    // echo $work_end . 'Раб_фин<br /><br />';
                    $work = mb_substr($file, $work_start + 100, $work_end - $work_start - 157);
                    $file = mb_substr($file, mb_strpos($file, 'www.autoins.ru') + 14, mb_strlen($file));
                    // dd($file);

                    $work_array = explode('<br/>', $work);
                    // dd($work_array, $work_end);

                    $j = 0;
                    for ($i = 0; $i < count($work_array) - 1; $i++) {
                        if (
                            trim($work_array[$i]) == "Наименование" ||
                            trim($work_array[$i]) == "Норматив," ||
                            trim($work_array[$i]) == "Сумма, руб."
                            ||
                            trim($work_array[$i]) == "Цена ед., руб." ||
                            trim($work_array[$i]) == "н-час" ||
                            trim($work_array[$i]) == "Цена" ||
                            trim($work_array[$i]) == "Нормо-час" ||
                            trim($work_array[$i]) == "Кол-во" ||
                            trim($work_array[$i]) == "Сумма" ||
                            trim($work_array[$i]) == ""



                        ) {
                        } else {
                            // if (($i - 3) % 5 != 0)
                            $works[$j++] = trim($work_array[$i]);
                        }


                        // echo $work_array[$i] . '<br />';
                    }
                    $work_array = $works;
                    $works = [];
                    $j = 0;
                    for ($i = 0; $i < count($work_array); $i++) {
                        if (($i - 3) % 5 != 0)
                            $works[$j++] = trim($work_array[$i]);
                    }



                    $works = make_work($works);
                    session(['works' => $works]);
                    $work_start = mb_strpos($file, 'Окрасочные работы, рубли');
                    // echo $work_start . 'Раб_Старт<br /><br />';
                    $work = '';
                    if ($work_start) {
                        $work_end = mb_strpos($file, 'окрасочных');

                        $work = mb_substr($file, $work_start + 101, $work_end - $work_start - 137);
                    }

                    $work_array = [];
                    $works = [];
                    $work_array = explode('<br/>', $work);
                    // dd($work_array, $work_end);

                    $j = 0;
                    for ($i = 0; $i < count($work_array) - 1; $i++) {
                        if (
                            trim($work_array[$i]) == "Наименование" ||
                            trim($work_array[$i]) == "Норматив," ||
                            trim($work_array[$i]) == "Сумма, руб."
                            ||
                            trim($work_array[$i]) == "Цена ед., руб." ||
                            trim($work_array[$i]) == "н-час" ||
                            trim($work_array[$i]) == "Цена" ||
                            trim($work_array[$i]) == "Нормо-час" ||
                            trim($work_array[$i]) == "Кол-во" ||
                            trim($work_array[$i]) == "Сумма" ||
                            trim($work_array[$i]) == ""



                        ) {
                        } else {
                            // if (($i - 3) % 5 != 0)
                            $works[$j++] = trim($work_array[$i]);
                        }


                        // echo $work_array[$i] . '<br />';
                    }
                    $work_array = $works;
                    $works = [];
                    $j = 0;
                    for ($i = 0; $i < count($work_array); $i++) {
                        if (($i - 3) % 5 != 0)
                            $works[$j++] = trim($work_array[$i]);
                    }

                    // dd($works, $work_array);
                    $works = make_work($works);
                    session(['works_paint' => $works]);
                } else {
                    if (!file_exists('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.txt')) {
                    }
                    $file = file_get_contents('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.txt');
                    $file = json_decode($file, JSON_UNESCAPED_UNICODE);
                    session(['works' => $file]);
                    $works = $file;

                    return view('Work.data_select_ocr', ['works' => $works, 'id' => $id, 'location' => $request['location'], 'reset' => $reset]);
                    // $work_start = mb_strpos($file, 'н-час');
                    // // echo $work_start . 'Раб_Старт<br /><br />';


                    // $work_end = mb_strpos($file, 'стоимость ремонта/замены:');
                    // // echo $work_end . 'Раб_фин<br /><br />';
                    // $work = mb_substr($file, $work_start + 6, $work_end - $work_start - 2);
                    // $work_array = explode("\n", $work);
                    // // dd($work_array);
                    // $j = 0;

                    // for ($i = 0; $i < count($work_array) - 1; $i++) {
                    //     if (
                    //         trim($work_array[$i]) == "Наименование" ||
                    //         trim($work_array[$i + 1]) == "Наименование" ||
                    //         trim($work_array[$i]) == "Норматив," ||
                    //         trim($work_array[$i]) == "Сумма, руб."
                    //         ||
                    //         trim($work_array[$i]) == "Цена ед., руб." ||
                    //         trim($work_array[$i]) == "н-час"


                    //     ) {
                    //     } else


                    //         $works[$j++] = $work_array[$i];
                    //     // echo $work_array[$i] . '<br />';
                    // }
                    // // dd($works);

                }
            }
            if ($app->id_insuranceCompany == 4) {
                $file = str_replace("</td>", '<br/>', $file);

                $file = html_entity_decode(strip_tags($file, ['br']));
                $file = str_replace("\t", '', $file);
                $file = str_replace("\n", ' ', $file);
                // dd($file);
                $work_start = mb_strpos($file, 'Норматив, н-');
                // echo $work_start . 'Раб_Старт<br /><br />';


                $work_end = mb_strpos($file, 'стоимость ремонта/замены:');
                // echo $work_end . 'Раб_фин<br /><br />';
                $work = mb_substr($file, $work_start + 11, $work_end - $work_start - 24);
                $work_array = explode('<br/>', $work);

                $j = 0;

                for ($i = 0; $i < count($work_array) - 1; $i++) {
                    if (
                        trim($work_array[$i]) == "> Наименование" ||
                        trim($work_array[$i]) == "Наименование" ||
                        trim($work_array[$i]) == "Норматив, н-час" ||
                        trim($work_array[$i]) == "Норматив," ||
                        trim($work_array[$i]) == "Сумма, руб."
                        ||
                        trim($work_array[$i]) == "Цена ед., руб." ||
                        trim($work_array[$i]) == "н-час" ||
                        trim($work_array[$i]) == "Коэфф." ||
                        trim($work_array[$i]) == "Сумма," ||
                        trim($work_array[$i]) == "-"
                        ||
                        trim($work_array[$i]) == "руб." ||
                        trim($work_array[$i]) == "час" ||
                        trim($work_array[$i]) == "Цена ед.," ||
                        trim($work_array[$i]) == "Норматив, н-" ||
                        mb_strpos("Наименование", $work_array[$i])  ||
                        mb_strpos("наименование", $work_array[$i])  ||
                        trim($work_array[$i]) == "Коэфф." ||
                        trim($work_array[$i]) == "Сумма, руб." ||
                        trim($work_array[$i]) == ""


                    ) {
                    } else


                        $works[$j++] = $work_array[$i];
                    // echo $work_array[$i] . '<br />';
                }

                // $works = make_work($works);
                session(['works' => $works]);

                $work_start = mb_strpos($file, 'Окраска');
                // echo $work_start . 'Раб_Старт<br /><br />';


                $work_end = mb_strpos($file, 'стоимость окраски/контроля:');
                // echo $work_end . 'Раб_фин<br /><br />';
                $work = mb_substr($file, $work_start + 11, $work_end - $work_start - 24);
                $work_array = [];
                $works = [];
                $work_array = explode('<br/>', $work);
                // dd($work_array, $work_end);

                $j = 0;
                for ($i = 0; $i < count($work_array) - 1; $i++) {
                    if (
                        trim($work_array[$i]) == "> Наименование" ||
                        trim($work_array[$i]) == "Наименование" ||
                        trim($work_array[$i]) == "Норматив, н-час" ||
                        trim($work_array[$i]) == "Норматив," ||
                        trim($work_array[$i]) == "Сумма, руб."
                        ||
                        trim($work_array[$i]) == "Цена ед., руб." ||
                        trim($work_array[$i]) == "н-час" ||
                        trim($work_array[$i]) == "Коэфф." ||
                        trim($work_array[$i]) == "Сумма," ||
                        trim($work_array[$i]) == "-"
                        ||
                        trim($work_array[$i]) == "руб." ||
                        trim($work_array[$i]) == "час" ||
                        trim($work_array[$i]) == "Цена ед.," ||
                        trim($work_array[$i]) == "Норматив, н-" ||
                        mb_strpos("Наименование", $work_array[$i])  ||
                        mb_strpos("наименование", $work_array[$i])  ||
                        trim($work_array[$i]) == "Коэфф." ||
                        trim($work_array[$i]) == "Сумма, руб." ||
                        trim($work_array[$i]) == ""


                    ) {
                    } else


                        $works[$j++] = $work_array[$i];
                    // echo $work_array[$i] . '<br />';
                }
                // dd($works);
                // $works = make_work($works);
                session(['works_paint' => $works]);
            }
        }


        // dd($work_array);
        return view('Work.data_select', ['Works' => $works, 'id' => $id, 'location' => $request['location'], 'reset' => $reset, 'id_ic' => $id_ic, 't' => $type]);
    }
    public function work_edit($id, $id_work, $count)
    {
        if ($count == 0) {

            $works = session('works');
            for ($i = $id_work; $i < count($works) - 1; $i++) {
                $works[$i] = $works[$i + 1];
            }
            unset($works[count($works) - 1]);

            // dd($works);
            $works = make_work($works);
            session(['works' => $works]);
        } else {

            $works = session('works');
            $works[$id_work - $count] = $works[$id_work - $count] . ' ' . $works[$id_work];
            for ($i = $id_work; $i < count($works) - 1; $i++) {
                $works[$i] = $works[$i + 1];
            }

            unset($works[count($works) - 1]);
            // dd($works);
            // unset(session('works')[count($works) - 1]);

            $works = make_work($works);
            session(['works' => $works]);
            // dd(session('works'));
        }

        return view('Work.data_select', ['id' => $id]);
    }
    public function work_edit_paint($id, $id_work, $count)
    {
        if ($count == 0) {

            $works = session('works_paint');
            for ($i = $id_work; $i < count($works) - 1; $i++) {
                $works[$i] = $works[$i + 1];
            }
            unset($works[count($works) - 1]);

            // dd($works);
            $works = make_work($works);
            session(['works' => $works]);
        } else {

            $works = session('works_paint');
            $works[$id_work - $count] = $works[$id_work - $count] . ' ' . $works[$id_work];
            for ($i = $id_work; $i < count($works) - 1; $i++) {
                $works[$i] = $works[$i + 1];
            }

            unset($works[count($works) - 1]);
            // dd($works);
            // unset(session('works')[count($works) - 1]);
            $works = make_work($works);
            session(['works_paint' => $works]);
            // dd(session('works'));
        }

        return view('Work.data_select', ['id' => $id]);
    }
    public function data_save(Request $request, $id)
    {

        $name = $request['name'];
        $summ = $request['summ'];
        $price = $request['price'];
        $count_hours = $request['count_hours'];
        $name_paint = $request['name_paint'];
        $summ_paint = $request['summ_paint'];
        $price_paint = $request['price_paint'];
        $count_hours_paint = $request['count_hours_paint'];
        $id_application = $request['id_application'];
        $works = Work::where('id_application', '=', $id_application)
            ->where('summ', '<>', 0)
            ->get();

        foreach ($works as $work) {
            $work->delete();
        }
        // dd($works);

        //dd($request->name);
        for ($i = 1; $i <= count($name); $i++) {
            Work::updateOrCreate(
                [
                    'id_application' => $id_application,
                    'name' => clearHTML($name[$i]),
                ],
                [
                    'price' => str2number($price[$i]),
                    'count_hours' => str2number($count_hours[$i]),
                    'summ' => str2number($summ[$i]),
                    'npp' => $i,
                    'paint' => 0
                ]
            );
        }
        if ($name_paint)
            for ($i = 1; $i <= count($name_paint); $i++) {
                Work::updateOrCreate(
                    [
                        'id_application' => $id_application,
                        'name' => clearHTML($name_paint[$i]),
                    ],
                    [
                        'price' => str2number($price_paint[$i]),
                        'count_hours' => str2number($count_hours_paint[$i]),
                        'summ' => str2number($summ_paint[$i]),
                        'npp' => $i,
                        'paint' => '1'
                    ]
                );
            }

        return redirect()->route('SparePart', $id);
    }
    public function destroy($id)
    {
        $Work = Work::findOrFail($id);
        $Work->delete();
        return redirect()
            ->route('SparePart', $Work->id_application);
    }
}
