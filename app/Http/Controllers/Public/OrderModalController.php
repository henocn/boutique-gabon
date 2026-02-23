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

        // Save client info in cookies for next time
        $cookieName = cookie('order_client_name', $data['client_name'], 525600); // 1 year
        $cookieContact = cookie('order_client_contact', $data['client_contact'], 525600);
        $cookieAddress = cookie('order_client_address', $data['client_address'] ?? '', 525600);

        $productId = $data['product_id'];
        $cookieKey = 'ordered_' . $productId;
        $orderedAt = $request->cookie($cookieKey);
        $now = now()->timestamp;
        if ($orderedAt && ($now - (int)$orderedAt) < 7200) {
            return back()->withErrors(['order' => 'Vous avez déjà commandé ce produit il y a moins de 2 heures.']);
        }
        // Set cookie for 2h
        return back()->with('status', 'Votre commande a bien été prise en compte !')
            ->withCookie(cookie($cookieKey, $now, 120))
            ->withCookie($cookieName)
            ->withCookie($cookieContact)
            ->withCookie($cookieAddress);
    }
}
