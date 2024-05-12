<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;

use App\Models\user;
use App\Models\usergroup;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class PersonalController extends Controller
{
    public function index()
    {

        session(['rout' => 'personal']);
        $user = User::findOrFail(Auth::user()->id);
        return view('personal.index', ['user' => $user]);
    }
    public function role()
    {
        $user = User::findOrFail(Auth::user()->id);
        $usergroups = new usergroup();
        return view('personal.role', ['user' => $user, 'usergroups' => $usergroups->all()]);
    }
    public function role_update(Request $request)
    {
        $data = $this->validate($request, [
            'role' => 'required'
        ]);
        session(['GroupId' => (int)$data['role']]);
        // dd(session('GroupId'));
        return redirect()->back();
    }
    public function create()
    {
        return view('personal.create');
    }
    public function store(Request $request)
    {
    }
    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);
        $data = $request->validate([
            'name' => ["required", "string"],
            'phone' => ["required", "string"],
            'theme' => ["nullable", "string"],
        ]);

        // dd($data);
        $user->name = $data["name"];
        $user->phone = $data["phone"];
        $user->theme = $data["theme"];
        $user->save();
        return redirect()->back();
    }
    public function telegram()
    {
        $user = User::findOrFail(Auth::user()->id);
        return view('personal.telegram', ['user' => $user]);
    }
    public function callback(Request $request)
    {
        $telegramUser = Socialite::driver('telegram')->user();
        Auth::user()->telegram_id = $telegramUser->id;
        Auth::user()->avatar = $telegramUser->avatar;
        Auth::user()->nickname = $telegramUser->nickname;
        Auth::user()->save();
        // dd($telegramUser);

        return view('personal.telegram');
    }
    public function telegram_update(Request $request)
    {
        Auth::user()->telegram_notify = $request->telegram_notify;
        Auth::user()->save();
        return redirect()->back();
    }
    public function telegram_delete(Request $request)
    {
        Auth::user()->telegram_id = null;
        Auth::user()->telegram_notify = null;
        Auth::user()->avatar = null;
        Auth::user()->nickname = null;
        Auth::user()->save();
        return redirect()->back();
    }
}
