<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductPack;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FrontOrderController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'pack_id' => ['nullable', 'integer', 'exists:product_packs,id'],
            'client_name' => ['required', 'string', 'max:64'],
            'client_phone' => ['required', 'string', 'max:64'],
            'client_adress' => ['required', 'string', 'max:128'],
            'client_note' => ['nullable', 'string', 'max:128'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::with(['countries', 'managers'])->findOrFail($data['product_id']);
        $country = Country::query()->where('code', 'GAB')->first();
        $sellingPrice = 0;

        if ($country) {
            $priceRow = $product->countries->firstWhere('id', $country->id);
            $sellingPrice = $priceRow?->pivot?->selling_price ?? 0;
        }

        $pack = null;
        if (!empty($data['pack_id'])) {
            $pack = ProductPack::query()->where('product_id', $product->id)->find($data['pack_id']);
        }

        $quantity = $pack?->quantity ?? ($data['quantity'] ?? 1);
        $unitPrice = $pack?->price ?? $sellingPrice;
        $totalPrice = $unitPrice * $quantity;
        $managerId = $product->managers->first()?->id;

        Order::query()->create([
            'product_id' => $product->id,
            'pack_id' => $pack?->id,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'purchase_price' => $product->purchase_price,
            'total_price' => $totalPrice,
            'client_name' => $data['client_name'],
            'client_country_id' => $country?->id ?? 1,
            'client_phone' => $data['client_phone'],
            'client_adress' => $data['client_adress'],
            'client_note' => $data['client_note'] ?? null,
            'manager_id' => $managerId,
            'status' => 'new',
        ]);

        return back()->with('order_message', 'Votre commande a ete enregistree.');
    }
}
