<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\application;
use App\Models\attachment;

class AiController extends Controller
{
    public function getAi(Request $request)
    {
        $data = $request->all();
        $file = "storage/files/" . $data['app_id'] . "/AIresult.txt";
        $text = '';

        // foreach ($data as $key => $value) {
        //     $text .= $key . ': ' . $value . "\n";
        // }

        // file_put_contents($file, json_encode($data));
        $text .= $data['app_id'] . "\n\n\nПовреждения\n\n\n";
        $i = 1;
        foreach ($data['result']['damages'] as $damage) {
            // $text .= $damage['name'] . ': ' . $damage['catalog_id'] . "\n";
            $text .= $i++ . ": " . $damage['name'] . ': ' . $damage['catalog_id'] . "\n\n";
        }
        $text .= "\n\nЗаключение\n\n\n" . $data['result']['summary'] . "\n\n\n";
        $text .= json_encode($data);



        file_put_contents($file, $text);
        $attach = new attachment();
        $attach->message_id = 0;
        $attach->id_application = $data['app_id'];
        $attach->location = 'AIresult.txt';
        $attach->mime_type = 'txt';
        $attach->file_size = filesize($file);
        $attach->file_name = 'AIresult.txt';
        $attach->save();
        return $data;
    }
    public function sendAi(Request $request)
    {
        $data = $this->validate($request, [
            'app_id' => 'required',
            'location' => 'required'
        ]);
        $app = application::find($data['app_id']);
        if (!$app->vin)
            return 202;
        if (!$app->DTPdate)
            return 203;

        $response = Http::withHeaders([
            'X-Api-Key' => '164350b2f1b353f206d0b90c23a8a5fd248d1b0ec4013639f1aa655b1d0ac0fe'

        ])
            ->asForm()
            ->post('http://localhost:8000/assess', [
                'archive_url' => 'https://mandarin-portal.ru/storage/files/' . $app->id . '/' . $data['location'],
                'app_id' => $app->id,
                'accident_date' => $app->DTPdate,
                'vin' => $app->vin,
                'callback_url' => 'https://mandarin-portal.ru/api/ai/get_data'
            ]);
        if ($response->json()['status'] == 'accepted')
            return 200;
        else
            return 201;
    }
    public function getData($id)
    {
        $response = Http::withHeaders([
            'X-Api-Key' => '164350b2f1b353f206d0b90c23a8a5fd248d1b0ec4013639f1aa655b1d0ac0fe'

        ])

            ->get('http://localhost:8000//result/' . $id);
        dd($response->json());
    }
}
