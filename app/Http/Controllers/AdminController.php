<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;
use App\Models\usergroup;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }
    public function users_index()
    {
        $users = DB::table('users')
            ->join('user_groups', 'users.GroupId', '=', 'user_groups.id')
            ->select('users.*', 'user_groups.GroupName')
            ->get();

        return view('admin.users.index', ['users' => $users]);
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
            'GroupId' => ["required", "integer"]
        ]);

        // dd($data);
        $user = User::create([
            'name' => $data["name"],
            'email' => $data["email"],
            'password' => bcrypt($data["password"]),
            'phone' => $data["phone"],
            'GroupId' => $data["GroupId"]
        ]);
        //  dd($user);
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

        return redirect()->route('users');
    }

    public function usergroups_index()

    {
        $usergroups = new usergroup();
        return view('admin.usergroups.index', ['usergroups' => $usergroups->all()]);
    }

    public function destroy_usergroup($id)
    {
        $usergroup = usergroup::findOrFail($id);
        $usergroup->delete();
        return redirect()
            ->route('usergroups');
    }
    public function create_usergroup()
    {
        $usergroups = new usergroup();
        return view('admin.usergroups.create', ['usergroups' => $usergroups->all()]);
    }
    public function usergroup_register(Request $request)
    {
        $data = $request->validate([
            'groupname' => ["required", "string"],
            'groupdescription' => ["required", "string"]
        ]);

        // dd($data);
        $usergroup = UserGroup::create([
            'groupname' => $data["groupname"],
            'groupdescription' => $data["groupdescription"],
        ]);

        return redirect()->route('usergroups');
    }

    public function usergroup_edit($id)
    {
        $usergroup = UserGroup::findOrFail($id);
        return view('admin.usergroups.edit', ['usergroup' => $usergroup]);
    }
    public function update_usergroup(Request $request, $id)
    {
        $usergroup = UserGroup::findOrFail($id);
        $data = $request->validate([
            'groupname' => ["required", "string"],
            'groupdescription' => ["required", "string"]
        ]);

        // dd($data);
        $usergroup->groupname = $data["groupname"];
        $usergroup->groupdescription = $data["groupdescription"];
        $usergroup->save();

        return redirect()->route('usergroups');
    }
}
