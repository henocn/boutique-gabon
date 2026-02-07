<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $delivered = OrderStatus::Delivered;

        $orderScope = Order::query()->with('product');

        if ($user->role === User::ROLE_MANAGER) {
            $orderScope->whereHas('product', function ($builder) use ($user): void {
                $builder->where('manager_id', $user->id);
            });
        }

        $totalOrders = (clone $orderScope)->count();
        $deliveredOrders = (clone $orderScope)->where('status', $delivered)->count();
        $deliveredRevenue = (clone $orderScope)
            ->where('status', $delivered)
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->sum('products.price_sell');

        $orderSeries = $this->buildOrderSeries($user);

        $data = [
            'isAdmin' => $user->isAdmin(),
            'totalOrders' => $totalOrders,
            'deliveredOrders' => $deliveredOrders,
            'deliveredRevenue' => (int) $deliveredRevenue,
            'orderSeries' => $orderSeries,
        ];

        if ($user->isAdmin()) {
            $activeCategories = Category::query()->where('is_active', true)->count();
            $activeProducts = Product::query()->where('status', ProductStatus::Active)->count();

            $latestProducts = Product::query()
                ->with(['category', 'productImages'])
                ->latest()
                ->take(6)
                ->get();

            $topSold = Product::query()
                ->with('category')
                ->withCount([
                    'orders as sold_count' => function ($query) use ($delivered): void {
                        $query->where('status', $delivered);
                    },
                ])
                ->orderByDesc('sold_count')
                ->take(5)
                ->get();

            $topRevenueRows = Order::query()
                ->select('orders.product_id', DB::raw('COUNT(*) as sold_count'), DB::raw('SUM(products.price_sell) as revenue'))
                ->join('products', 'orders.product_id', '=', 'products.id')
                ->where('orders.status', $delivered)
                ->groupBy('orders.product_id')
                ->orderByDesc('revenue')
                ->take(5)
                ->get();

            $productMap = Product::query()
                ->whereIn('id', $topRevenueRows->pluck('product_id'))
                ->get()
                ->keyBy('id');

            $topRevenue = $topRevenueRows->map(function ($row) use ($productMap) {
                return [
                    'product' => $productMap->get($row->product_id),
                    'revenue' => (int) $row->revenue,
                    'sold_count' => (int) $row->sold_count,
                ];
            });

            $data = array_merge($data, [
                'activeCategories' => $activeCategories,
                'activeProducts' => $activeProducts,
                'latestProducts' => $latestProducts,
                'topSold' => $topSold,
                'topRevenue' => $topRevenue,
            ]);
        }

        return view('dashboard', $data);
    }

    private function buildOrderSeries(User $user): array
    {
        $start = Carbon::today()->subDays(6);
        $end = Carbon::today()->endOfDay();

        $seriesQuery = Order::query();

        if ($user->role === User::ROLE_MANAGER) {
            $seriesQuery->whereHas('product', function ($builder) use ($user): void {
                $builder->where('manager_id', $user->id);
            });
        }

        $counts = $seriesQuery
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->pluck('total', 'date');

        $series = [];
        $max = 0;

        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i);
            $label = $date->format('d/m');
            $count = (int) ($counts[$date->toDateString()] ?? 0);
            $max = max($max, $count);
            $series[] = [
                'label' => $label,
                'count' => $count,
            ];
        }

        $max = max($max, 1);

        return array_map(function (array $item) use ($max): array {
            $height = (int) round(($item['count'] / $max) * 100);
            if ($height > 0 && $height < 10) {
                $height = 10;
            }
            $item['height'] = $height;
            return $item;
        }, $series);
    }
}
