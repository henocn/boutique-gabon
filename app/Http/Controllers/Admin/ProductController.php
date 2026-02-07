<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->with(['category', 'manager', 'images'])
            ->latest()
            ->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $product = new Product();
        $categories = Category::query()->orderBy('name')->get();
        $managers = User::query()
            ->where('role', User::ROLE_MANAGER)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $statuses = ProductStatus::cases();

        return view('admin.products.create', compact('product', 'categories', 'managers', 'statuses'));
    }

    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $images = array_values(array_filter(
            $request->file('images', []),
            static fn ($file) => $file && $file->isValid()
        ));

        if (count($images) > 5) {
            return back()->withErrors(['images' => 'Maximum 5 images.'])->withInput();
        }

        DB::transaction(function () use ($data, $images): void {
            $product = Product::create($data);

            foreach ($images as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'position' => $index + 1,
                ]);
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produit cree.');
    }

    public function edit(Product $product): View
    {
        $product->load('images');
        $categories = Category::query()->orderBy('name')->get();
        $managers = User::query()
            ->where('role', User::ROLE_MANAGER)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $statuses = ProductStatus::cases();

        return view('admin.products.edit', compact('product', 'categories', 'managers', 'statuses'));
    }

    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        $removeImages = collect($request->input('remove_images', []))->map('intval');
        $newImages = array_values(array_filter(
            $request->file('images', []),
            static fn ($file) => $file && $file->isValid()
        ));

        $remainingCount = $product->images()
            ->whereNotIn('id', $removeImages->all())
            ->count();

        if ($remainingCount + count($newImages) > 5) {
            return back()->withErrors(['images' => 'Maximum 5 images.'])->withInput();
        }

        DB::transaction(function () use ($product, $data, $removeImages, $newImages): void {
            if ($removeImages->isNotEmpty()) {
                $imagesToRemove = $product->images()->whereIn('id', $removeImages)->get();
                foreach ($imagesToRemove as $image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }

            $product->update($data);

            $startPosition = $product->images()->max('position') ?? 0;
            foreach ($newImages as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'position' => $startPosition + $index + 1,
                ]);
            }
        });

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produit mis a jour.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->load('images');

        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produit supprime.');
    }
}
