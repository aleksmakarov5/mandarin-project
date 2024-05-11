<?php

namespace App\Http\Controllers;

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
}
