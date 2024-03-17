<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\work;

class WorkController extends Controller
{
    public function Work($id)
    {
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
        foreach ($Works_ic as $work) {
            $summ_ic = $summ_ic + (int)str_replace(' ', '', $work->summ);
        }
        foreach ($Works_serv as $work) {
            $summ_service = $summ_service + (int)str_replace(' ', '', $work->price_service);
        }
        $delta = $summ_service - $summ_ic;
        return view('Work.index', [
            'Works' => $Works_ic->all(), "Works_serv" => $Works_serv->all(),
            'id' => $id, 'delta' => $delta,
            'summ_ic' => $summ_ic, 'summ_service' => $summ_service
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


        ]);

        foreach ($data as $key => $value) {
            $Work->$key = $value;
        }
        $Work->save();
        return redirect()
            ->route('Work', $Work->id_application);
    }

    public function edit($id)
    {
        $Work = Work::findOrFail($id);

        return view('Work.edit', ['Work' => $Work]);
    }
    public function update(Request $request, $id)
    {
        $Work = Work::findOrFail($id);

        $data = $this->validate($request, [
            'id_application' => 'required',
            'name' => 'required',
            'summ' => 'nullable',
            'price_service' => 'nullable'

        ]);
        // dd($data);
        foreach ($data as $key => $val) {
            $Work->$key = $val;
        }
        $Work->save();
        return redirect()
            ->to(route('Work', $Work->id_application) . '#' . $request->id);
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

        //dd($request['iskl']);
        rename('storage/files/' . $id . '/' . $request['location'], 'storage/files/' . $id . '/work.pdf');
        exec('pdftohtml storage/files/' . $id . '/work.pdf  -p -i');
        rename('storage/files/' . $id . '/work.pdf', 'storage/files/' . $id . '/' . $request['location']);

        $html = 'works.html';
        //  include('storage/files/'.$id.'/' . $html);
        $file = file_get_contents('storage/files/' . $id . '/' . $html);
        $file = html_entity_decode(strip_tags($file, ['br']));
        $work_start = mb_strpos($file, 'н-час');
        // echo $work_start . 'Раб_Старт<br /><br />';
        $iskl = $request['iskl'];
        if ($request['reset']) {
            $iskl = [];
        }

        $work_end = mb_strpos($file, 'стоимость ремонта/замены:');
        // echo $work_end . 'Раб_фин<br /><br />';
        $work = mb_substr($file, $work_start + 5, $work_end - $work_start - 18);
        $work_array = explode('<br/>', $work);
        $j = 0;
        $reset = 0;
        $works = [];
        for ($i = 0; $i < count($work_array); $i++) {
            if ($request['iskl']) {
                foreach ($iskl as $key => $value) {
                    if ($i == $value) {
                        $i++;
                        $reset++;
                    }
                }
            }
            $works[$j++] = $work_array[$i];
            // echo $work_array[$i] . '<br />';
        }

        // dd($work_array);
        return view('Work.data_select', ['Works' => $works, 'id' => $id, 'location' => $request['location'], 'reset' => $reset]);
    }
    public function data_save(Request $request, $id)
    {

        $name = $request['name'];
        $summ = $request['summ'];
        $id_application = $request['id_application'];
        //dd($request->name);
        for ($i = 1; $i <= count($name); $i++) {
            $Work = new Work;
            $Work->name = $name[$i];
            $Work->summ = $summ[$i];
            $Work->id_application = $id_application;
            $Work->save();
        }

        return redirect()->route('Work', $id);
    }
    public function destroy($id)
    {
        $Work = Work::findOrFail($id);
        $Work->delete();
        return redirect()
            ->route('Work', $Work->id_application);
    }
}
