@extends('management.layout')

@section('title', 'Ajouter un produit')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.css" rel="stylesheet">
@endpush

@section('content')
<h2 class="mb-4 text-center">Ajouter un nouveau produit</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('management.products.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Categorie</label>
                    <select class="form-select" name="category_id" required>
                        <option value="" disabled selected>Choisir</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nom du produit</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Prix d'achat</label>
                    <input type="number" class="form-control" name="purchase_price" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Livraison</label>
                    <input type="number" class="form-control" name="shipping_price">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Quantite</label>
                    <input type="number" class="form-control" name="quantity">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Assistants de vente</label>
                    <select class="form-select" name="manager_ids[]" multiple>
                        @foreach ($managers as $manager)
                            <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Prix de vente (Gabon)</label>
                    <input type="number" class="form-control" name="selling_price" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Image principale</label>
                    <input type="file" class="form-control" name="image" accept="image/*" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control summernote" name="description" rows="6"></textarea>
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
                <div class="col-md-4"><input type="file" class="form-control" name="carousel1" accept="image/*"></div>
                <div class="col-md-4"><input type="file" class="form-control" name="carousel2" accept="image/*"></div>
                <div class="col-md-4"><input type="file" class="form-control" name="carousel3" accept="image/*"></div>
                <div class="col-md-4"><input type="file" class="form-control" name="carousel4" accept="image/*"></div>
                <div class="col-md-4"><input type="file" class="form-control" name="carousel5" accept="image/*"></div>
            </div>
        </div>
    </div>

    <div class="card mb-4 collapse" id="characteristicsSection">
        <div class="card-header">Caracteristiques</div>
        <div class="card-body">
            <div id="characteristics-list"></div>
            <button type="button" class="btn btn-outline-secondary" id="addCharacteristic">Ajouter</button>
        </div>
    </div>

    <div class="card mb-4 collapse" id="videosSection">
        <div class="card-header">Videos</div>
        <div class="card-body">
            <div id="videos-list"></div>
            <button type="button" class="btn btn-outline-secondary" id="addVideo">Ajouter</button>
        </div>
    </div>

    <div class="card mb-4 collapse" id="packsSection">
        <div class="card-header">Packs</div>
        <div class="card-body">
            <div id="packs-list"></div>
            <button type="button" class="btn btn-outline-secondary" id="addPack">Ajouter</button>
        </div>
    </div>

    <button type="submit" class="btn btn-dark">Enregistrer le produit</button>
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
