<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;

class OrderModalController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:100'],
            'client_contact' => ['required', 'string', 'max:30'],
            'client_address' => ['nullable', 'string', 'max:255'],
        ]);
        Session::put('order_client', $data);
        return back()->with('status', 'Informations client enregistrées.');
    }
}
