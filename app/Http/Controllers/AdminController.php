<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;
use App\Models\usergroup;

use App\Models\ApplicationStatus;
use App\Models\InsuranceCompany;
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
            ->paginate(5);

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

    public function application_statuses_index()

    {
        $application_statuses = new ApplicationStatus();
        return view('admin.application_statuses.index', ['application_statuses' => $application_statuses->all()]);
    }

    public function destroy_application_status($id)
    {
        $application_status = ApplicationStatus::findOrFail($id);
        $application_status->delete();
        return redirect()
            ->route('application_statuses');
    }
    public function create_application_status()
    {
        $application_statuses = new ApplicationStatus();
        return view('admin.application_statuses.create', ['application_statuses' => $application_statuses->all()]);
    }
    public function application_status_register(Request $request)
    {
        $data = $request->validate([
            'StatusName' => ["required", "string"],

        ]);

        // dd($data);
        $application_status = ApplicationStatus::create([
            'StatusName' => $data["StatusName"]
        ]);

        return redirect()->route('application_statuses');
    }

    public function application_status_edit($id)
    {
        $application_status = ApplicationStatus::findOrFail($id);
        return view('admin.application_statuses.edit', ['application_status' => $application_status]);
    }
    public function update_application_status(Request $request, $id)
    {
        $application_status = ApplicationStatus::findOrFail($id);
        $data = $request->validate([
            'StatusName' => ["required", "string"]
        ]);

        // dd($data);
        $application_status->StatusName = $data["StatusName"];
        $application_status->save();

        return redirect()->route('application_statuses');
    }

    public function insurance_companies_index()

    {
        $insurance_companies = new InsuranceCompany();
        return view('admin.insurance_companies.index', ['insurance_companies' => $insurance_companies->all()]);
    }

    public function destroy_insurance_company($id)
    {
        $insurance_company = InsuranceCompany::findOrFail($id);
        $insurance_company->delete();
        return redirect()
            ->route('insurance_companies');
    }
    public function create_insurance_company()
    {
        $insurance_companies = new InsuranceCompany();
        return view('admin.insurance_companies.create', ['insurance_companies' => $insurance_companies->all()]);
    }
    public function insurance_company_register(Request $request)
    {
        $data = $request->validate([
            'name' => ["required", "string"],
            'email' => ["required", "email"],
            'manager' => ["required", "confirmed"],
            'phone' => ["required", "string"],
            'address' => ["required", "string"]

        ]);

        // dd($data);
        $insurance_company = InsuranceCompany::create([
            'name' => $data["name"],
            'email' => $data["email"],
            'manager' => $data["manager"],
            'phone' => $data["phone"],
            'address' => $data["address"]
        ]);

        return redirect()->route('insurance_companies');
    }

    public function insurance_company_edit($id)
    {
        $insurance_company = InsuranceCompany::findOrFail($id);
        return view('admin.insurance_companies.edit', ['insurance_company' => $insurance_company]);
    }
    public function update_insurance_company(Request $request, $id)
    {
        $insurance_company = InsuranceCompany::findOrFail($id);
        $data = $request->validate([
            'name' => ["required", "string"],
            'email' => ["required", "email"],
            'manager' => ["required", "confirmed"],
            'phone' => ["required", "string"],
            'address' => ["required", "string"]
        ]);

        // dd($data);
        $insurance_company->name = $data["name"];
        $insurance_company->email = $data["email"];
        $insurance_company->manager = $data["manager"];
        $insurance_company->phone = $data["phone"];
        $insurance_company->address = $data["address"];
        $insurance_company->save();

        return redirect()->route('insurance_companies');
    }
}
