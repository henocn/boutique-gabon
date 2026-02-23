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

        $tab = request('tab', 'active');
        $baseQuery = Order::query();

        if ($user->role === User::ROLE_MANAGER) {
            $baseQuery->whereHas('product', function ($builder) use ($user): void {
                $builder->where('manager_id', $user->id);
            });
        }

        $counts = [
            'new' => (clone $baseQuery)->where('status', OrderStatus::New)->count(),
            'processed' => (clone $baseQuery)->where('status', OrderStatus::Processed)->count(),
            'unreachable' => (clone $baseQuery)->where('status', OrderStatus::Unreachable)->count(),
            'delivered' => (clone $baseQuery)->where('status', OrderStatus::Delivered)->count(),
            'other' => (clone $baseQuery)
                ->whereNotIn('status', [
                    OrderStatus::New,
                    OrderStatus::Processed,
                    OrderStatus::Unreachable,
                    OrderStatus::Delivered,
                ])
                ->count(),
        ];

        $query = (clone $baseQuery)->with(['product.category', 'product.manager']);

        switch ($tab) {
            case 'unreachable':
                $query->where('status', OrderStatus::Unreachable);
                break;
            case 'delivered':
                $query->where('status', OrderStatus::Delivered);
                break;
            case 'other':
                $query->whereNotIn('status', [
                    OrderStatus::New,
                    OrderStatus::Processed,
                    OrderStatus::Unreachable,
                    OrderStatus::Delivered,
                ]);
                break;
            case 'active':
            default:
                $tab = 'active';
                $query->whereIn('status', [OrderStatus::New, OrderStatus::Processed]);
                break;
        }

        $orders = $query->latest()->paginate(15)->withQueryString();
        $statuses = OrderStatus::cases();

        return view('admin.orders.index', compact('orders', 'statuses', 'counts', 'tab'));
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
