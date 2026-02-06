<?php

namespace App\Http\Controllers\Public;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $products = Product::query()
            ->where('status', ProductStatus::Active)
            ->with(['images', 'category'])
            ->latest()
            ->take(12)
            ->get();

        return view('welcome', compact('categories', 'products'));
    }
}
