<?php

namespace App\Http\Controllers\Public;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class CartController extends Controller
{
    public function index(): View
    {
        $expired = $this->clearIfExpired();
        $cart = session()->get('cart', []);
        $productIds = array_keys($cart);

        $products = Product::query()
            ->with(['productImages', 'category'])
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $items = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $products->get((int) $productId);
            if (! $product) {
                continue;
            }
            $quantity = max(1, (int) $quantity);
            $subtotal = $product->price_sell * $quantity;
            $total += $subtotal;
            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }

        return view('cart', compact('items', 'total', 'expired'));
    }

    public function add(Request $request): RedirectResponse
    {
        $this->clearIfExpired();
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = session()->get('cart', []);
        $productId = (string) $data['product_id'];
        $cart[$productId] = ($cart[$productId] ?? 0) + $data['quantity'];
        session()->put('cart', $cart);
        session()->put('cart_created_at', session('cart_created_at') ?? Carbon::now()->timestamp);

        return back()->with('status', 'Produit ajoute au panier.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        if ($this->clearIfExpired()) {
            return back()->withErrors(['cart' => 'Panier reinitialise apres 30 minutes.']);
        }
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ]);

        $cart = session()->get('cart', []);
        $productId = (string) $product->id;

        if ($data['quantity'] <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $data['quantity'];
        }

        session()->put('cart', $cart);
        session()->put('cart_created_at', session('cart_created_at') ?? Carbon::now()->timestamp);

        return back()->with('status', 'Panier mis a jour.');
    }

    public function remove(Product $product): RedirectResponse
    {
        if ($this->clearIfExpired()) {
            return back()->withErrors(['cart' => 'Panier reinitialise apres 30 minutes.']);
        }
        $cart = session()->get('cart', []);
        unset($cart[(string) $product->id]);
        session()->put('cart', $cart);

        return back()->with('status', 'Produit retire.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        if ($this->clearIfExpired()) {
            return back()->withErrors(['cart' => 'Panier reinitialise apres 30 minutes.']);
        }
        $data = $request->validate([
            'client_name' => ['required', 'string', 'max:120'],
            'client_contact' => ['required', 'string', 'max:120'],
            'client_comment' => ['nullable', 'string', 'max:500'],
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return back()->withErrors(['cart' => 'Votre panier est vide.']);
        }

        DB::transaction(function () use ($cart, $data): void {
            foreach ($cart as $productId => $quantity) {
                Order::create([
                    'client_name' => $data['client_name'],
                    'client_contact' => $data['client_contact'],
                    'client_comment' => $data['client_comment'] ?? null,
                    'product_id' => (int) $productId,
                    'quantity' => max(1, (int) $quantity),
                    'status' => OrderStatus::New,
                ]);
            }
        });

        session()->forget('cart');
        session()->forget('cart_created_at');

        return redirect('/')->with('status', 'Commande envoyee.');
    }

    private function clearIfExpired(): bool
    {
        $createdAt = session()->get('cart_created_at');
        if (! $createdAt) {
            return false;
        }

        $expiresAt = Carbon::createFromTimestamp((int) $createdAt)->addMinutes(30);
        if (Carbon::now()->greaterThan($expiresAt)) {
            session()->forget('cart');
            session()->forget('cart_created_at');
            return true;
        }

        return false;
    }
}
