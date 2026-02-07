<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Boutique Gabon') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        @php
            $cartCount = array_sum(session('cart', []));
        @endphp
        <nav class="navbar navbar-expand-lg bg-white border-bottom navbar-client fixed-top">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/">Boutique Gabon</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <form class="d-none d-lg-flex align-items-center gap-2 ms-lg-4 me-lg-auto" method="GET" action="/">
                        <input class="form-control form-control-sm navbar-search" type="search" name="q" value="{{ $search }}" placeholder="Rechercher un produit">
                        @if ($selectedCategory)
                            <input type="hidden" name="category" value="{{ $selectedCategory }}">
                        @endif
                        <button class="btn btn-sm btn-brand" type="submit" aria-label="Rechercher">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                    <ul class="navbar-nav align-items-lg-center gap-lg-3">
                        <li class="nav-item"><a class="nav-link" href="#products">Produits</a></li>
                        <li class="nav-item"><a class="nav-link" href="#categories">Categories</a></li>
                        <li class="nav-item">
                            <a class="btn btn-brand position-relative" href="{{ route('cart.index') }}">
                                Mon panier
                                @if ($cartCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-brand">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>
                <button class="btn btn-sm btn-brand d-lg-none" type="button" data-bs-toggle="modal" data-bs-target="#searchModal" aria-label="Rechercher">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </nav>

        <section id="products" class="py-5 bg-white">
            <div class="container">
                <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-3">
                    <h2 class="h3 fw-bold mb-1">Produits</h2>
                    <form class="d-flex align-items-center gap-2 flex-nowrap filter-row" method="GET" action="/">
                        @if ($search)
                            <input type="hidden" name="q" value="{{ $search }}">
                        @endif
                        <select class="form-select" name="category">
                            <option value="">Toutes categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) $selectedCategory === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-brand" type="submit">Filtrer</button>
                    </form>
                    <div>
                        <p class="text-muted mb-0">Parcourez la liste de nos produits, trouvez rapidement ce qui vous plait et passez votre commande.</p>
                    </div>
                </div>
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                <div class="row g-3">
                    @forelse ($products as $product)
                        @php
                            $firstImage = $product->productImages->first();
                        @endphp
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="card product-card h-100">
                                <div class="ratio ratio-4x3 bg-light overflow-hidden">
                                    @if ($firstImage)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($firstImage->path) }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
                                    @endif
                                </div>
                                <div class="product-body">
                                    <a class="stretched-link text-decoration-none text-reset" href="{{ route('products.show', $product) }}"></a>
                                    <p class="fw-semibold mb-1">{{ $product->name }}</p>
                                    <p class="text-muted small product-desc mb-2">{{ strip_tags($product->description_html ?? '') }}</p>
                                    <div class="d-flex align-items-center justify-content-between product-actions">
                                        <span class="fw-bold">{{ number_format($product->price_sell, 0, ',', ' ') }} FCFA</span>
                                        <button class="btn btn-brand btn-cart" type="button" data-bs-toggle="modal" data-bs-target="#orderModal" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" aria-label="Ajouter au panier">
                                            <i class="bi bi-cart"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light">Aucun produit pour le moment.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section id="categories" class="py-5">
            <div class="container">
                <div class="d-flex align-items-end justify-content-between mb-3">
                    <h2 class="h3 fw-bold mb-0">Categories</h2>
                    <a class="text-brand fw-semibold" href="#products">Voir les produits</a>
                </div>
                <div class="row g-3">
                    @forelse ($categories as $category)
                        @php
                            $categoryUrl = url('/').'?category='.$category->id.($search ? '&q='.urlencode($search) : '');
                        @endphp
                        <div class="col-6 col-lg-3">
                            <a class="card card-soft p-3 text-decoration-none text-reset" href="{{ $categoryUrl }}">
                                <div class="ratio ratio-4x3 bg-light rounded-4 overflow-hidden">
                                    @if ($category->image_path)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($category->image_path) }}" alt="{{ $category->name }}" class="w-100 h-100 object-fit-cover">
                                    @endif
                                </div>
                                <div class="mt-3">
                                    <p class="fw-semibold mb-1">{{ $category->name }}</p>
                                    <p class="text-muted small mb-0">{{ \Illuminate\Support\Str::limit($category->description, 60) }}</p>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-light">Aucune categorie pour le moment.</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('cart.add') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="orderModalLabel">Ajouter au panier</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="product_id" id="orderProductId">
                            <p class="fw-semibold mb-3" id="orderProductName"></p>
                            <div class="mb-3">
                                <label class="form-label" for="orderQuantity">Quantite</label>
                                <input id="orderQuantity" name="quantity" type="number" min="1" max="99" value="1" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-brand">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="searchModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="GET" action="/">
                        <div class="modal-header">
                            <h5 class="modal-title">Rechercher</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                        </div>
                        <div class="modal-body">
                            <input class="form-control" type="search" name="q" value="{{ $search }}" placeholder="Rechercher un produit">
                            @if ($selectedCategory)
                                <input type="hidden" name="category" value="{{ $selectedCategory }}">
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-brand">
                                <i class="bi bi-search"></i>
                                Rechercher
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <footer class="py-4 border-top bg-white">
            <div class="container d-flex flex-wrap justify-content-between align-items-center">
                <p class="mb-0 text-muted">{{ date('Y') }} Boutique Gabon. Tous droits reserves.</p>
                <span class="text-muted">Support: +241 00 00 00 00</span>
            </div>
        </footer>
        <script>
            (function () {
                var modal = document.getElementById('orderModal');
                if (!modal) {
                    return;
                }
                modal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var productId = button.getAttribute('data-product-id');
                    var productName = button.getAttribute('data-product-name');
                    document.getElementById('orderProductId').value = productId;
                    document.getElementById('orderProductName').textContent = productName;
                    document.getElementById('orderQuantity').value = 1;
                });
            })();
        </script>
    </body>
</html>
