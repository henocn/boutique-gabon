<?php

namespace App\Http\Controllers\Public;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $productsQuery = Product::query()
            ->where('status', ProductStatus::Active)
            ->with(['productImages', 'category']);

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $productsQuery->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description_html', 'like', "%{$search}%");
            });
        }

        $selectedCategory = $request->query('category');
        if ($selectedCategory) {
            $productsQuery->where('category_id', $selectedCategory);
        }

        $products = $productsQuery->latest()->get();

        return view('welcome', compact('categories', 'products', 'search', 'selectedCategory'));
    }
}
