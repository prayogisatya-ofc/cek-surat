<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required','string'],
            'password' => ['required','string'],
        ]);

        if (Auth::attempt(['username' => $data['username'], 'password' => $data['password']], true)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        return redirect()->route('login')->withErrors(['username' => 'Login gagal, pastikan username & password benar.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
