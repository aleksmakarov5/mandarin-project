<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\SparePart;
use App\Models\application;
use App\Models\sparepart_status;
use App\Models\estimate;
use Illuminate\Support\Facades\Gate;
use App\Models\troubleshooting;


class SparePartController extends Controller
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
            $summ_ic_full = $summ_ic_full +  str_replace(' ', '', $sparepart->price_ic_full);
            $summ_ic_half = $summ_ic_half +  str_replace(' ', '', $sparepart->price_ic_half);
            switch ($sparepart->selected_price) {
                case 1:
                    $summ_order = $summ_order +  str_replace(' ', '', $sparepart->price_original);
                    break;
                case 2:
                    $summ_order = $summ_order +  str_replace(' ', '', $sparepart->price_analogue);
                    break;
                case 3:
                    $summ_order = $summ_order +  str_replace(' ', '', $sparepart->price_used);
                    break;
                case 4:
                    $summ_order = $summ_order + str2number($sparepart->price_service);
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
    public function SparePart($id)
    {
        $eco = '';

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
        $app = application::where('id', '=', $id)->first();
        $estimates = DB::table('estimates')->where('id_application', '=', $id)->first();
        // dd($estimates);

        $Works_serv = DB::table('works')
            ->where('id_application', '=', $id)
            ->get();
        $summ_service = 0;
        foreach ($Works_serv as $work) {
            $summ_service = $summ_service + (int)str_replace(' ', '', $work->price_service);
        }

        // dd($rewiew->all()) ;
        $summ_ic_full = 0;
        $summ_sto = 0;
        $summ_ic_half = 0;
        $summ_order = 0;
        $delta_full = [];
        $delta_half = [];
        foreach ($spareparts as $i => $sparepart) {
            // $sparepart->price_ic_full = $app->ub_type ? $sparepart->price_ic_half : $sparepart->price_ic_full;
            $summ_ic_full = $summ_ic_full + str2number($sparepart->price_ic_full);
            if (str2number($sparepart->price_sto) == 0)
                $summ_sto = $summ_sto +  str2number($sparepart->price_ic_full);
            else
                $summ_sto = $summ_sto + str2number($sparepart->price_sto);

            $summ_ic_half = $summ_ic_half +  str2number($sparepart->price_ic_half);
            switch ($sparepart->selected_price) {
                case 1:
                    $summ_order = $summ_order + str2number($sparepart->price_original);
                    $delta_full[$i] = str2number($sparepart->price_original) - str2number($sparepart->price_ic_full);
                    $delta_half[$i] = str2number($sparepart->price_original) - str2number($sparepart->price_ic_half);
                    break;
                case 2:
                    $summ_order = $summ_order + str2number($sparepart->price_analogue);
                    $delta_full[$i] = str2number($sparepart->price_analogue) - str2number($sparepart->price_ic_full);
                    $delta_half[$i] = str2number($sparepart->price_analogue) - str2number($sparepart->price_ic_half);
                    break;
                case 3:
                    $summ_order = $summ_order + str2number($sparepart->price_used);
                    $delta_full[$i] = str2number($sparepart->price_used) -  str2number($sparepart->price_ic_full);
                    $delta_half[$i] = str2number($sparepart->price_used) - str2number($sparepart->price_ic_half);
                    break;
                case 4:
                    $summ_order = $summ_order + str2number($sparepart->price_service);
                    $delta_full[$i] = str2number($sparepart->price_service) -  str2number($sparepart->price_ic_full);
                    $delta_half[$i] = str2number($sparepart->price_service) - str2number($sparepart->price_ic_half);
                    break;
                default:
                    $delta_full[$i] = 0 - str2number($sparepart->price_ic_full);
                    $delta_half[$i] = 0 -  str2number($sparepart->price_ic_half);
                    break;
            }
            $sparepart->delta = $delta_full[$i];
        }
        $delta_res_full = $summ_order - $summ_ic_full;
        $delta_res_half = $summ_order - $summ_ic_half;
        $sertificat = application::findOrFail($id)->sertificat;
        // dd($sertificat)

        $eco = '';

        $Works_ic = DB::table('works')
            ->where('id_application', '=', $id)
            ->where('summ', '<>', 0)
            ->where('paint', '=', '0')
            ->get();
        $Works_paint = DB::table('works')
            ->where('id_application', '=', $id)
            ->where('summ', '<>', 0)
            ->where('paint', '=', '1')
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
            $summ_pzn += str2number($work->count_hours_pzn) != 0 ? str2number($work->count_hours_pzn) * str2number($work->price) : str2number($work->summ);
        }
        $summ_paint = 0;
        $summ_pzn_paint = 0;
        foreach ($Works_paint as $work) {
            $summ_paint = $summ_paint + str2number($work->summ);
            $summ_pzn_paint += str2number($work->count_hours_pzn) != 0 ? str2number($work->count_hours_pzn) * str2number($work->price) : str2number($work->summ);
        }
        foreach ($Works_serv as $work) {
            $summ_service = $summ_service + str2number($work->price_service);
        }
        $delta = $summ_service - $summ_ic;
        // $estimates->fact = $summ_service + $summ_order;
        $statuses = sparepart_status::all();
        return view('SparePart.index', [
            'spareparts' => $spareparts,
            'delta_res_full' => $delta_res_full,
            'delta_res_half' => $delta_res_half,
            'delta_full' => $delta_full,
            'delta_half' => $delta_half,
            'id' => $id,
            'summ_ic_full' => $summ_ic_full,
            'summ_sto' => $summ_sto,
            'summ_ic_half' => $summ_ic_half,
            'summ_order' => $summ_order,
            'estimate' => $estimates,
            'summ_service' => $summ_service,
            'eco' => $eco,
            'statuses' => $statuses,
            'sertificat' => $sertificat,
            'Works' => $Works_ic->all(),
            "Works_serv" => $Works_serv->all(),
            'delta' => $delta,
            'summ_ic' => $summ_ic,
            'summ_pzn' => $summ_pzn,
            'Works_paint' => $Works_paint->all(),
            'summ_paint' => $summ_paint,
            'summ_pzn_paint' => $summ_pzn_paint,
        ]);
    }

    public function create($id)
    {
        $sparepart = new SparePart;

        return view('SparePart.create', ['sparepart' => $sparepart->all()], ['id' => $id]);
    }

    public function store(Request $request)
    {
        $sparepart = new SparePart;
        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required',
            'part_number' => 'nullable',
            'order_number' => 'nullable',
            'price_ic_full' => 'nullable',
            'price_sto' => 'nullable',
            'price_analogue' => 'nullable',
            'price_original' => 'nullable',
            'price_ic_half' => 'nullable',
            'price_used' => 'nullable',
            'status' => 'nullable',
            'comment' => 'nullable',
            'date_price' => 'nullable',
            'author' => 'integer',
            'date_wait' => 'nullable',
            'status_id' => 'nullable',
        ]);
        $request->flash();


        foreach ($data as $key => $value) {
            $sparepart->$key = $value;
        }

        $sparepart->save();
        if ($data['author'] == 5) {
            $troubleshooting = new troubleshooting;
            $troubleshooting->PartName = $data['name'];
            $troubleshooting->id_application = $data['id_application'];
            $troubleshooting->save();
        }
        return redirect()
            ->route('SparePart', $sparepart->id_application);
    }

    public function sertificat(Request $request, $id)
    {
        $app = application::findOrFail($id);
        $app->sertificat = $request->sertificat;
        $app->save();
        dd($app);
        return redirect()->back();
    }

    public function emr10(request $request, $id)
    {

        $estimates = estimate::updateorcreate(
            ['id_application' => $id],
            [
                'emr10' => $request->emr10,
            ]
        );
        return redirect()->back();
    }

    public function estimate(request $request, $id)
    {
        try {
            $emr10 = estimate::where('id_application', '=', $id)->first()->emr10;
        } catch (\Exception $e) {
            $emr10 = 0;
        }

        if ($emr10)
            $estimates = estimate::updateorcreate(
                ['id_application' => $id],
                [
                    'price_sp_full' => round(str2number($request->price_sp_full) / 1.1, 2),
                    'price_sp_half' => round(str2number($request->price_sp_half) / 1.1, 2),
                    'price_repair' => round(str2number($request->price_repair) / 1.1, 2),
                    'price_paint' => round(str2number($request->price_paint) / 1.1, 2),
                    'price_zip' => round(str2number($request->price_zip) / 1.1, 2),
                    'price_work' => round(str2number($request->price_work) / 1.1, 2),
                    'price_repair_full' =>  round(str2number($request->price_repair_full) / 1.1, 2),
                    'price_repair_half' => round(str2number($request->price_repair_half) / 1.1, 2)

                ]
            );
        else
            $estimates = estimate::updateorcreate(
                ['id_application' => $id],
                [
                    'price_sp_full' => str2number($request->price_sp_full),
                    'price_sp_half' => str2number($request->price_sp_half),
                    'price_repair' => str2number($request->price_repair),
                    'price_paint' => str2number($request->price_paint),
                    'price_zip' => str2number($request->price_zip),
                    'price_work' => str2number($request->price_work),
                    'price_repair_full' =>  str2number($request->price_repair_full),
                    'price_repair_half' => str2number($request->price_repair_half)
                ]
            );
        return redirect()->back();
    }

    public function edit($id)
    {
        $spareparts = DB::table('spare_parts')->where('id_application', '=', $id)->get();

        return view('SparePart.edit', [
            'spareparts' => $spareparts->all(),
            'id' => $id
        ]);
    }
    public function update(Request $request, $id)
    {

        // dd($request);

        $sparepart = sparepart::findOrFail($request->id);
        try {
            $emr10 = estimate::where('id_application', '=', $id)->first()->emr10;
        } catch (\Exception $e) {
            $emr10 = 0;
        }

        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required',
            'part_number' => 'nullable',
            'order_number' => 'nullable',
            'price_original' => 'nullable',
            'price_analogue' => 'nullable',
            'price_used' => 'nullable',
            'price_ic_full' => 'nullable',
            'price_sto' => 'nullable',
            'price_ic_half' => 'nullable',
            'status' => 'nullable',
            'comment' => 'nullable',
            'date_price' => 'nullable',
            'date_wait' => 'nullable',
            'selected_price' => 'nullable',
            'selected_mrk' => 'nullable',
            'selected_service' => 'nullable',
            'price_service' => 'nullable',
            'approved' => 'nullable',
            'status_id' => 'nullable'
        ]);
        if ($emr10)
            $data['price_ic_full'] = $data['price_ic_full']  / 1.1;
        $data['price_sto'] = str2number($data['price_sto']);
        $data['price_analogue'] = str2number($data['price_analogue']);
        $data['price_used'] = str2number($data['price_used']);
        $data['price_original'] = str2number($data['price_original']);
        $data['price_ic_full'] = str2number($data['price_ic_full']);


        //  dd($data);
        foreach ($data as $key => $value) {
            $sparepart->$key = $value;
        }
        $sparepart->save();


        return redirect()
            ->to(route('SparePart', $request->id_application) . '#' . $request->id);
    }
    public function doc_load($id)
    {
        $documents = DB::table('attachments')
            ->where('id_application', '=', $id)
            ->get();
        return view('SparePart.doc_load', ['id' => $id], ['documents' => $documents]);
    }

    public function data_load(Request $request, $id)
    {
        $SP = sparepart::where('id_application', '=', $id)->get()->all();
        // dd($SP, $id);
        $type = 0;
        $SpareParts = [];
        $reset = 0;
        // dd($request);
        if (pathinfo($request['location'], PATHINFO_EXTENSION) != 'html') {
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
        }
        $html = 'works.html';
        //  include('storage/files/'.$id.'/' . $html);
        $file = file_get_contents('storage/files/' . $id . '/' . $html);
        // dd($file);
        // $DOM = new \DOMDocument();
        // $table_start = strpos($file, '<table');
        // $table_end = strpos($file, '</table>');
        // $table=
        // libxml_use_internal_errors(true);
        // $DOM->loadHTML($file);
        // libxml_use_internal_errors(false);
        // $items = $DOM->getElementsByTagName('tr');


        // dd($items);

        // $file = html_entity_decode($file);

        // $file = preg_replace('/\xc2\xa0/', '<br/>', $file);

        // $file = preg_replace("/&#160/", '<br/>', $file);

        // $file = preg_replace("/\u{A0}/", '<br/>', $file);
        // dd($file);

        // $file = str_replace('&#160', '</br>', $file);

        // $file = str_replace("<br/><br/>", '<br/>', $file);
        $file = str_replace("износом,<br/>Рубли", 'износом, Рубли', $file);

        $file = html_entity_decode($file);


        dd($file);
        $app = Application::findorfail($id);
        if ($request['id_ic'])
            $app->id_insuranceCompany = 3;
        $id_ic = $app->id_insuranceCompany;
        if (mb_strpos($file, 'zn-'))
            $app->id_insuranceCompany = 1;

        if (mb_strpos($file, "1\t")) {
            $id_ic = 7;
            $file = str_replace("</td>", '<br/>', $file);
            for ($i = 1; $i < 300; $i++) {
                $file = str_replace($i . "\t", $i . '<br/>', $file);
            }
            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);
            $SparePart_start = mb_strpos($file, 'износом, Рубли');
            if (!$SparePart_start)
                $SparePart_start = mb_strpos($file, 'ПО КАТАЛОГУ');
            // echo $SparePart_start . 'Раб_Старт<br /><br />';
            $iskl = $request['iskl'];
            if ($request['reset']) {
                $iskl = [];
            }

            $SparePart_end = mb_strpos($file, 'ИТОГО');
            // echo $SparePart_end . 'Раб_фин<br /><br />';
            $SparePart = mb_substr($file, $SparePart_start + 15, $SparePart_end - $SparePart_start - 15);
            $SparePart_array = explode('<br/>', $SparePart);
            $j = 0;
            $reset = 0;
            $SpareParts = [];
            for ($i = 0; $i < count($SparePart_array); $i++) {
                $SparePart_array[$i] = trim($SparePart_array[$i]);
                if ($request['iskl']) {
                    foreach ($iskl as $key => $value) {
                        if ($i == $value) {
                            $i++;
                            $reset++;
                        }
                    }
                }
                $SpareParts[$j++] = $SparePart_array[$i];
            }
            // dd($SpareParts);
        } else if ((mb_strpos($file, 'Эксперт: _______________/') != 0)) {
            // dd($file);
            $file = str_replace("</td>", '<br/>', $file);
            $id_ic = 100;
            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);
            // dd($file);
            $SparePart_start = mb_strpos($file, 'Сумма, руб.');
            if (!$SparePart_start)
                $SparePart_start = mb_strpos($file, 'ПО КАТАЛОГУ');
            // echo $SparePart_start . 'Раб_Старт<br /><br />';
            $iskl = $request['iskl'];
            if ($request['reset']) {
                $iskl = [];
            }

            $SparePart_end = mb_strpos($file, 'стоимость узлов и деталей');
            // echo $SparePart_end . 'Раб_фин<br /><br />';
            $SparePart = mb_substr($file, $SparePart_start + 17, $SparePart_end - $SparePart_start - 30);
            $SparePart_array = explode('<br/>', $SparePart);
            // dd($SparePart_array);
            $j = 0;
            $reset = 0;
            $SpareParts = [];
            for ($i = 0; $i < count($SparePart_array); $i++) {
                $SparePart_array[$i] = trim($SparePart_array[$i]);
                if ($request['iskl']) {
                    foreach ($iskl as $key => $value) {
                        if ($i == $value) {
                            $i++;
                            $reset++;
                        }
                    }
                }
                $SpareParts[$j++] = $SparePart_array[$i];
                // echo $SparePart_array[$i] . '<br />';
                // dd($SpareParts);
                // dd($SparePart_array);
            }
        } else if ((mb_strpos($file, 'Ананьин') != 0) || (mb_strpos($file, 'Мандарин') != 0)) {
            $file = str_replace("</td>", '<br/>', $file);

            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);
            // dd($file);
            $type = 2;
            $SparePart_start = mb_strpos($file, 'по каталогу');
            if (!$SparePart_start)
                $SparePart_start = mb_strpos($file, 'ПО КАТАЛОГУ');
            // echo $SparePart_start . 'Раб_Старт<br /><br />';
            $iskl = $request['iskl'];
            if ($request['reset']) {
                $iskl = [];
            }

            $SparePart_end = mb_strpos($file, 'Стоимость запчастей');
            // echo $SparePart_end . 'Раб_фин<br /><br />';
            $SparePart = mb_substr($file, $SparePart_start + 18, $SparePart_end - $SparePart_start - 18);
            $SparePart_array = explode('<br/>', $SparePart);
            $j = 0;
            $reset = 0;
            $SpareParts = [];
            for ($i = 0; $i < count($SparePart_array); $i++) {
                $SparePart_array[$i] = trim($SparePart_array[$i]);
                if ($request['iskl']) {
                    foreach ($iskl as $key => $value) {
                        if ($i == $value) {
                            $i++;
                            $reset++;
                        }
                    }
                }
                $SpareParts[$j++] = $SparePart_array[$i];
                // echo $SparePart_array[$i] . '<br />';
                // dd($SpareParts);
                // dd($SparePart_array);
            }
        } else if ((mb_strpos($file, 'одометр') != 0) && !(mb_strpos($file, 'одометра'))) {
            // $file = str_replace("\u{A0}\u{A0}", '<br/>', $file);
            // $file = str_replace("\u{A0}", ' ', $file);

            $file = str_replace("</td>", '<br/>', $file);
            // $file = str_replace("\u{A0}\u{A0}", '<br/>', $file);


            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);
            // dd($file);
            $type = 2;
            $SparePart_start = mb_strpos($file, 'каталога');

            // echo $SparePart_start . 'Раб_Старт<br /><br />';
            $iskl = $request['iskl'];
            if ($request['reset']) {
                $iskl = [];
            }

            $SparePart_end = mb_strpos($file, 'Итого');
            // echo $SparePart_end . 'Раб_фин<br /><br />';
            $SparePart = mb_substr($file, $SparePart_start + 18, $SparePart_end - $SparePart_start - 25);
            $SparePart_array = explode('<br/>', $SparePart);
            $j = 0;
            $reset = 0;
            $SpareParts = [];

            for ($i = 0; $i < count($SparePart_array); $i++) {
                $SparePart_array[$i] = trim($SparePart_array[$i]);
                if ($request['iskl']) {
                    foreach ($iskl as $key => $value) {
                        if ($i == $value) {
                            $i++;
                            $reset++;
                        }
                    }
                }
                // if (
                //     mb_strpos($SparePart_array[$i], '%')
                //     || mb_strpos($SparePart_array[$i], 'скидки')
                //     || $SparePart_array[$i] == ""
                // ) {
                // } else
                $SpareParts[$j++] = $SparePart_array[$i];
                // echo $SparePart_array[$i] . '<br />';

            }
            // dd($SpareParts);
        } else if (mb_strpos($file, 'до сотен') != 0 && mb_strpos($file, 'Новгород') == 0) {
            // $file = str_replace("\u{A0}\u{A0}", '<br/>', $file);
            // $file = str_replace("\u{A0}", ' ', $file);

            $file = str_replace("</td>", '<br/>', $file);
            // dd($file);
            $file = html_entity_decode(strip_tags($file, ['br']));
            $file = str_replace("\t", '', $file);
            $file = str_replace("\n", ' ', $file);


            $SparePart_start = mb_strpos($file, 'износом, руб');

            // echo $SparePart_start . 'Раб_Старт<br /><br />';
            $iskl = $request['iskl'];
            if ($request['reset']) {
                $iskl = [];
            }

            $SparePart_end = mb_strpos($file, 'ИТОГО');
            // echo $SparePart_end . 'Раб_фин<br /><br />';
            $SparePart = mb_substr($file, $SparePart_start + 19, $SparePart_end - $SparePart_start - 19);
            $SparePart_array = explode('<br/>', $SparePart);
            $j = 0;
            $reset = 0;
            $SpareParts = [];
            for ($i = 0; $i < count($SparePart_array); $i++) {
                if ($request['iskl']) {
                    foreach ($iskl as $key => $value) {
                        if ($i == $value) {
                            $i++;
                            $reset++;
                        }
                    }
                }
                $SpareParts[$j++] = $SparePart_array[$i];
            }
            // dd($SparePart_array);
        } else {
            // dd($file);
            // $file = str_replace("\u{A0}", '<br/>', $file);

            $file = html_entity_decode(strip_tags($file, ['br']));
            if ($app->id_insuranceCompany == 1 || $app->id_insuranceCompany == 5) {
                // dd($file);
                $file = str_replace("\u{A0}", '<br/>', $file);
                $SparePart_start = mb_strpos($file, 'износом, Рубли');

                // echo $SparePart_start . 'Раб_Старт<br /><br />';
                $iskl = $request['iskl'];
                if ($request['reset']) {
                    $iskl = [];
                }

                $SparePart_end = mb_strpos($file, 'ИТОГО');
                // echo $SparePart_end . 'Раб_фин<br /><br />';
                $SparePart = mb_substr($file, $SparePart_start + 14, $SparePart_end - $SparePart_start + 14);
                $SparePart_array = explode('<br/>', $SparePart);
                $j = 0;
                $reset = 0;
                $SpareParts = [];
                for ($i = 0; $i < count($SparePart_array); $i++) {
                    if ($request['iskl']) {
                        foreach ($iskl as $key => $value) {
                            if ($i == $value) {
                                $i++;
                                $reset++;
                            }
                        }
                    }
                    $SpareParts[$j++] = $SparePart_array[$i];
                    // echo $SparePart_array[$i] . '<br />';
                }
                // dd($SparePart_array);
                $id_ic = 1;
            } else if (($app->id_insuranceCompany == 3)) { {

                    $SparePart_start = mb_strpos($file, 'износом, Рубли');

                    // dd($SparePart_start);
                    if ($SparePart_start) {
                        $file = str_replace("\u{A0}", '<br/>', $file);
                        $SparePart_start = mb_strpos($file, 'износом, Рубли');
                        // dd($file);
                        // echo $SparePart_start . 'Раб_Старт<br /><br />';
                        $iskl = $request['iskl'];
                        if ($request['reset']) {
                            $iskl = [];
                        }

                        $SparePart_end = mb_strpos($file, 'ИТОГО');
                        // echo $SparePart_end . 'Раб_фин<br /><br />';
                        $SparePart = mb_substr($file, $SparePart_start + 14, $SparePart_end - $SparePart_start + 14);
                        // dd($SparePart);
                        $SparePart_array = explode('<br/>', $SparePart);
                        $j = 0;
                        $reset = 0;
                        $SpareParts = [];
                        for ($i = 0; $i < count($SparePart_array); $i++) {
                            if ($request['iskl']) {
                                foreach ($iskl as $key => $value) {
                                    if ($i == $value) {
                                        $i++;
                                        $reset++;
                                    }
                                }
                            }
                            $SpareParts[$j++] = $SparePart_array[$i];
                            // echo $SparePart_array[$i] . '<br />';
                        }
                        // dd($SpareParts);
                        $id_ic = 1;
                    } else {
                        $SparePart_start = mb_strpos($file, 'каталогу');
                        if ($SparePart_start) {
                            $file = str_replace("\u{A0}", ' ', $file);
                            // dd($SparePart_start);
                            // echo $SparePart_start . 'Раб_Старт<br /><br />';
                            $iskl = $request['iskl'];
                            if ($request['reset']) {
                                $iskl = [];
                            }

                            $SparePart_end = mb_strpos($file, 'www.autoins.ru');
                            // echo $SparePart_end . 'Раб_фин<br /><br />';
                            $SparePart = mb_substr($file, $SparePart_start + 29, $SparePart_end - $SparePart_start - 20);
                            // dd($SparePart);
                            $SparePart_array = explode('<br/>', $SparePart);
                            $j = 0;
                            $reset = 0;
                            $SpareParts = [];
                            for ($i = 0; $i < count($SparePart_array); $i++) {

                                if ($request['iskl']) {
                                    foreach ($iskl as $key => $value) {
                                        if ($i == $value) {
                                            $i++;
                                            $reset++;
                                        }
                                    }
                                }
                                if (trim($SparePart_array[$i]) != "")
                                    $SpareParts[$j++] = $SparePart_array[$i];
                                // echo $SparePart_array[$i] . '<br />';
                            }

                            // dd($SpareParts);
                        } else {

                            $SparePart_start = mb_strpos($file, 'износом');
                            if ($SparePart_start) {
                                $file = str_replace("\u{A0}", '<br/>', $file);
                                $SparePart_start = mb_strpos($file, 'износом');
                                // dd($SparePart_start);
                                // echo $SparePart_start . 'Раб_Старт<br /><br />';
                                $iskl = $request['iskl'];
                                if ($request['reset']) {
                                    $iskl = [];
                                }

                                $SparePart_end = mb_strpos($file, 'www.autoins.ru');
                                // echo $SparePart_end . 'Раб_фин<br /><br />';
                                $SparePart = mb_substr($file, $SparePart_start + 13, $SparePart_end - $SparePart_start - 20);
                                $SparePart_array = explode('<br/>', $SparePart);
                                $j = 0;
                                $reset = 0;
                                $SpareParts = [];
                                for ($i = 0; $i < count($SparePart_array); $i++) {

                                    if ($request['iskl']) {
                                        foreach ($iskl as $key => $value) {
                                            if ($i == $value) {
                                                $i++;
                                                $reset++;
                                            }
                                        }
                                    }
                                    if (trim($SparePart_array[$i]) != "")
                                        $SpareParts[$j++] = $SparePart_array[$i];
                                    // echo $SparePart_array[$i] . '<br />';
                                }

                                // dd($SpareParts);
                            } else {
                                if (!file_exists('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.txt')) {
                                    // rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
                                    // exec('base64 -i storage/files/' . $id . '/work.pdf > storage/files/' . $id . '/output.txt');
                                    // rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);
                                    // $output = file_get_contents('storage/files/' . $id . '/output.txt');
                                    // $data = array(
                                    //     "mimeType" => "application/pdf",
                                    //     "languageCodes" => ["*"],
                                    //     "model" => "table",
                                    //     "content" => $output
                                    // );


                                    // $ch = curl_init('https://ocr.api.cloud.yandex.net/ocr/v1/recognizeTextAsync');
                                    // curl_setopt(
                                    //     $ch,
                                    //     CURLOPT_HTTPHEADER,
                                    //     array(
                                    //         'Content-Type:application/json',
                                    //         'Authorization: Api-Key AQVNwD4aqhqRFt7PCZNt1WEmpma_qrsqyLCuxtm2',
                                    //         'x-folder-id: b1gcnqkpepfcp6fulsah',
                                    //         'x-data-logging-enabled: true'
                                    //     )
                                    // );
                                    // curl_setopt(
                                    //     $ch,
                                    //     CURLOPT_POST,
                                    //     1
                                    // );
                                    // curl_setopt(
                                    //     $ch,
                                    //     CURLOPT_POSTFIELDS,
                                    //     json_encode($data, JSON_UNESCAPED_UNICODE)
                                    // );
                                    // curl_setopt(
                                    //     $ch,
                                    //     CURLOPT_RETURNTRANSFER,
                                    //     true
                                    // );
                                    // curl_setopt(
                                    //     $ch,
                                    //     CURLOPT_SSL_VERIFYPEER,
                                    //     false
                                    // );
                                    // curl_setopt(
                                    //     $ch,
                                    //     CURLOPT_HEADER,
                                    //     false
                                    // );
                                    // $res = curl_exec($ch);
                                    // curl_close($ch);

                                    // $res = json_decode($res, JSON_UNESCAPED_UNICODE);

                                    // // echo $res['id'];

                                    // $ch = curl_init('https://ocr.api.cloud.yandex.net/ocr/v1/getRecognition?operationId=' . $res['id']);
                                    // curl_setopt(
                                    //     $ch,
                                    //     CURLOPT_RETURNTRANSFER,
                                    //     true
                                    // );
                                    // curl_setopt(
                                    //     $ch,
                                    //     CURLOPT_HTTPHEADER,
                                    //     array(
                                    //         'Content-Type:application/json',
                                    //         'Authorization: Api-Key AQVNwD4aqhqRFt7PCZNt1WEmpma_qrsqyLCuxtm2',
                                    //         'x-folder-id: b1gcnqkpepfcp6fulsah',
                                    //         'x-data-logging-enabled: true'
                                    //     )
                                    // );
                                    // curl_setopt(
                                    //     $ch,
                                    //     CURLOPT_SSL_VERIFYPEER,
                                    //     false
                                    // );
                                    // curl_setopt(
                                    //     $ch,
                                    //     CURLOPT_HEADER,
                                    //     false
                                    // );
                                    // $html = curl_exec($ch);
                                    // $html = json_decode($html, JSON_UNESCAPED_UNICODE);
                                    // if (isset($html['error']['httpCode']))
                                    //     while (isset($html['error'])) {
                                    //         // echo ($html['error']['httpCode'] . '<br>');
                                    //         $html = curl_exec($ch);
                                    //         $html = json_decode($html, JSON_UNESCAPED_UNICODE);
                                    //     }
                                    // $html = curl_exec($ch);
                                    // // dd($html);
                                    // $html = explode("}}", $html);
                                    // curl_close($ch);
                                    // $file = '';
                                    // foreach ($html as $result) {
                                    //     $result .= '}}';
                                    //     $result = json_decode($result, JSON_UNESCAPED_UNICODE);

                                    //     if (isset($result['result']['textAnnotation']['tables'])) {
                                    //         foreach ($result['result']['textAnnotation']['tables'] as $table) {
                                    //             foreach ($table['cells'] as $row) {
                                    //                 $text = $row['text'];
                                    //                 $text = preg_replace('/\xc2\xa0/', ' ', $text);

                                    //                 $text = preg_replace(
                                    //                     "/&#160/",
                                    //                     ' ',
                                    //                     $text
                                    //                 );

                                    //                 $text = preg_replace("/\u{A0}/", ' ', $text);

                                    //                 $text = str_replace(
                                    //                     '&#160',
                                    //                     ' ',
                                    //                     $text
                                    //                 );
                                    //                 $text = str_replace("\u{A0}", ' ', $text);

                                    //                 $file .= $text . "<br/>";
                                    //             }
                                    //         }
                                    //     }
                                    // }
                                    // file_put_contents('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.txt', $file);
                                }
                                $file = file_get_contents('storage/files/' . $id . '/' . pathinfo($request['location'], PATHINFO_FILENAME) . '.txt');
                                // dd($file);
                                $SparePart_start = mb_strpos($file, 'износом, Рубли');
                                // echo $SparePart_start . 'Раб_Старт<br /><br />';
                                $iskl = $request['iskl'];
                                if ($request['reset']) {
                                    $iskl = [];
                                }

                                $SparePart_end = mb_strpos($file, 'ИТОГ');
                                // echo $SparePart_end . 'Раб_фин<br /><br />';
                                $SparePart = mb_substr($file, $SparePart_start + 24, $SparePart_end - $SparePart_start - 34);
                                $SparePart_array = explode("<br/>", $SparePart);
                                $j = 0;
                                $reset = 0;
                                $SpareParts = [];
                                for ($i = 0; $i < count($SparePart_array); $i++) {
                                    if ($request['iskl']) {
                                        foreach ($iskl as $key => $value) {
                                            if ($i == $value) {
                                                $i++;
                                                $reset++;
                                            }
                                        }
                                    }
                                    $SpareParts[$j++] = $SparePart_array[$i];
                                    // echo $SparePart_array[$i] . '<br />';
                                }
                            }
                        }
                    }
                }
            } else if (($app->id_insuranceCompany == 7)) { {
                    // dd($file);
                    $SparePart_start = mb_strpos($file, 'износом, Рубли');
                    if ($SparePart_start) {
                        // echo $SparePart_start . 'Раб_Старт<br /><br />';
                        $iskl = $request['iskl'];
                        if ($request['reset']) {
                            $iskl = [];
                        }

                        $SparePart_end = mb_strpos($file, 'ИТОГО');
                        // echo $SparePart_end . 'Раб_фин<br /><br />';
                        $SparePart = mb_substr($file, $SparePart_start + 14, $SparePart_end - $SparePart_start + 14);
                        $SparePart_array = explode('<br/>', $SparePart);
                        $j = 0;
                        $reset = 0;
                        $SpareParts = [];
                        for ($i = 0; $i < count($SparePart_array); $i++) {
                            if ($request['iskl']) {
                                foreach ($iskl as $key => $value) {
                                    if ($i == $value) {
                                        $i++;
                                        $reset++;
                                    }
                                }
                            }
                            $SpareParts[$j++] = $SparePart_array[$i];
                            // echo $SparePart_array[$i] . '<br />';
                        }
                        $id_ic = $app->id_insuranceCompany;
                    }
                    // dd($SpareParts);
                }
            } else if ($app->id_insuranceCompany == 4) {
                // dd($file);
                $SparePart_start = mb_strpos($file, 'износом, руб');

                // echo $SparePart_start . 'Раб_Старт<br /><br />';
                $iskl = $request['iskl'];
                if ($request['reset']) {
                    $iskl = [];
                }

                $SparePart_end = mb_strpos($file, 'ИТОГО');
                // echo $SparePart_end . 'Раб_фин<br /><br />';
                $SparePart = mb_substr($file, $SparePart_start + 18, $SparePart_end - $SparePart_start - 18);
                $SparePart_array = explode('<br/>', $SparePart);
                $j = 0;
                $reset = 0;
                $SpareParts = [];
                for ($i = 0; $i < count($SparePart_array); $i++) {
                    if ($request['iskl']) {
                        foreach ($iskl as $key => $value) {
                            if ($i == $value) {
                                $i++;
                                $reset++;
                            }
                        }
                    }
                    $SpareParts[$j++] = $SparePart_array[$i];
                    // echo $SparePart_array[$i] . '<br />';
                }
                // dd($SpareParts);
            }
        }





        // dd($id_ic);
        return view('SparePart.data_select', ['SpareParts' => $SpareParts, 'id' => $id, 'location' => $request['location'], 'reset' => $reset, 'id_ic' => $id_ic, 't' => $type, 'SP' => $SP]);
    }
    public function data_save(Request $request, $id)
    {

        $name = $request['name'];
        $price_ic_full = $request['price_ic_full'];
        $part_number = $request['part_number'];
        $price_ic_half = $request['price_ic_half'];
        $id_application = $request['id_application'];
        $price = $request['price'];
        $count = $request['count'];
        $iznos = $request['iznos'];
        //dd($request->name);
        for ($i = 1; $i <= count($name); $i++) {
            SparePart::updateOrCreate(
                [
                    'id_application' => $id_application,

                    'part_number' => $part_number[$i]
                ],
                [
                    'name' => $name[$i],
                    'price' => str2number($price[$i]),
                    'price_ic_full' => str2number($price_ic_full[$i]),
                    'price_ic_half' => str2number($price_ic_half[$i]),
                    'count' =>  str2number($count[$i]),
                    'iznos' => str2number($iznos[$i]),
                    'npp' => $i,
                    'author' => 0
                ]

            );
            // $SparePart = new SparePart;
            // $SparePart->name = $name[$i];
            // $SparePart->price_ic_full = str_replace(' ', '', $price_ic_full[$i]);
            // $SparePart->part_number = str_replace(' ', '', $part_number[$i]);
            // $SparePart->price_ic_half = str_replace(' ', '', $price_ic_half[$i]);
            // $SparePart->price = str_replace(' ', '', $price[$i]);
            // $SparePart->count = str_replace(' ', '', $count[$i]);
            // $SparePart->iznos = str_replace(' ', '', $iznos[$i]);
            // $SparePart->id_application = str_replace(' ', '', $id_application);
            // $SparePart->save();
        }

        return redirect()->route('SparePart', $id);
    }
    public function destroy($id)
    {
        $SparePart = SparePart::findOrFail($id);
        $SparePart->delete();
        return redirect()
            ->route('SparePart', $SparePart->id_application);
    }

    public function price_select($id)
    {
        $SpareParts = DB::table('spare_parts')->where('id_application', '=', $id)->get();
        return view('SparePart.price_select', ['spareparts' => $SpareParts->all(), 'id' => $id]);
    }

    public function update_price(Request $request, $id)
    {

        // dd($request);



        $data = $this->validate($request, [
            'id_application' => 'required',
            'id' => 'array|required',
            'selected_price' => 'array|nullable',
            'selected_mrk' => 'array|nullable',
        ]);
        //  dd($data);
        $id = $request->id;
        $selected_price = $request->selected_price;
        $selected_mrk = $request->selected_mrk;
        foreach ($id as $key => $val) {
            $sparepart = SparePart::findOrFail($val);
            if (isset($selected_price[$key]))
                $sparepart->selected_price = $selected_price[$key];
            if (isset($selected_mrk[$key]))
                $sparepart->selected_mrk = $selected_mrk[$key];
            $sparepart->save();
        }


        return redirect()
            ->route('SparePart', $request->id_application);
    }
}
