<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\application;

class ApplicationController extends Controller
{
    public function destroy($id)
    {
        $application = application::findOrFail($id);
        $application->delete();
        return true;
    }
}
