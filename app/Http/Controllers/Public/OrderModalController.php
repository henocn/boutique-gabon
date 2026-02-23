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

        // Utiliser la session pour limiter la commande par produit pendant 2h
        $productId = $data['product_id'];
        $sessionKey = 'ordered_' . $productId;
        $orderedAt = session($sessionKey);
        $now = now()->timestamp;
        if ($orderedAt && ($now - (int)$orderedAt) < 7200) {
            return back()->withErrors(['order' => 'Vous avez déjà commandé ce produit il y a moins de 2 heures.']);
        }

        // Création de la commande
        $orderData = [
            'client_name' => $data['client_name'],
            'client_contact' => $data['client_contact'],
            'client_comment' => $request->input('client_comment'),
            'client_address' => $data['client_address'] ?? null,
            'product_id' => $productId,
            'quantity' => max(1, (int) $request->input('quantity', 1)),
            'status' => \App\Enums\OrderStatus::New,
        ];
        // On retire client_address si la colonne n'existe pas
        if (!\Schema::hasColumn('orders', 'client_address')) {
            unset($orderData['client_address']);
        }
        \App\Models\Order::create($orderData);

        // Stocker les infos client en session pour pré-remplir le formulaire
        session([
            'order_client_name' => $data['client_name'],
            'order_client_contact' => $data['client_contact'],
            'order_client_address' => $data['client_address'] ?? '',
            $sessionKey => $now,
        ]);

        return back()->with('status', 'Votre commande a bien été prise en compte !');
    }

    public function create(Request $request): RedirectResponse
    {
        return redirect('/')->with('status', 'Commande envoyée.');
    }
}
