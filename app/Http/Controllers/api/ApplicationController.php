<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\application;
use App\Models\user_filter;

class ApplicationController extends Controller
{
    public function destroy($id)
    {
        $application = application::findOrFail($id);
        $application->delete();
        return true;
    }
    public function delete_filter(Request $request)
    {
        $data = $this->validate($request, [
            'id_city' => 'required',
            'id_user' => 'required'
        ]);
        $user_filter = user_filter::where('id_user', '=', $data['id_user'])->where('id_city', '=', $data['id_city'])->first();
        $user_filter->delete();
        return true;
    }
    public function add_filter(Request $request)
    {
        $data = $this->validate($request, [
            'id_city' => 'required',
            'id_user' => 'required'
        ]);
        $user_filter = user_filter::firstOrCreate(['id_user' => $data['id_user'], 'id_city' => $data['id_city']]);

        return true;
    }
    public function delete_filters(Request $request)
    {
        $data = $this->validate($request, [

            'id_user' => 'required'
        ]);
        $user_filters = user_filter::where('id_user', '=', $data['id_user'])->get();
        foreach ($user_filters as $user_filter) {
            $user_filter->delete();
        }
        return true;
    }
    public function add_filters(Request $request)
    {
        $data = $this->validate($request, [
            'id_city' => 'required|array',
            'id_user' => 'required'
        ]);
        foreach ($data['id_city'] as $city_id) {
            $user_filter = user_filter::firstOrCreate(['id_user' => $data['id_user'], 'id_city' => $city_id]);
        }

        return true;
    }
}
