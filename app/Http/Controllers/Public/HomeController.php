<?php

namespace App\Http\Controllers\Public;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $product = Product::where('status', ProductStatus::Active)
            ->with(['productImages'])
            ->inRandomOrder()
            ->first();

        if (!$product) {
            abort(404, 'Aucun produit disponible');
        }

        return view('welcome', compact('product'));
    }
}
