<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }



    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (auth('web')->attempt($data)) {
            session(['GroupId' => Auth::user()->GroupId]);
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Предоставленные учетные данные не соответствуют нашим записям.',
        ]);
    }


    public function logout(Request $request)
    {
        auth('web')->logout();
        return redirect()->route('home');
    }
}
