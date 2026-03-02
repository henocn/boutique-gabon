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
        <!-- description moved to bottom for better Summernote integration -->
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
            <div class="col-md-4">
                <label class="form-label d-block">Pays</label>
                <div class="d-flex flex-column gap-2">
                    @foreach (\App\Enums\Country::cases() as $country)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="countries[]" value="{{ $country->value }}" id="country_{{ $country->value }}" @checked(in_array($country->value, old('countries', $product->countries ?? [])))>
                            <label class="form-check-label" for="country_{{ $country->value }}">
                                {{ $country->label() }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <small class="text-muted d-block mt-2">Sélectionnez au moins un pays</small>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
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
        @if ($product->exists && $product->productImages?->isNotEmpty())
            <div class="mb-3">
                <label class="form-label">Images existantes</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($product->productImages as $image)
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

<div class="mb-3">
    <label class="form-label" for="description_html">Description</label>
    <textarea id="description_html" name="description_html" class="form-control">{{ old('description_html', $product->description_html) }}</textarea>
    <small class="text-muted">Utilisez l'éditeur ci-dessus pour formater la description (images, tableaux, liens).</small>
</div>

<div class="d-flex gap-2 mt-3">
    <button class="btn btn-brand" type="submit">{{ $submitLabel }}</button>
    <a class="btn btn-outline-secondary" href="{{ route('admin.products.index') }}">Annuler</a>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css">
<style>
    /* ── Summernote integration fixes ─────────────────────────── */
    .note-editor.note-frame { width: 100%; }
    .note-editor.note-frame .note-editable { min-height: 300px; }

    /* Keep dropdowns and modals above other UI while preserving layout flow */
    .note-editor .dropdown-menu { z-index: 1061; }
    .note-modal { z-index: 1062; }
    .note-modal-backdrop { z-index: 1061; }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
<script>
    (function () {
        var input = document.getElementById('images');
        var container = document.getElementById('selected-images');
        var list = document.getElementById('selected-images-list');

        if (input && container && list) {
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
        }

        // Initialize Summernote on description textarea
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && jQuery().summernote) {
                jQuery('#description_html').summernote({
                    height: 300,
                    dialogsInBody: true,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture', 'video', 'table']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    callbacks: {
                        onImageUpload: function(files) {
                            var $textarea = jQuery(this);
                            Array.from(files).forEach(function(file) {
                                var reader = new FileReader();
                                reader.onload = function(e) {
                                    var imgNode = jQuery('<img>').attr('src', e.target.result);
                                    $textarea.summernote('insertNode', imgNode[0]);
                                };
                                reader.readAsDataURL(file);
                            });
                        }
                    }
                });
            }
        });
    })();
</script>
@endpush
