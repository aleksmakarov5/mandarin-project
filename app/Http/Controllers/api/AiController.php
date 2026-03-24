<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\application;
use App\Models\attachment;
use App\Models\User;
use App\Models\chat;
use App\Models\chat_rec;
use Illuminate\Support\Facades\DB;
use App\Models\status_chat;
use App\Models\notify;
use Jenssegers\Date\Date;
use App\Models\chat_attachment;
use App\Models\work_group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\user\newmessage;


use App\Events\MessageSent;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;


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
        $text .= $data['app_id'];
        // file_put_contents($file, json_encode($data));
        if (isset($data['result']['car_info']['brand']))
            $text .= "\n\n\nМарка: " . $data['result']['car_info']['brand'];
        if (isset($data['result']['car_info']['model']))
            $text .= " " . $data['result']['car_info']['model'];
        if (isset($data['result']['car_info']['vin']))
            $text .= "\n\nВИН: " . $data['result']['car_info']['vin'];
        if (isset($data['result']['car_info']['year']))
            $text .= "\n\nГод выпуска: " . $data['result']['car_info']['year'];
        $text .=   "\n\n\nПовреждения\n\n\n";
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
        $attach->sto_view = 1;
        $attach->save();
        $user = User::find(2);
        $to = $data['user_id'];
        $id = $data['app_id'];
        $chat = new chat;
        $chat->id_application = $id;
        $chat->from = 2;
        $chat->to = 0;
        $chat->message = 'Получены данные анализа ИИ';
        $chat->save();
        $recipient = User::findOrFail($to);
        $chat_recs = new chat_rec;
        $chat_recs->id_chat = $chat->id;
        $chat_recs->id_to = $to;
        $chat_recs->save();
        $notify = new notify;
        $notify->id_user = $to;
        $notify->id_application = $data['id_application'];
        $notify->id_text = 2;
        $notify->link = "/chat/";

        $status_chat = new status_chat;
        $status_chat->id_user = $to;
        $status_chat->id_application = $data['id_application'];
        $status_chat->is_readed = false;
        $status_chat->save();
        $notification = DB::table('notifies')->where('notifies.id', '=', $notify->id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
            ->select('notifies.*', 'text_notifies.text')->first();
        // broadcast(new MessageSent($notification));
        // if ($recipient->telegram_notify) {
        //     $message = strip_tags($data['message'], '<b></b><br>');
        //     $message = str_replace('&nbsp;', ' ', $message);
        //     $new_chat = TelegraphChat::where('chat_id', '=', $recipient->telegram_id)->first();
        //     $ch = $new_chat->html("В чате заявки {$data['id_application']} новое сообщение \n<b>{$author->name}</b>\n\n <i>{$message}</i>")
        //         ->send();
        //     try {
        //         $file = "storage/test.txt";
        //         $notify->id_message = json_decode($ch)->result->message_id;
        //         file_put_contents($file, json_decode($ch));
        //     } catch (\Exception $e) {
        //         // dd($e);
        //     }
        // }
        if ($recipient->email_notify) {
            $message = 'Получены данные анализа ИИ';
            Mail::to($recipient->email)->queue(new newmessage($data['id_application'], 'Системное оповещение', $message));
        }


        $notify->save();
        return $data;
    }
    public function sendAi(Request $request)
    {
        $data = $this->validate($request, [
            'app_id' => 'required',
            'location' => 'required',
            'user_id' => 'required'
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
                'callback_url' => 'https://mandarin-portal.ru/api/ai/get_data',
                'user_id' => $data['user_id']
            ]);
        if ($response->json())
            return $response->json();
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
