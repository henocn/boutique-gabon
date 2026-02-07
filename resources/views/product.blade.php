<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $product->name }} - {{ config('app.name', 'Boutique Gabon') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <nav class="navbar navbar-expand-lg bg-white border-bottom navbar-client fixed-top">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/">Boutique Gabon</a>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <a class="btn btn-outline-secondary" href="/">Retour</a>
                    <a class="btn btn-brand position-relative" href="{{ route('cart.index') }}">
                        Mon panier
                        @php($cartCount = array_sum(session('cart', [])))
                        @if ($cartCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-brand">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </nav>

        <main class="py-5">
            <div class="container">
                @php($images = $product->productImages)
                <div class="product-detail card-soft p-4">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="ratio ratio-4x3 bg-light border product-hero-image rounded-3 overflow-hidden">
                                @php($firstImage = $images->first())
                                @if ($firstImage)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($firstImage->path) }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="d-flex align-items-center justify-content-center text-muted">Aucune image</div>
                                @endif
                            </div>
                            @if ($images->count() > 1)
                                <div class="d-flex gap-2 mt-3 flex-wrap">
                                    @foreach ($images->slice(1) as $image)
                                        <div class="product-thumb border rounded-3 overflow-hidden">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($image->path) }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-6">
                            <div class="text-muted small mb-2">{{ $product->category?->name }}</div>
                            <h1 class="h3 fw-bold mb-2">{{ $product->name }}</h1>
                            <div class="text-muted product-detail-summary mb-3">{{ strip_tags($product->description_html ?? '') }}</div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="h4 fw-bold mb-0">{{ number_format($product->price_sell, 0, ',', ' ') }} FCFA</span>
                                <span class="badge badge-soft">Stock {{ $product->stock }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                                <div class="d-flex align-items-center gap-2 text-muted">
                                    <i class="bi bi-truck"></i>
                                    Livraison rapide
                                </div>
                                <div class="d-flex align-items-center gap-2 text-muted">
                                    <i class="bi bi-shield-check"></i>
                                    Paiement a la livraison
                                </div>
                            </div>
                            <form class="d-flex align-items-center gap-2" method="POST" action="{{ route('cart.add') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input class="form-control" type="number" name="quantity" min="1" max="99" value="1" style="width: 120px;" required>
                                <button class="btn btn-brand" type="submit">
                                    <i class="bi bi-cart"></i>
                                    Ajouter au panier
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="product-detail-section mt-4">
                        <h2 class="h5 fw-bold mb-3">Description</h2>
                        <div class="trix-content text-muted">{!! $product->description_html !!}</div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
