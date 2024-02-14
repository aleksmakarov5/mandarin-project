<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\user;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (auth('web')->attempt($data)) {
            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Предоставленные учетные данные не соответствуют нашим записям.',
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ["required", "string"],
            'email' => ["required", "email"],
            'password' => ["required", "confirmed"]
            // 'password' => ["required", "confirmed"]
        ]);

        //  dd($data);
        $user = User::create([
            'name' => $data["name"],
            'email' => $data["email"],
            'password' => bcrypt($data["password"])
        ]);
        if ($user) {
            auth('web')->login($user);
        }
        return redirect()->route('home');
    }
    public function logout(Request $request)
    {
        auth('web')->logout();
        return redirect()->route('home');
    }
}
