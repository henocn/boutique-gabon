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

        $tab = request('tab', 'to-process');
        $baseQuery = Order::query();

        if ($user->role === User::ROLE_MANAGER) {
            $baseQuery->whereHas('product', function ($builder) use ($user): void {
                $builder->where('manager_id', $user->id);
            });
        }

        $counts = [
            'to_process' => (clone $baseQuery)->whereIn('status', [OrderStatus::New, OrderStatus::Remind])->count(),
            'unreachable' => (clone $baseQuery)->where('status', OrderStatus::Unreachable)->count(),
            'processing' => (clone $baseQuery)->where('status', OrderStatus::Processing)->count(),
            'delivered_today' => (clone $baseQuery)
                ->where('status', OrderStatus::Delivered)
                ->whereDate('updated_at', today())
                ->count(),
        ];

        $query = (clone $baseQuery)->with(['product.manager']);

        switch ($tab) {
            case 'unreachable':
                $query->where('status', OrderStatus::Unreachable);
                break;
            case 'processing':
                $query->where('status', OrderStatus::Processing);
                break;
            case 'delivered-today':
                $query->where('status', OrderStatus::Delivered)
                    ->whereDate('updated_at', today());
                break;
            case 'to-process':
            default:
                $tab = 'to-process';
                $query->whereIn('status', [OrderStatus::New, OrderStatus::Remind]);
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
