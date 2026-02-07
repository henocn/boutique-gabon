@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-3">
    <label class="form-label" for="name">Nom</label>
    <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $category->name) }}" required>
</div>

<div class="mb-3">
    <label class="form-label" for="description">Description</label>
    <textarea id="description" name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label" for="image">Image</label>
    <input id="image" name="image" type="file" class="form-control" accept="image/*">
</div>

<div class="form-check mb-3">
    <input id="is_active" name="is_active" type="checkbox" class="form-check-input" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Actif</label>
</div>

<div class="d-flex gap-2">
    <button class="btn btn-brand" type="submit" aria-label="{{ $submitLabel }}">
        <i class="bi bi-check-lg"></i>
        <span class="visually-hidden">{{ $submitLabel }}</span>
    </button>
    @if (! ($compactActions ?? false))
        <a class="btn btn-outline-secondary" href="{{ route('admin.categories.index') }}" aria-label="Annuler">
            <i class="bi bi-x-lg"></i>
            <span class="visually-hidden">Annuler</span>
        </a>
    @endif
</div>
