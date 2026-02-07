<?php

namespace App\Http\Controllers\Public;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        $product->load(['productImages', 'category']);

        if ($product->status !== ProductStatus::Active) {
            abort(404);
        }

        return view('product', compact('product'));
    }
}
