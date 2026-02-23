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
        $deliveredOrders = (clone $orderScope)->where('orders.status', $delivered)->count();
        $pendingOrders = (clone $orderScope)->where('orders.status', OrderStatus::New)->count();
        $cancelledOrders = (clone $orderScope)->where('orders.status', OrderStatus::Cancelled)->count();
        $deliveredRevenue = (clone $orderScope)
            ->where('orders.status', $delivered)
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->select(DB::raw('SUM(products.price_sell * COALESCE(orders.quantity, 1)) as revenue'))
            ->value('revenue') ?? 0;
        $totalBenefit = (clone $orderScope)
            ->where('orders.status', $delivered)
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->select(DB::raw('SUM((products.price_sell - products.price_buy) * COALESCE(orders.quantity, 1)) as benefit'))
            ->value('benefit') ?? 0;


        // Statistiques utilisateurs
        $totalUsers = User::count();
        $adminUsers = User::where('role', User::ROLE_ADMIN)->count();
        $managerUsers = User::where('role', User::ROLE_MANAGER)->count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();

        $orderSeries = $this->buildOrderSeries($user);

        $data = [
            'isAdmin' => $user->isAdmin(),
            'totalOrders' => $totalOrders,
            'deliveredOrders' => $deliveredOrders,
            'pendingOrders' => $pendingOrders,
            'cancelledOrders' => $cancelledOrders,
            'deliveredRevenue' => (int) $deliveredRevenue,
            'totalBenefit' => (int) $totalBenefit,
            'orderSeries' => $orderSeries,
            'totalUsers' => $totalUsers,
            'adminUsers' => $adminUsers,
            'managerUsers' => $managerUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
        ];

        if ($user->isAdmin()) {
                        // Top vendeurs (managers/assistantes)
                        $topSellersRows = Order::query()
                            ->select(
                                'products.manager_id',
                                DB::raw('COUNT(orders.id) as total_sales'),
                                DB::raw('SUM(products.price_sell * COALESCE(orders.quantity, 1)) as total_revenue'),
                                DB::raw('SUM((products.price_sell - products.price_buy) * COALESCE(orders.quantity, 1)) as total_benefit')
                            )
                            ->join('products', 'orders.product_id', '=', 'products.id')
                            ->where('orders.status', $delivered)
                            ->groupBy('products.manager_id')
                            ->orderByDesc('total_sales')
                            ->take(10)
                            ->get();

                        $managers = User::query()
                            ->whereIn('id', $topSellersRows->pluck('manager_id'))
                            ->get()
                            ->keyBy('id');

                        $topSellers = $topSellersRows->map(function ($row) use ($managers) {
                            return [
                                'user' => $managers->get($row->manager_id),
                                'total_sales' => (int) $row->total_sales,
                                'total_revenue' => (int) $row->total_revenue,
                                'total_benefit' => (int) $row->total_benefit,
                            ];
                        })->sortByDesc('total_sales')->values();
            $activeCategories = Category::query()->where('is_active', true)->count();
            $totalCategories = Category::query()->count();
            $activeProducts = Product::query()->where('status', ProductStatus::Active)->count();
            $totalProducts = Product::query()->count();

            $latestProducts = Product::query()
                ->with(['category', 'productImages'])
                ->latest()
                ->take(6)
                ->get();

            $topSoldRows = Order::query()
                ->select('orders.product_id', DB::raw('SUM(COALESCE(orders.quantity, 1)) as sold_count'))
                ->join('products', 'orders.product_id', '=', 'products.id')
                ->where('orders.status', $delivered)
                ->groupBy('orders.product_id')
                ->orderByDesc('sold_count')
                ->take(5)
                ->get();

            $topSoldProducts = Product::query()
                ->with('category')
                ->whereIn('id', $topSoldRows->pluck('product_id'))
                ->get()
                ->keyBy('id');

            $topSold = $topSoldRows->map(function ($row) use ($topSoldProducts) {
                return (object) [
                    'product' => $topSoldProducts->get($row->product_id),
                    'sold_count' => (int) $row->sold_count,
                ];
            });

            $topRevenueRows = Order::query()
                ->select(
                    'orders.product_id',
                    DB::raw('SUM(COALESCE(orders.quantity, 1)) as sold_count'),
                    DB::raw('SUM(products.price_sell * COALESCE(orders.quantity, 1)) as revenue')
                )
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
                'totalCategories' => $totalCategories,
                'activeProducts' => $activeProducts,
                'totalProducts' => $totalProducts,
                'latestProducts' => $latestProducts,
                'topSold' => $topSold,
                'topRevenue' => $topRevenue,
                'topSellers' => $topSellers,
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
