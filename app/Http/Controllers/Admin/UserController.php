<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $user = new User();

        return view('admin.users.create', compact('user'));
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Utilisateur cree.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        if ($request->user()->is($user)) {
            if ($data['role'] !== $user->role) {
                return back()->withErrors(['role' => 'Impossible de changer votre role.']);
            }
            if (! $data['is_active']) {
                return back()->withErrors(['is_active' => 'Impossible de desactiver votre compte.']);
            }
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Utilisateur mis a jour.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if (request()->user()->is($user)) {
            return back()->withErrors(['user' => 'Impossible de supprimer votre compte.']);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Utilisateur supprime.');
    }
}
