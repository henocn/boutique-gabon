<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->latest()
            ->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $category = new Category();

        return view('admin.categories.create', compact('category'));
    }

    public function store(CategoryStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        Category::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Categorie creee.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryUpdateRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($category->image_path) {
                Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Categorie mise a jour.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('status', 'Categorie supprimee.');
    }
}
