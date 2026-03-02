<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $delivered = OrderStatus::Delivered;

        [$start, $end, $startDate, $endDate] = $this->resolveDateRange($request);

        $orderScope = $this->buildOrderScope($user, $start, $end);
        $baseStats = $this->buildBaseStats($orderScope, $user, $start, $end, $delivered);
        $userStats = $this->buildUserStats();

        $data = array_merge(
            [
                'isAdmin' => $user->isAdmin(),
            ],
            $baseStats,
            $userStats
        );

        if ($user->isAdmin()) {
            $data = array_merge($data, $this->buildAdminStats($start, $end, $delivered));
        }

        $data['appliedRangeLabel'] = $this->buildAppliedRangeLabel($startDate, $endDate, $start, $end);

        return view('dashboard', $data);
    }

    private function resolveDateRange(Request $request): array
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        try {
            $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::today()->startOfDay();
        } catch (\Throwable $e) {
            $start = Carbon::today()->startOfDay();
        }

        try {
            $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::today()->endOfDay();
        } catch (\Throwable $e) {
            $end = Carbon::today()->endOfDay();
        }

        if ($start->greaterThan($end)) {
            $tmp = $start;
            $start = $end->copy()->startOfDay();
            $end = $tmp->copy()->endOfDay();
        }

        $maxDays = 90;
        $rangeDays = $start->diffInDays($end) + 1;
        if ($rangeDays > $maxDays) {
            $start = $end->copy()->subDays($maxDays - 1)->startOfDay();
        }

        return [$start, $end, $startDate, $endDate];
    }

    private function buildOrderScope(User $user, Carbon $start, Carbon $end)
    {
        $orderScope = Order::query()->with('product');

        if ($user->role === User::ROLE_MANAGER) {
            $orderScope->whereHas('product', function ($builder) use ($user): void {
                $builder->where('manager_id', $user->id);
            });
        }

        $orderScope->whereBetween('orders.created_at', [$start, $end]);

        return $orderScope;
    }

    private function buildBaseStats($orderScope, User $user, Carbon $start, Carbon $end, OrderStatus $delivered): array
    {
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

        return [
            'totalOrders' => $totalOrders,
            'deliveredOrders' => $deliveredOrders,
            'pendingOrders' => $pendingOrders,
            'cancelledOrders' => $cancelledOrders,
            'deliveredRevenue' => (int) $deliveredRevenue,
            'totalBenefit' => (int) $totalBenefit,
            'orderSeries' => $this->buildOrderSeries($user, $start, $end),
        ];
    }

    private function buildUserStats(): array
    {
        return [
            'totalUsers' => User::count(),
            'adminUsers' => User::where('role', User::ROLE_ADMIN)->count(),
            'managerUsers' => User::where('role', User::ROLE_MANAGER)->count(),
            'activeUsers' => User::where('is_active', true)->count(),
            'inactiveUsers' => User::where('is_active', false)->count(),
        ];
    }

    private function buildAppliedRangeLabel(?string $startDate, ?string $endDate, Carbon $start, Carbon $end): string
    {
        $rangeDays = $start->diffInDays($end) + 1;
        $today = Carbon::today();

        if (!$startDate && !$endDate) {
            return "Aujourd'hui";
        }

        if ($rangeDays === 1) {
            return 'Le ' . $start->format('d/m/Y');
        }

        if ($end->isSameDay($today) && in_array($rangeDays, [7, 30, 90], true)) {
            return "Derniers {$rangeDays} jours";
        }

        return $start->format('d/m/Y') . ' — ' . $end->format('d/m/Y');
    }

    private function buildAdminStats(Carbon $start, Carbon $end, OrderStatus $delivered): array
    {
        [$activeProducts, $totalProducts, $latestProducts] = $this->buildProductsOverview();

        return [
            'activeProducts' => $activeProducts,
            'totalProducts' => $totalProducts,
            'latestProducts' => $latestProducts,
            'topSold' => $this->buildTopSold($start, $end, $delivered),
            'topRevenue' => $this->buildTopRevenue($start, $end, $delivered),
            'topSellers' => $this->buildTopSellers($start, $end, $delivered),
        ];
    }

    private function buildProductsOverview(): array
    {
        $activeProducts = Product::query()->where('status', ProductStatus::Active)->count();
        $totalProducts = Product::query()->count();

        $latestProducts = Product::query()
            ->with(['productImages'])
            ->latest()
            ->take(6)
            ->get();

        return [$activeProducts, $totalProducts, $latestProducts];
    }

    private function buildTopSellers(Carbon $start, Carbon $end, OrderStatus $delivered)
    {
        $topSellersRows = Order::query()
            ->select(
                'products.manager_id',
                DB::raw('COUNT(orders.id) as total_sales'),
                DB::raw('SUM(products.price_sell * COALESCE(orders.quantity, 1)) as total_revenue'),
                DB::raw('SUM((products.price_sell - products.price_buy) * COALESCE(orders.quantity, 1)) as total_benefit')
            )
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->where('orders.status', $delivered)
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('products.manager_id')
            ->orderByDesc('total_sales')
            ->take(10)
            ->get();

        $managers = User::query()
            ->whereIn('id', $topSellersRows->pluck('manager_id'))
            ->get()
            ->keyBy('id');

        return $topSellersRows->map(function ($row) use ($managers) {
            return [
                'user' => $managers->get($row->manager_id),
                'total_sales' => (int) $row->total_sales,
                'total_revenue' => (int) $row->total_revenue,
                'total_benefit' => (int) $row->total_benefit,
            ];
        })->sortByDesc('total_sales')->values();
    }

    private function buildTopSold(Carbon $start, Carbon $end, OrderStatus $delivered)
    {
        $topSoldRows = Order::query()
            ->select('orders.product_id', DB::raw('SUM(COALESCE(orders.quantity, 1)) as sold_count'))
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->where('orders.status', $delivered)
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('orders.product_id')
            ->orderByDesc('sold_count')
            ->take(5)
            ->get();

        $topSoldProducts = Product::query()
            ->whereIn('id', $topSoldRows->pluck('product_id'))
            ->get()
            ->keyBy('id');

        return $topSoldRows->map(function ($row) use ($topSoldProducts) {
            return (object) [
                'product' => $topSoldProducts->get($row->product_id),
                'sold_count' => (int) $row->sold_count,
            ];
        });
    }

    private function buildTopRevenue(Carbon $start, Carbon $end, OrderStatus $delivered)
    {
        $topRevenueRows = Order::query()
            ->select(
                'orders.product_id',
                DB::raw('SUM(COALESCE(orders.quantity, 1)) as sold_count'),
                DB::raw('SUM(products.price_sell * COALESCE(orders.quantity, 1)) as revenue')
            )
            ->join('products', 'orders.product_id', '=', 'products.id')
            ->where('orders.status', $delivered)
            ->whereBetween('orders.created_at', [$start, $end])
            ->groupBy('orders.product_id')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        $productMap = Product::query()
            ->whereIn('id', $topRevenueRows->pluck('product_id'))
            ->get()
            ->keyBy('id');

        return $topRevenueRows->map(function ($row) use ($productMap) {
            return [
                'product' => $productMap->get($row->product_id),
                'revenue' => (int) $row->revenue,
                'sold_count' => (int) $row->sold_count,
            ];
        });
    }

    private function buildOrderSeries(User $user, Carbon $start, Carbon $end): array
    {
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

        $days = $start->diffInDays($end) + 1;

        for ($i = 0; $i < $days; $i++) {
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
