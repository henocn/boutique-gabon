<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function home()
    {
        $product = Product::query()->first();

        if (!$product) {
            return view('front.empty');
        }

        return redirect()->route('product.show', ['product' => $product->id]);
    }

    public function product(int $product)
    {
        $productItem = Product::with(['category', 'packs', 'characteristics', 'videos', 'countries'])->find($product);

        if (!$productItem) {
            abort(404);
        }

        $country = Country::query()->where('code', 'GAB')->first();
        $sellingPrice = 0;
        if ($country) {
            $priceRow = $productItem->countries->firstWhere('id', $country->id);
            $sellingPrice = $priceRow?->pivot?->selling_price ?? 0;
        }

        $categories = Category::query()->orderBy('name')->get();
        $suggestedCategories = $categories->reject(fn ($category) => $category->id === $productItem->category_id)->take(6);

        return view('front.product', [
            'product' => $productItem,
            'sellingPrice' => $sellingPrice,
            'country' => $country,
            'categories' => $categories,
            'suggestedCategories' => $suggestedCategories,
        ]);
    }
}
