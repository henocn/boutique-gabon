@extends('management.layout')

@section('title', 'Modifier un produit')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.css" rel="stylesheet">
@endpush

@section('content')
<h2 class="mb-4 text-center">Modifier le produit</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('management.products.update', $product) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Categorie</label>
                    <select class="form-select" name="category_id" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($category->id === $product->category_id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nom du produit</label>
                    <input type="text" class="form-control" name="name" value="{{ $product->name }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Prix d'achat</label>
                    <input type="number" class="form-control" name="purchase_price" value="{{ $product->purchase_price }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Livraison</label>
                    <input type="number" class="form-control" name="shipping_price" value="{{ $product->shipping_price }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Quantite</label>
                    <input type="number" class="form-control" name="quantity" value="{{ $product->quantity }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Assistants de vente</label>
                    <select class="form-select" name="manager_ids[]" multiple>
                        @foreach ($managers as $manager)
                            <option value="{{ $manager->id }}" @selected($product->managers->contains($manager))>{{ $manager->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Prix de vente (Gabon)</label>
                    <input type="number" class="form-control" name="selling_price" value="{{ $product->countries->first()?->pivot?->selling_price ?? 0 }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Image principale</label>
                    @if ($product->image)
                        <div class="mb-2">
                            <img src="{{ asset('uploads/main/' . $product->image) }}" class="img-thumbnail" style="max-width:140px;" alt="{{ $product->name }}">
                        </div>
                    @endif
                    <input type="file" class="form-control" name="image" accept="image/*">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="delete_main_image" value="1" id="deleteMainImage">
                        <label class="form-check-label" for="deleteMainImage">Supprimer l'image principale</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control summernote" name="description" rows="6">{{ $product->description }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-3">
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#characteristicsSection">
            Caracteristiques
        </button>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#videosSection">
            Videos
        </button>
        <button type="button" class="btn btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#packsSection">
            Packs
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-header">Carousel</div>
        <div class="card-body">
            <div class="row g-3">
                @foreach (['carousel1', 'carousel2', 'carousel3', 'carousel4', 'carousel5'] as $slot)
                    <div class="col-md-4">
                        @if ($product->{$slot})
                            <img src="{{ asset('uploads/carousel/' . $product->{$slot}) }}" class="img-thumbnail mb-2" style="max-width:140px;" alt="{{ $product->name }}">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="delete_carousel[]" value="{{ $slot }}" id="delete_{{ $slot }}">
                                <label class="form-check-label" for="delete_{{ $slot }}">Supprimer</label>
                            </div>
                        @endif
                        <input type="file" class="form-control" name="{{ $slot }}" accept="image/*">
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mb-4 collapse" id="characteristicsSection">
        <div class="card-header">Caracteristiques</div>
        <div class="card-body">
            @foreach ($product->characteristics as $index => $char)
                <div class="border rounded p-3 mb-3">
                    <input type="hidden" name="existing_char_id[]" value="{{ $char->id }}">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="existing_char_title[]" value="{{ $char->title }}" placeholder="Titre">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="existing_char_description[]" value="{{ $char->description }}" placeholder="Description">
                        </div>
                        <div class="col-md-4">
                            <input type="file" class="form-control" name="existing_char_image_file[]" accept="image/*">
                        </div>
                    </div>
                    @if ($char->image)
                        <div class="mt-2">
                            <img src="{{ asset('uploads/characteristics/' . $char->image) }}" class="img-thumbnail" style="max-width:120px;" alt="{{ $char->title }}">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="delete_char_image[]" value="{{ $char->id }}" id="delete_char_image_{{ $char->id }}">
                                <label class="form-check-label" for="delete_char_image_{{ $char->id }}">Supprimer l'image</label>
                            </div>
                        </div>
                    @endif
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="delete_characteristic[]" value="{{ $char->id }}" id="delete_char_{{ $char->id }}">
                        <label class="form-check-label" for="delete_char_{{ $char->id }}">Supprimer la caracteristique</label>
                    </div>
                </div>
            @endforeach
            <div id="characteristics-list"></div>
            <button type="button" class="btn btn-outline-secondary" id="addCharacteristic">Ajouter</button>
        </div>
    </div>

    <div class="card mb-4 collapse" id="videosSection">
        <div class="card-header">Videos</div>
        <div class="card-body">
            @foreach ($product->videos as $video)
                <div class="border rounded p-3 mb-3">
                    <input type="hidden" name="existing_video_id[]" value="{{ $video->id }}">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="existing_video_url[]" value="{{ $video->video_url }}" placeholder="URL video">
                        </div>
                        <div class="col-md-6">
                            <input type="file" class="form-control" name="existing_video_file[]" accept="video/*">
                        </div>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="delete_video_file[]" value="{{ $video->id }}" id="delete_video_file_{{ $video->id }}">
                        <label class="form-check-label" for="delete_video_file_{{ $video->id }}">Supprimer le fichier</label>
                    </div>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="delete_video[]" value="{{ $video->id }}" id="delete_video_{{ $video->id }}">
                        <label class="form-check-label" for="delete_video_{{ $video->id }}">Supprimer la video</label>
                    </div>
                </div>
            @endforeach
            <div id="videos-list"></div>
            <button type="button" class="btn btn-outline-secondary" id="addVideo">Ajouter</button>
        </div>
    </div>

    <div class="card mb-4 collapse" id="packsSection">
        <div class="card-header">Packs</div>
        <div class="card-body">
            @foreach ($product->packs as $pack)
                <div class="border rounded p-3 mb-3">
                    <input type="hidden" name="existing_pack_id[]" value="{{ $pack->id }}">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="existing_pack_name[]" value="{{ $pack->name }}" placeholder="Nom">
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="existing_pack_quantity[]" value="{{ $pack->quantity }}" placeholder="Quantite">
                        </div>
                        <div class="col-md-3">
                            <input type="number" class="form-control" name="existing_pack_price[]" value="{{ $pack->price }}" placeholder="Prix">
                        </div>
                        <div class="col-md-3">
                            <input type="file" class="form-control" name="existing_pack_image_file[]" accept="image/*">
                        </div>
                    </div>
                    @if ($pack->image)
                        <div class="mt-2">
                            <img src="{{ asset('uploads/packs/' . $pack->image) }}" class="img-thumbnail" style="max-width:120px;" alt="{{ $pack->name }}">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="delete_pack_image[]" value="{{ $pack->id }}" id="delete_pack_image_{{ $pack->id }}">
                                <label class="form-check-label" for="delete_pack_image_{{ $pack->id }}">Supprimer l'image</label>
                            </div>
                        </div>
                    @endif
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="delete_pack[]" value="{{ $pack->id }}" id="delete_pack_{{ $pack->id }}">
                        <label class="form-check-label" for="delete_pack_{{ $pack->id }}">Supprimer le pack</label>
                    </div>
                </div>
            @endforeach
            <div id="packs-list"></div>
            <button type="button" class="btn btn-outline-secondary" id="addPack">Ajouter</button>
        </div>
    </div>

    <button type="submit" class="btn btn-dark">Mettre a jour</button>
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.js"></script>
<script>
    $(document).ready(function () {
        $('.summernote').summernote({
            height: 260,
            tabsize: 2,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });

    const addCharacteristic = document.getElementById('addCharacteristic');
    const characteristicsList = document.getElementById('characteristics-list');
    addCharacteristic.addEventListener('click', () => {
        const wrapper = document.createElement('div');
        wrapper.className = 'dynamic-row row g-2 mb-3 align-items-end';
        wrapper.innerHTML = `
            <div class="col-md-4"><input type="text" class="form-control" name="characteristic_title[]" placeholder="Titre"></div>
            <div class="col-md-4"><input type="file" class="form-control" name="characteristic_image[]" accept="image/*"></div>
            <div class="col-md-4"><input type="text" class="form-control" name="characteristic_description[]" placeholder="Description"></div>
            <div class="col-12 d-flex justify-content-end">
                <button type="button" class="btn-remove-row" onclick="this.closest('.dynamic-row').remove()">Supprimer</button>
            </div>
        `;
        characteristicsList.appendChild(wrapper);
    });

    const addVideo = document.getElementById('addVideo');
    const videosList = document.getElementById('videos-list');
    addVideo.addEventListener('click', () => {
        const wrapper = document.createElement('div');
        wrapper.className = 'dynamic-row row g-2 mb-3 align-items-end';
        wrapper.innerHTML = `
            <div class="col-md-6"><input type="text" class="form-control" name="video_url[]" placeholder="URL video"></div>
            <div class="col-md-6"><input type="file" class="form-control" name="video_file[]" accept="video/*"></div>
            <div class="col-12 d-flex justify-content-end">
                <button type="button" class="btn-remove-row" onclick="this.closest('.dynamic-row').remove()">Supprimer</button>
            </div>
        `;
        videosList.appendChild(wrapper);
    });

    const addPack = document.getElementById('addPack');
    const packsList = document.getElementById('packs-list');
    addPack.addEventListener('click', () => {
        const wrapper = document.createElement('div');
        wrapper.className = 'dynamic-row row g-2 mb-3 align-items-end';
        wrapper.innerHTML = `
            <div class="col-md-3"><input type="text" class="form-control" name="pack_name[]" placeholder="Nom"></div>
            <div class="col-md-3"><input type="number" class="form-control" name="pack_quantity[]" placeholder="Quantite"></div>
            <div class="col-md-3"><input type="number" class="form-control" name="pack_price[]" placeholder="Prix"></div>
            <div class="col-md-3"><input type="file" class="form-control" name="pack_image[]" accept="image/*"></div>
            <div class="col-12 d-flex justify-content-end">
                <button type="button" class="btn-remove-row" onclick="this.closest('.dynamic-row').remove()">Supprimer</button>
            </div>
        `;
        packsList.appendChild(wrapper);
    });

</script>
@endpush
