<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $product->name }} - {{ config('app.shop_name', config('app.name')) }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <nav class="navbar navbar-expand-lg bg-white border-bottom navbar-client fixed-top">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/">{{ config('app.shop_name', config('app.name')) }}</a>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    {{-- Panier UI supprimé --}}
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
                            <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner ratio ratio-4x3 bg-light border product-hero-image rounded-3 overflow-hidden">
                                    @forelse ($images as $index => $image)
                                        <div class="carousel-item @if ($index === 0) active @endif">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($image->path) }}" alt="{{ $product->name }}" class="d-block w-100 h-100 object-fit-cover">
                                        </div>
                                    @empty
                                        <div class="carousel-item active">
                                            <div class="d-flex align-items-center justify-content-center text-muted h-100">Aucune image</div>
                                        </div>
                                    @endforelse
                                </div>
                                @if ($images->count() > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Precedent</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Suivant</span>
                                    </button>
                                @endif
                            </div>
                            @if ($images->count() > 1)
                                <div class="d-flex gap-2 mt-3 flex-wrap">
                                    @foreach ($images as $index => $image)
                                        <button class="product-thumb border rounded-3 overflow-hidden" type="button" data-bs-target="#productCarousel" data-bs-slide-to="{{ $index }}" aria-label="Image {{ $index + 1 }}">
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($image->path) }}" alt="{{ $product->name }}" class="w-100 h-100 object-fit-cover">
                                        </button>
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
                            {{-- Ajout panier supprimé --}}
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
