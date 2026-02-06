<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::query()->orderBy('id')->get();
        $country = Country::query()->where('code', 'GAB')->first();

        return view('management.users.index', compact('users', 'country'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:64'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'integer', 'in:0,1'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $country = Country::query()->where('code', 'GAB')->first();

        User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'country_id' => $country?->id ?? 1,
            'is_active' => true,
        ]);

        return back()->with('status', 'Utilisateur ajoute.');
    }

    public function toggle(User $user): RedirectResponse
    {
        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('status', 'Statut utilisateur mis a jour.');
    }
}
