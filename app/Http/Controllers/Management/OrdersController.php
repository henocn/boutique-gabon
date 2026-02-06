<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Depense;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrdersController extends Controller
{
    public function index()
    {
        $query = Order::with(['product', 'manager'])->orderByDesc('created_at');
        $user = Auth::user();

        if ($user && (int)$user->role === 0) {
            $query->where('manager_id', $user->id);
        }

        $orders = $query->get();

        return view('management.orders.index', compact('orders'));
    }

    public function archive()
    {
        $query = Order::with(['product', 'manager'])
            ->where('status', 'deliver')
            ->orderByDesc('updated_at');
        $user = Auth::user();

        if ($user && (int)$user->role === 0) {
            $query->where('manager_id', $user->id);
        }

        $orders = $query->get();

        return view('management.orders.archive', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:new,deliver,processing,remind,unreachable,canceled'],
            'manager_note' => ['nullable', 'string', 'max:128'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'total_price' => ['nullable', 'integer', 'min:0'],
            'delivery_fee' => ['nullable', 'integer', 'min:0'],
        ]);

        $previousStatus = $order->status;
        if (array_key_exists('quantity', $data)) {
            $order->quantity = $data['quantity'] ?? $order->quantity;
        }
        if (array_key_exists('total_price', $data)) {
            $order->total_price = $data['total_price'] ?? $order->total_price;
            if (($order->quantity ?? 0) > 0) {
                $order->unit_price = (int) round($order->total_price / $order->quantity);
            }
        }
        $order->status = $data['status'];
        $order->manager_note = $data['manager_note'] ?? $order->manager_note;
        $order->save();

        if ($previousStatus !== 'deliver' && $order->status === 'deliver') {
            $product = Product::query()->find($order->product_id);
            if ($product && $order->quantity > 0) {
                $product->decrement('quantity', $order->quantity);
            }

            $fee = (int)($data['delivery_fee'] ?? 0);
            if ($fee > 0) {
                Depense::query()->create([
                    'type' => 'products',
                    'product_id' => $order->product_id,
                    'manager_id' => $order->manager_id,
                    'cout' => $fee,
                    'date' => now(),
                    'description' => 'Livraison',
                ]);
            }
        }

        return back()->with('status', 'Commande mise a jour.');
    }
}
