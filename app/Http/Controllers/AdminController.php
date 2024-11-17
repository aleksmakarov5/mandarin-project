<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;
use App\Models\usergroup;
use App\Models\OurCompany;
use App\Models\user_role;
use App\Models\user_city;

use App\Models\ApplicationStatus;
use App\Models\InsuranceCompany;
use App\Models\failure_var;
use App\Models\service;
use App\Models\role_company;
use App\Models\city;

use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{
    public function index()
    {

        session(['rout' => 'admin']);
        return view('admin.index');
    }
    public function users_index()
    {
        $users = DB::table('users')
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
    public function destroy_role($role_id)
    {
        $role_companies = role_company::where('id_role', '=', $role_id)->get();
        foreach ($role_companies as $rc) {
            $rc->delete();
        }

        $user_role = user_role::findOrFail($role_id);
        $user_role->delete();
        return redirect()
            ->back();
    }
    public function destroy_company($id)
    {
        $role_company = role_company::findOrFail($id);
        $role_company->delete();
        return redirect()
            ->back();
    }
    public function destroy_city($id)
    {
        $user_city = user_city::findOrFail($id);
        $user_city->delete();
        return redirect()
            ->back();
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
            'phone' => ["required", "string"]
        ]);

        // dd($data);
        $user = User::create([
            'name' => $data["name"],
            'email' => $data["email"],
            'password' => bcrypt($data["password"]),
            'phone' => $data["phone"]
        ]);
        //  dd($user);
        return redirect()->route('users');
    }

    public function user_edit($id)
    {
        $user = User::findOrFail($id);
        $user->roles = user_role::where('id_user', '=', $user->id)
            ->join('user_groups', 'user_groups.id', '=', 'user_roles.id_group')
            ->select('user_roles.*', 'user_groups.GroupName')
            ->get();
        $user->city = user_city::where('id_user', '=', $user->id)
            ->join('cities', 'cities.id', '=', 'user_cities.id_city')
            ->select('user_cities.*', 'cities.name as city')
            ->get();
        foreach ($user->roles as $role) {
            $role->company = role_company::where('id_role', '=', $role->id)
                ->join('our_companies', 'our_companies.id', '=', 'role_companies.id_company')
                ->select('role_companies.*', 'our_companies.our_name')
                ->get();
        }
        // dd($user);
        $groups = new usergroup();
        $companies = new OurCompany();
        $cities = new city();
        return view('admin.users.edit', ['user' => $user, 'groups' => $groups->all(), 'companies' => $companies->all(), 'cities' => $cities->all()]);
    }
    public function update_user(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => ["required", "string"],
            'email' => ["required", "email"],
            'password' => ["nullable", "confirmed"],
            'phone' => ["required", "string"],
            'groupid' => ["nullable", "integer"],
            'id_city' => ["nullable", "integer"]
        ]);

        // dd($data);
        $user->name = $data["name"];
        $user->email = $data["email"];
        if ($data["password"])
            $user->password = bcrypt($data["password"]);
        $user->phone = $data["phone"];
        $user->save();

        if ($data['groupid'] != null) {

            $user_role = user_role::firstorcreate(
                ['id_user' => $id, 'id_group' => $data['groupid']]
            );
        }

        if ($data['id_city'] != null) {
            $user_city = user_city::firstorcreate(
                ['id_user' => $id, 'id_city' => $data['id_city']]
            );
        }

        return redirect()->back();
    }
    public function role_company_create(Request $request, $id)
    {

        $data = $request->validate([

            'id_company' => ["nullable", "integer"],

        ]);

        if ($data['id_company'] != null && $data['id_company'] != 0) {
            $role_company = role_company::firstorcreate(
                ['id_role' => $id, 'id_company' => $data['id_company']]
            );
        }

        return redirect()->back();
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
        $application_statuses = DB::table('application_statuses')
            ->join('user_groups', 'application_statuses.current_group', '=', 'user_groups.id')
            ->select('application_statuses.*', 'user_groups.GroupName')
            ->get();
        return view('admin.application_statuses.index', ['application_statuses' => $application_statuses]);
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
        $groups = new usergroup();
        $application_statuses = new ApplicationStatus();
        return view('admin.application_statuses.create', ['application_statuses' => $application_statuses->all(), 'groups' => $groups->all()]);
    }
    public function application_status_register(Request $request)
    {
        $data = $request->validate([
            'StatusName' => ["required", "string"],
            'current_group' => ["required"]

        ]);

        // dd($data);
        $application_status = ApplicationStatus::create([
            'StatusName' => $data["StatusName"],
            'current_group' => $data["current_group"]
        ]);

        return redirect()->route('application_statuses');
    }

    public function application_status_edit($id)
    {
        $application_status = ApplicationStatus::findOrFail($id);
        $groups = new usergroup();
        return view('admin.application_statuses.edit', ['application_status' => $application_status, 'groups' => $groups->all()]);
    }
    public function update_application_status(Request $request, $id)
    {
        $application_status = ApplicationStatus::findOrFail($id);
        $data = $request->validate([
            'StatusName' => ["required", "string"],
            'current_group' => ["required"]

        ]);

        // dd($data);
        $application_status->StatusName = $data["StatusName"];
        $application_status->current_group = $data["current_group"];
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
            'manager' => ["required", "string"],
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
            'manager' => ["required", "string"],
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


    public function fail_var()

    {
        $fail_vars = new failure_var();
        return view('admin.fail_var', ['fail_vars' => $fail_vars->all()]);
    }
    public function fail_var_edit(Request $request, $id)
    {
        $fail_var = failure_var::findOrFail($id);
        $data = $request->validate([
            'variant' => ["required", "string"]
        ]);
        $fail_var->variant = $data['variant'];
        $fail_var->save();
        return redirect()->route('fail_var');
    }

    public function fail_var_register(Request $request)
    {
        $data = $request->validate([
            'variant' => ["required", "string"]
        ]);
        $fail_var = failure_var::create([
            'variant' => $data['variant']
        ]);
        return redirect()->route('fail_var');
    }

    public function destroy_fail_var($id)
    {
        $fail_var = failure_var::findOrFail($id);
        $fail_var->delete();
        return redirect()
            ->route('fail_var');
    }


    public function services_index()
    {
        $services = service::leftjoin('users', 'services.id_user', '=', 'users.id')
            ->select('services.*', 'users.name')
            ->get()->all();
        return view('admin.services.index', ['services' => $services]);
    }

    public function service_edit($id)
    {
        $service = service::findOrFail($id);
        $users = user::where('GroupId', '=', '9')->get();
        return view('admin.services.edit', ['service' => $service, 'users' => $users->all()]);
    }
    public function update_service(Request $request, $id)
    {
        $service = service::findOrFail($id);
        $data = $request->validate([
            'service_name' => ["required", "string"],
            'service_description' => ["required", "string"],
            'id_user' => ["nullable", "integer"]
        ]);

        // dd($data);
        $service->service_name = $data["service_name"];
        $service->service_description = $data["service_description"];
        $service->id_user = $data["id_user"];
        $service->save();

        return redirect()->route('services');
    }

    public function destroy_service($id)
    {
        $service = service::findOrFail($id);
        $service->delete();
        return redirect()
            ->route('services');
    }
    public function create_service()
    {
        $users = user::where('GroupId', '=', '9')->get();
        return view('admin.services.create', ['users' => $users->all()]);
    }
    public function store_service(Request $request)
    {
        $data = $request->validate([
            'service_name' => ["required", "string"],
            'service_description' => ["required", "string"],
            'id_user' => ["nullable", "integer"]
        ]);

        // dd($data);
        $service = service::create([
            'service_name' => $data["service_name"],
            'service_description' => $data["service_description"],
            'id_user' => $data["id_user"]
        ]);
        //  dd($service);
        return redirect()->route('services');
    }
}
