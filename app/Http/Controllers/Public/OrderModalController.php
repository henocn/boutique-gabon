<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cookie;

class OrderModalController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:100'],
            'client_contact' => ['required', 'string', 'max:30'],
            'client_address' => ['nullable', 'string', 'max:255'],
            'product_id' => ['required', 'integer'],
        ]);
        Session::put('order_client', $data);

        $productId = $data['product_id'];
        $cookieKey = 'ordered_' . $productId;
        $orderedAt = $request->cookie($cookieKey);
        $now = now()->timestamp;
        if ($orderedAt && ($now - (int)$orderedAt) < 7200) {
            return back()->withErrors(['order' => 'Vous avez déjà commandé ce produit il y a moins de 2 heures.']);
        }
        // Set cookie for 2h
        return back()->with('status', 'Votre commande a bien été prise en compte !')
            ->withCookie(cookie($cookieKey, $now, 120));
    }
}
