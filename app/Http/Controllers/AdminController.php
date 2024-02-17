<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;
use App\Models\usergroup;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
    public function users_index()
    {
        $users = new user();
        return view('admin.users.index', ['users' => $users->all()]);
    }
    public function statuses_index()
    {
        return view('admin.statuses.index');
    }
    public function inscompanies_index()
    {
        return view('admin.inscompanies.index');
    }
    public function destroy_user($id)
    {
        $user = user::findOrFail($id);
        $user->delete();
        return redirect()
            ->route('users');
    }
    public function create_user()
    {
        $groups = new usergroup();
        return view('admin.users.create', ['groups' => $groups->all()]);
    }
    public function user_register(Request $request)
    {
        $data = $request->validate([
            'name' => ["required", "string"],
            'email' => ["required", "email", "unique:users"],
            'password' => ["required", "confirmed"],
            'phone' => ["required", "string"],
            'groupid' => ["nullable", "integer"]
        ]);

        // dd($data);
        $user = User::create([
            'name' => $data["name"],
            'email' => $data["email"],
            'password' => bcrypt($data["password"]),
            'phone' => $data["phone"],
            'GroupId' => $data["groupid"]
        ]);

        return redirect()->route('users');
    }

    public function user_edit($id)
    {
        $user = User::findOrFail($id);
        $groups = new usergroup();
        return view('admin.users.edit', ['user' => $user, 'groups' => $groups->all()]);
    }
    public function update_user(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => ["required", "string"],
            'email' => ["required", "email"],
            'password' => ["required", "confirmed"],
            'phone' => ["required", "string"],
            'groupid' => ["nullable", "integer"]
        ]);

        // dd($data);
        $user->name = $data["name"];
        $user->email = $data["email"];
        $user->password = bcrypt($data["password"]);
        $user->phone = $data["phone"];
        $user->GroupId = $data["groupid"];
        $user->save();

        return redirect()->route('user_edit', $user->id);
    }
}
