<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('management.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, true)) {
            return back()->with('error', 'Identifiants invalides.')->withInput();
        }

        $request->session()->regenerate();

        return redirect()->route('management.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('management.login');
    }

    public function showChangePassword()
    {
        return view('management.auth.change-password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = $request->user();

        if (!$user || !Hash::check($data['current_password'], $user->password)) {
            return back()->with('error', 'Mot de passe actuel invalide.');
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        return back()->with('status', 'Mot de passe mis a jour.');
    }
}
