<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use App\Models\User;
use App\Models\chat_rec;
use App\Models\chat;
use App\Events\MessageSent;
use Illuminate\Support\Facades\DB;
use App\Models\status_chat;
use App\Models\notify;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Auth;


class ChatController extends Controller
{
    public function chat_send(Request $request)
    {
        // dd($request);
        // dd($request);
        $chat = new chat;
        $data = $this->validate($request, [
            'id_application' => 'required',
            'from' => 'required',
            'to' => 'array|nullable',
            'message' => 'required',
        ]);
        $author = User::findOrFail($data['from']);

        $chat->id_application = $data['id_application'];
        $chat->from = $data['from'];
        $chat->to = 0;
        $chat->message = $data['message'];
        $chat->save();

        foreach ($data['to'] as $key => $to) {
            // dd($to);
            if ($to != 0) {
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
                broadcast(new MessageSent($notification));
                if ($recipient->telegram_notify) {
                    $message = strip_tags($data['message'], '<b></b><br>');
                    $message = str_replace('&nbsp;', ' ', $message);
                    $new_chat = TelegraphChat::where('chat_id', '=', $recipient->telegram_id)->first();
                    $ch = $new_chat->html("В чате заявки {$data['id_application']} новое сообщение \n<b>{$author->name}</b>\n\n <i>{$message}</i>")
                        ->send();
                    try {
                        $file = "storage/test.txt";
                        $notify->id_message = json_decode($ch)->result->message_id;
                        file_put_contents($file, json_decode($ch));
                    } catch (\Exception $e) {
                        // dd($e);
                    }
                }

                $notify->save();
            }
        }

        $chats = chat::where('chats.id', '=', $chat->id)
            ->leftjoin('users as userfrom', 'chats.from', '=', 'userfrom.id')
            ->leftjoin('users as userto', 'chats.to', '=', 'userto.id')
            ->select('chats.*', 'userfrom.name as from_name', 'userto.name as to_name')
            ->first();

        $chats->user_to = chat_rec::where('id_chat', '=', $chat->id)
            ->join('users as userto', 'chat_recs.id_to', '=', 'userto.id')
            ->select('userto.name as to_name')->get();
        $chats->time = Date::parse($chat->created_at)->format('j F Yг. H:i');

        return $chats;
    }

    public function readed(Request $request)
    {
        $data = $this->validate($request, [
            'id_application' => 'required',
            'user_id' => 'required'
        ]);
        $status_chat = status_chat::where('id_user', '=', $data['user_id'])->where('id_application', '=', $data['id_application'])
            ->where('is_readed', '=', '0')->get();
        foreach ($status_chat as $status) {
            $status->is_readed = 1;
            $status->save();
        }
        $notify = notify::where('id_application', '=', $data['id_application'])
            ->where('id_user', '=', $data['user_id'])
            ->where('id_text', '=', '2')->get()->all();
        foreach ($notify as $notif) {
            if ($notif->id_message) {
                $user = User::where('id', '=', $data['user_id'])->first();
                $new_chat = TelegraphChat::where('chat_id', '=', $user->telegram_id)->first();
                $ch = $new_chat->deleteMessage($notif->id_message)->send();
            }
            $notif->delete();
        }
        return response()->json(['message' => 'All readed'], 200);
    }
    public function unreaded(Request $request)
    {
        $data = $this->validate($request, [
            'id_application' => 'required',
            'user_id' => 'required'
        ]);


        $status_chat = new status_chat;
        $status_chat->id_user = $data['user_id'];
        $status_chat->id_application = $data['id_application'];
        $status_chat->is_readed = false;
        $status_chat->save();

        $notify = new notify;
        $notify->id_user = $data['user_id'];
        $notify->id_application = $data['id_application'];
        $notify->id_text = 2;
        $notify->link = "/chat/";
        $notify->save();
        $notification = DB::table('notifies')->where('notifies.id', '=', $notify->id)->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
            ->select('notifies.*', 'text_notifies.text')->first();
        broadcast(new MessageSent($notification));
        $chats = DB::table('chats')
            ->where('id_application', '=', $data['id_application'])
            ->leftjoin('users as userfrom', 'chats.from', '=', 'userfrom.id')
            ->leftjoin('users as userto', 'chats.to', '=', 'userto.id')
            ->select('chats.*', 'userfrom.name as from_name', 'userto.name as to_name')
            ->get()->sortDesc();
        foreach ($chats as $chat) {
            $chat->user_to = chat_rec::where('id_chat', '=', $chat->id)
                ->join('users as userto', 'chat_recs.id_to', '=', 'userto.id')
                ->select('userto.name as to_name')->get();
            $chat->time = Date::parse($chat->created_at)->format('j F Yг. H:i');
        }
        return $chats;
    }

    public function destroy($id)
    {
        chat::destroy($id);
        chat_rec::where('id_chat', '=', $id)->delete();
        return response()->json(['message' => 'Chat deleted successfully'], 200);
    }
}
