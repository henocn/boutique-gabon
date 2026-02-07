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
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="ratio ratio-4x3 bg-light border product-hero-image">
                            @php($firstImage = $product->productImages->first())
                            @if ($firstImage)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($firstImage->path) }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <p class="text-muted mb-1">{{ $product->category?->name }}</p>
                        <h1 class="h3 fw-bold mb-3">{{ $product->name }}</h1>
                        <div class="text-muted mb-3 product-detail-desc">{!! $product->description_html !!}</div>
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <span class="h4 fw-bold mb-0">{{ number_format($product->price_sell, 0, ',', ' ') }} FCFA</span>
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
            </div>
        </main>
    </body>
</html>
