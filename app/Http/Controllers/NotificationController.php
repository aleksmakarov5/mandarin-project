<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\notify;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index($id)
    {
        $notifications = DB::table('notifies')
            ->where('notifies.id_user', '=', $id)
            ->join('text_notifies', 'text_notifies.id', '=', 'notifies.id_text')
            ->select('notifies.*', 'text_notifies.text')
            ->get();

        return  $notifications;
    }
    public function create()
    {
        return view('notifications.create');
    }
    public function show($id)
    {
        return view('notifications.show');
    }
    public function edit($id)
    {
        return view('notifications.edit');
    }
    public function delete($id)
    {
        $notify = notify::where('id', '=', $id)->first();
        $id = $notify->id_user;
        $notify->delete();
        $res = $this->index($id);
        return $res;
    }
}