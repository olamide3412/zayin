<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;

class AuthController extends Controller
{
    public function showLinkRequestForm()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required'], // Can be email or phone conceptually but we check email mostly
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');
        $loginInput = trim($credentials['login']);

        // Determine if input is email or username, then authenticate
        $field = filter_var($loginInput, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $loginInput, 'password' => $credentials['password']], $remember) ||
            Auth::attempt(['email' => $loginInput, 'password' => $credentials['password']], $remember) ||
            Auth::attempt(['username' => $loginInput, 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard')->with('message', 'Login successful!');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
