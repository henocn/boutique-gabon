<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Country;
use App\Models\Product;
use App\Models\ProductCharacteristic;
use App\Models\ProductPack;
use App\Models\ProductVideo;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'countries', 'managers'])->orderByDesc('id')->get();

        return view('management.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::query()->orderBy('name')->get();
        $managers = User::query()->where('role', 0)->where('is_active', true)->orderBy('name')->get();
        $country = Country::query()->where('code', 'GAB')->first();

        return view('management.products.create', compact('categories', 'managers', 'country'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'purchase_price' => ['required', 'integer', 'min:0'],
            'shipping_price' => ['nullable', 'integer', 'min:0'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['required', 'image', 'max:4096'],
            'carousel1' => ['nullable', 'image', 'max:4096'],
            'carousel2' => ['nullable', 'image', 'max:4096'],
            'carousel3' => ['nullable', 'image', 'max:4096'],
            'carousel4' => ['nullable', 'image', 'max:4096'],
            'carousel5' => ['nullable', 'image', 'max:4096'],
            'manager_ids' => ['nullable', 'array'],
            'manager_ids.*' => ['integer', 'exists:users,id'],
            'selling_price' => ['required', 'integer', 'min:0'],
            'characteristic_title' => ['nullable', 'array'],
            'characteristic_title.*' => ['nullable', 'string', 'max:100'],
            'characteristic_description' => ['nullable', 'array'],
            'characteristic_image' => ['nullable', 'array'],
            'characteristic_image.*' => ['nullable', 'image', 'max:4096'],
            'video_url' => ['nullable', 'array'],
            'video_url.*' => ['nullable', 'string', 'max:500'],
            'video_file' => ['nullable', 'array'],
            'video_file.*' => ['nullable', 'file', 'max:20480'],
            'pack_name' => ['nullable', 'array'],
            'pack_name.*' => ['nullable', 'string', 'max:255'],
            'pack_quantity' => ['nullable', 'array'],
            'pack_price' => ['nullable', 'array'],
            'pack_image' => ['nullable', 'array'],
            'pack_image.*' => ['nullable', 'image', 'max:4096'],
        ]);

        $product = Product::query()->create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'purchase_price' => $data['purchase_price'],
            'shipping_price' => $data['shipping_price'] ?? null,
            'quantity' => $data['quantity'] ?? null,
            'description' => $data['description'] ?? null,
            'image' => $this->storePublicFile($request->file('image'), 'uploads/main'),
            'carousel1' => $this->storeOptionalFile($request->file('carousel1'), 'uploads/carousel'),
            'carousel2' => $this->storeOptionalFile($request->file('carousel2'), 'uploads/carousel'),
            'carousel3' => $this->storeOptionalFile($request->file('carousel3'), 'uploads/carousel'),
            'carousel4' => $this->storeOptionalFile($request->file('carousel4'), 'uploads/carousel'),
            'carousel5' => $this->storeOptionalFile($request->file('carousel5'), 'uploads/carousel'),
            'status' => 1,
            'manager_id' => $data['manager_ids'][0] ?? null,
        ]);

        if (!empty($data['manager_ids'])) {
            $product->managers()->sync($data['manager_ids']);
        }

        $country = Country::query()->where('code', 'GAB')->first();
        if ($country) {
            $product->countries()->sync([
                $country->id => ['selling_price' => $data['selling_price']],
            ]);
        }

        $this->storeCharacteristics($request, $product);
        $this->storeVideos($request, $product);
        $this->storePacks($request, $product);

        return redirect()->route('management.products.index')->with('status', 'Produit ajoute.');
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'managers', 'countries', 'characteristics', 'videos', 'packs']);
        $categories = Category::query()->orderBy('name')->get();
        $managers = User::query()->where('role', 0)->where('is_active', true)->orderBy('name')->get();
        $country = Country::query()->where('code', 'GAB')->first();

        return view('management.products.edit', compact('product', 'categories', 'managers', 'country'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'purchase_price' => ['required', 'integer', 'min:0'],
            'shipping_price' => ['nullable', 'integer', 'min:0'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'carousel1' => ['nullable', 'image', 'max:4096'],
            'carousel2' => ['nullable', 'image', 'max:4096'],
            'carousel3' => ['nullable', 'image', 'max:4096'],
            'carousel4' => ['nullable', 'image', 'max:4096'],
            'carousel5' => ['nullable', 'image', 'max:4096'],
            'delete_main_image' => ['nullable', 'boolean'],
            'delete_carousel' => ['nullable', 'array'],
            'manager_ids' => ['nullable', 'array'],
            'manager_ids.*' => ['integer', 'exists:users,id'],
            'selling_price' => ['required', 'integer', 'min:0'],
            'existing_char_id' => ['nullable', 'array'],
            'existing_char_title' => ['nullable', 'array'],
            'existing_char_description' => ['nullable', 'array'],
            'existing_char_image_file' => ['nullable', 'array'],
            'existing_char_image_file.*' => ['nullable', 'image', 'max:4096'],
            'delete_characteristic' => ['nullable', 'array'],
            'delete_char_image' => ['nullable', 'array'],
            'existing_video_id' => ['nullable', 'array'],
            'existing_video_url' => ['nullable', 'array'],
            'existing_video_file' => ['nullable', 'array'],
            'existing_video_file.*' => ['nullable', 'file', 'max:20480'],
            'delete_video' => ['nullable', 'array'],
            'delete_video_file' => ['nullable', 'array'],
            'existing_pack_id' => ['nullable', 'array'],
            'existing_pack_name' => ['nullable', 'array'],
            'existing_pack_quantity' => ['nullable', 'array'],
            'existing_pack_price' => ['nullable', 'array'],
            'existing_pack_image_file' => ['nullable', 'array'],
            'existing_pack_image_file.*' => ['nullable', 'image', 'max:4096'],
            'delete_pack' => ['nullable', 'array'],
            'delete_pack_image' => ['nullable', 'array'],
            'characteristic_title' => ['nullable', 'array'],
            'characteristic_title.*' => ['nullable', 'string', 'max:100'],
            'characteristic_description' => ['nullable', 'array'],
            'characteristic_image' => ['nullable', 'array'],
            'characteristic_image.*' => ['nullable', 'image', 'max:4096'],
            'video_url' => ['nullable', 'array'],
            'video_url.*' => ['nullable', 'string', 'max:500'],
            'video_file' => ['nullable', 'array'],
            'video_file.*' => ['nullable', 'file', 'max:20480'],
            'pack_name' => ['nullable', 'array'],
            'pack_name.*' => ['nullable', 'string', 'max:255'],
            'pack_quantity' => ['nullable', 'array'],
            'pack_price' => ['nullable', 'array'],
            'pack_image' => ['nullable', 'array'],
            'pack_image.*' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->boolean('delete_main_image') && $product->image) {
            $this->deletePublicFile('uploads/main', $product->image);
            $product->image = '';
        }

        if ($request->hasFile('image')) {
            $this->deletePublicFile('uploads/main', $product->image);
            $product->image = $this->storePublicFile($request->file('image'), 'uploads/main');
        }

        $product->fill([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'purchase_price' => $data['purchase_price'],
            'shipping_price' => $data['shipping_price'] ?? null,
            'quantity' => $data['quantity'] ?? null,
            'description' => $data['description'] ?? null,
            'manager_id' => $data['manager_ids'][0] ?? null,
        ]);

        $this->updateCarouselImage($request, $product, 'carousel1');
        $this->updateCarouselImage($request, $product, 'carousel2');
        $this->updateCarouselImage($request, $product, 'carousel3');
        $this->updateCarouselImage($request, $product, 'carousel4');
        $this->updateCarouselImage($request, $product, 'carousel5');

        $product->save();

        $product->managers()->sync($data['manager_ids'] ?? []);

        $country = Country::query()->where('code', 'GAB')->first();
        if ($country) {
            $product->countries()->sync([
                $country->id => ['selling_price' => $data['selling_price']],
            ]);
        }

        $this->updateCharacteristics($request, $product);
        $this->updateVideos($request, $product);
        $this->updatePacks($request, $product);
        $this->storeCharacteristics($request, $product);
        $this->storeVideos($request, $product);
        $this->storePacks($request, $product);

        return redirect()->route('management.products.index')->with('status', 'Produit mis a jour.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return back()->with('status', 'Produit supprime.');
    }


    private function storePublicFile($file, string $dir): string
    {
        $name = time() . '_' . Str::random(6) . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $ext = $file->getClientOriginalExtension();
        $fileName = $name . '.' . $ext;

        $file->move(public_path($dir), $fileName);

        return $fileName;
    }

    private function storeOptionalFile($file, string $dir): ?string
    {
        if (!$file) {
            return null;
        }

        return $this->storePublicFile($file, $dir);
    }

    private function deletePublicFile(string $dir, ?string $fileName): void
    {
        if (!$fileName) {
            return;
        }

        $path = public_path(trim($dir, '/') . '/' . $fileName);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    private function updateCarouselImage(Request $request, Product $product, string $field): void
    {
        $deleteSlots = $request->input('delete_carousel', []);
        if (in_array($field, $deleteSlots, true)) {
            $this->deletePublicFile('uploads/carousel', $product->{$field});
            $product->{$field} = null;
        }

        if ($request->hasFile($field)) {
            $this->deletePublicFile('uploads/carousel', $product->{$field});
            $product->{$field} = $this->storePublicFile($request->file($field), 'uploads/carousel');
        }
    }

    private function updateCharacteristics(Request $request, Product $product): void
    {
        $ids = $request->input('existing_char_id', []);
        $titles = $request->input('existing_char_title', []);
        $descriptions = $request->input('existing_char_description', []);
        $deleteIds = array_map('intval', $request->input('delete_characteristic', []));
        $deleteImages = array_map('intval', $request->input('delete_char_image', []));
        $files = $request->file('existing_char_image_file', []);

        foreach ($ids as $index => $id) {
            $characteristic = ProductCharacteristic::query()
                ->where('product_id', $product->id)
                ->find($id);

            if (!$characteristic) {
                continue;
            }

            if (in_array((int)$id, $deleteIds, true)) {
                $this->deletePublicFile('uploads/characteristics', $characteristic->image);
                $characteristic->delete();
                continue;
            }

            $characteristic->title = $titles[$index] ?? $characteristic->title;
            $characteristic->description = $descriptions[$index] ?? $characteristic->description;

            if (in_array((int)$id, $deleteImages, true)) {
                $this->deletePublicFile('uploads/characteristics', $characteristic->image);
                $characteristic->image = null;
            }

            if (!empty($files[$index])) {
                $this->deletePublicFile('uploads/characteristics', $characteristic->image);
                $characteristic->image = $this->storePublicFile($files[$index], 'uploads/characteristics');
            }

            $characteristic->save();
        }
    }

    private function updateVideos(Request $request, Product $product): void
    {
        $ids = $request->input('existing_video_id', []);
        $urls = $request->input('existing_video_url', []);
        $files = $request->file('existing_video_file', []);
        $deleteIds = array_map('intval', $request->input('delete_video', []));
        $deleteFiles = array_map('intval', $request->input('delete_video_file', []));

        foreach ($ids as $index => $id) {
            $video = ProductVideo::query()
                ->where('product_id', $product->id)
                ->find($id);

            if (!$video) {
                continue;
            }

            if (in_array((int)$id, $deleteIds, true)) {
                $this->deleteIfLocalVideo($video->video_url);
                $video->delete();
                continue;
            }

            if (in_array((int)$id, $deleteFiles, true)) {
                $this->deleteIfLocalVideo($video->video_url);
                $video->video_url = null;
            }

            if (!empty($files[$index])) {
                $this->deleteIfLocalVideo($video->video_url);
                $video->video_url = $this->storePublicFile($files[$index], 'uploads/videos');
            }

            $newUrl = $urls[$index] ?? null;
            if ($newUrl) {
                if ($video->video_url && !filter_var($video->video_url, FILTER_VALIDATE_URL) && $video->video_url !== $newUrl) {
                    $this->deletePublicFile('uploads/videos', $video->video_url);
                }
                $video->video_url = $newUrl;
            }

            $video->save();
        }
    }

    private function updatePacks(Request $request, Product $product): void
    {
        $ids = $request->input('existing_pack_id', []);
        $names = $request->input('existing_pack_name', []);
        $quantities = $request->input('existing_pack_quantity', []);
        $prices = $request->input('existing_pack_price', []);
        $files = $request->file('existing_pack_image_file', []);
        $deleteIds = array_map('intval', $request->input('delete_pack', []));
        $deleteImages = array_map('intval', $request->input('delete_pack_image', []));

        foreach ($ids as $index => $id) {
            $pack = ProductPack::query()
                ->where('product_id', $product->id)
                ->find($id);

            if (!$pack) {
                continue;
            }

            if (in_array((int)$id, $deleteIds, true)) {
                $this->deletePublicFile('uploads/packs', $pack->image);
                $pack->delete();
                continue;
            }

            $pack->name = $names[$index] ?? $pack->name;
            $pack->quantity = (int)($quantities[$index] ?? $pack->quantity ?? 0);
            $pack->price = (int)($prices[$index] ?? $pack->price ?? 0);

            if (in_array((int)$id, $deleteImages, true)) {
                $this->deletePublicFile('uploads/packs', $pack->image);
                $pack->image = '';
            }

            if (!empty($files[$index])) {
                $this->deletePublicFile('uploads/packs', $pack->image);
                $pack->image = $this->storePublicFile($files[$index], 'uploads/packs');
            }

            $pack->save();
        }
    }

    private function deleteIfLocalVideo(?string $value): void
    {
        if (!$value) {
            return;
        }

        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return;
        }

        $this->deletePublicFile('uploads/videos', $value);
    }

    private function storeCharacteristics(Request $request, Product $product): void
    {
        $titles = $request->input('characteristic_title', []);
        $descriptions = $request->input('characteristic_description', []);
        $images = $request->file('characteristic_image', []);

        foreach ($titles as $index => $title) {
            if (!$title) {
                continue;
            }

            ProductCharacteristic::query()->create([
                'product_id' => $product->id,
                'title' => $title,
                'description' => $descriptions[$index] ?? null,
                'image' => $this->storeOptionalFile($images[$index] ?? null, 'uploads/characteristics'),
            ]);
        }
    }

    private function storeVideos(Request $request, Product $product): void
    {
        $urls = $request->input('video_url', []);
        $files = $request->file('video_file', []);

        foreach ($urls as $index => $url) {
            if (!$url && empty($files[$index])) {
                continue;
            }

            $storedFile = null;
            if (!empty($files[$index])) {
                $storedFile = $this->storePublicFile($files[$index], 'uploads/videos');
            }

            ProductVideo::query()->create([
                'product_id' => $product->id,
                'video_url' => $storedFile ?? $url,
                'texte' => null,
            ]);
        }
    }

    private function storePacks(Request $request, Product $product): void
    {
        $names = $request->input('pack_name', []);
        $quantities = $request->input('pack_quantity', []);
        $prices = $request->input('pack_price', []);
        $images = $request->file('pack_image', []);

        foreach ($names as $index => $name) {
            if (!$name) {
                continue;
            }

            ProductPack::query()->create([
                'product_id' => $product->id,
                'name' => $name,
                'image' => $this->storeOptionalFile($images[$index] ?? null, 'uploads/packs') ?? '',
                'quantity' => (int)($quantities[$index] ?? 0),
                'price' => (int)($prices[$index] ?? 0),
            ]);
        }
    }
}
