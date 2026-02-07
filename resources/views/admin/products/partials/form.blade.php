@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-lg-8">
        <div class="mb-3">
            <label class="form-label" for="name">Nom</label>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $product->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="description_html">Description</label>
            <textarea id="description_html" name="description_html" class="form-control" rows="6">{{ old('description_html', $product->description_html) }}</textarea>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="price_buy">Prix achat</label>
                <input id="price_buy" name="price_buy" type="number" step="1" min="0" class="form-control" value="{{ old('price_buy', $product->price_buy) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="price_sell">Prix vente</label>
                <input id="price_sell" name="price_sell" type="number" step="1" min="0" class="form-control" value="{{ old('price_sell', $product->price_sell) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="price_shipping">Livraison</label>
                <input id="price_shipping" name="price_shipping" type="number" step="1" min="0" class="form-control" value="{{ old('price_shipping', $product->price_shipping) }}" required>
            </div>
        </div>
        <div class="row g-3 mt-1">
            <div class="col-md-4">
                <label class="form-label" for="stock">Stock</label>
                <input id="stock" name="stock" type="number" class="form-control" value="{{ old('stock', $product->stock) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="status">Statut</label>
                <select id="status" name="status" class="form-select" required>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(old('status', $product->status?->value) === $status->value)>
                            {{ ucfirst(str_replace('_', ' ', $status->value)) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="mb-3">
            <label class="form-label" for="category_id">Categorie</label>
            <select id="category_id" name="category_id" class="form-select" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label" for="manager_id">Manager</label>
            <select id="manager_id" name="manager_id" class="form-select" required>
                @foreach ($managers as $manager)
                    <option value="{{ $manager->id }}" @selected(old('manager_id', $product->manager_id) == $manager->id)>
                        {{ $manager->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label" for="images">Images (max 5)</label>
            <input id="images" name="images[]" type="file" class="form-control" accept="image/*" multiple>
        </div>
        <div class="mb-3 d-none" id="selected-images">
            <label class="form-label">Apercu des images</label>
            <div class="d-flex flex-wrap gap-2" id="selected-images-list"></div>
        </div>
        @if ($product->exists && $product->images?->isNotEmpty())
            <div class="mb-3">
                <label class="form-label">Images existantes</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($product->images as $image)
                        <div class="position-relative border rounded p-2">
                            <input class="btn-check" type="checkbox" name="remove_images[]" value="{{ $image->id }}" id="remove_image_{{ $image->id }}">
                            <label class="btn btn-sm btn-danger position-absolute top-0 start-0 m-1" for="remove_image_{{ $image->id }}" title="Supprimer">
                                <i class="bi bi-x"></i>
                            </label>
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($image->path) }}" alt="Image" width="64" height="64" class="rounded">
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button class="btn btn-brand" type="submit">{{ $submitLabel }}</button>
    <a class="btn btn-outline-secondary" href="{{ route('admin.products.index') }}">Annuler</a>
</div>

@push('scripts')
<script>
    (function () {
        var input = document.getElementById('images');
        var container = document.getElementById('selected-images');
        var list = document.getElementById('selected-images-list');

        if (!input || !container || !list) {
            return;
        }

        input.addEventListener('change', function () {
            list.innerHTML = '';

            if (!input.files || input.files.length === 0) {
                container.classList.add('d-none');
                return;
            }

            Array.from(input.files).forEach(function (file) {
                var url = URL.createObjectURL(file);
                var img = document.createElement('img');
                img.src = url;
                img.width = 72;
                img.height = 72;
                img.className = 'rounded border';
                img.onload = function () {
                    URL.revokeObjectURL(url);
                };
                list.appendChild(img);
            });

            container.classList.remove('d-none');
        });
    })();
</script>
@endpush
