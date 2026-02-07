<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderUpdateRequest;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $query = Order::query()->with(['product.category', 'product.manager']);

        if ($user->role === User::ROLE_MANAGER) {
            $query->whereHas('product', function ($builder) use ($user): void {
                $builder->where('manager_id', $user->id);
            });
        }

        $orders = $query->latest()->paginate(15);
        $statuses = OrderStatus::cases();

        return view('admin.orders.index', compact('orders', 'statuses'));
    }

    public function update(OrderUpdateRequest $request, Order $order): RedirectResponse
    {
        $user = Auth::user();

        if ($user->role === User::ROLE_MANAGER && $order->product?->manager_id !== $user->id) {
            abort(403);
        }

        $order->update($request->validated());

        return back()->with('status', 'Commande mise a jour.');
    }
}
