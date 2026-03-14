<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function getAi(Request $request)
    {
        $data = $request->all();
        $file = "storage/files/test1.txt";
        file_put_contents($file, json_encode($data));
        return $data;
    }
    public function sendAi()
    {
        $response = Http::withHeaders([
            'X-Api-Key' => '164350b2f1b353f206d0b90c23a8a5fd248d1b0ec4013639f1aa655b1d0ac0fe'

        ])
            ->asForm()
            ->post('http://localhost:8000/assess', [
                'archive_url' => 'https://mandarin-portal.ru/storage/files/8003/foto_13-03-26.zip',
                'app_id' => '8003',
                'accident_date' => '15.01.2025',
                'callback_url' => 'https://mandarin-portal.ru/api/ai/get_data'
            ]);
        dd($response->json());

        // $data = $this->validate($request, [
        //     'id_application' => 'required',
        //     'location' => 'required'
        // ]);

        // $ch = curl_init('http://localhost:8000/assess' );
        // curl_setopt(
        //     $ch,
        //     CURLOPT_RETURNTRANSFER,
        //     true
        // );
        // curl_setopt(
        //     $ch,
        //     CURLOPT_HTTPHEADER,
        //     array(
        //         'X-Api-Key: 164350b2f1b353f206d0b90c23a8a5fd248d1b0ec4013639f1aa655b1d0ac0fe',
        //         'x-folder-id: b1gcnqkpepfcp6fulsah',
        //         'x-data-logging-enabled: true'
        //     )
        // );

        // $html = curl_exec($ch);
        // curl -X POST http://localhost:8000/assess   -H "X-Api-Key: 164350b2f1b353f206d0b90c23a8a5fd248d1b0ec4013639f1aa655b1d0ac0fe"   -F "app_id=CASE-2025-001234"   -F "archive_url=https://mandarin-portal.ru/storage/files/7889/foto_06-03-26_100303.zip"   -F "accident_date=15.01.2025"   -F "callback_url=https://your-crm.com/api/damage-result"
        // return $data;
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
