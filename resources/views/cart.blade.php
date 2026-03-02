<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Panier - {{ config('app.shop_name', config('app.name')) }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <nav class="navbar navbar-expand-lg bg-white border-bottom navbar-client fixed-top">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/">{{ config('app.shop_name', config('app.name')) }}</a>
                <div class="d-flex gap-2">
                    <a class="btn btn-brand" href="/">Continuer les achats</a>
                </div>
            </div>
        </nav>

        <main class="py-5">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h1 class="h4 fw-bold mb-1">Votre panier</h1>
                        <p class="text-muted mb-0">Verifiez les produits avant de valider.</p>
                        <p class="text-muted small mb-0">Le panier se reinitialise apres 30 minutes sans validation.</p>
                    </div>
                </div>

                @if (! empty($expired))
                    <div class="alert alert-warning">Panier reinitialise apres 30 minutes.</div>
                @endif
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-4">
                    <div class="col-lg-7">
                        <div class="card card-soft p-3">
                            @forelse ($items as $item)
                                <div class="d-flex align-items-center gap-3 py-3 border-bottom">
                                    @php($thumb = $item['product']->productImages->first())
                                    @if ($thumb)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($thumb->path) }}" alt="{{ $item['product']->name }}" width="64" height="64" class="rounded border">
                                    @else
                                        <div class="bg-light border rounded" style="width: 64px; height: 64px;"></div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ $item['product']->name }}</div>
                                        <div class="text-muted small">{{ $item['product']->name }}</div>
                                        <div class="fw-semibold">{{ number_format($item['product']->price_sell, 0, ',', ' ') }} FCFA</div>
                                    </div>
                                    <form class="d-flex align-items-center gap-2" method="POST" action="{{ route('cart.update', $item['product']) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input class="form-control form-control-sm" type="number" name="quantity" min="0" max="99" value="{{ $item['quantity'] }}" style="width: 80px;">
                                        <button class="btn btn-sm btn-outline-secondary" type="submit" aria-label="Mettre a jour" title="Mettre a jour">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('cart.remove', $item['product']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="text-muted">Votre panier est vide.</div>
                            @endforelse
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card card-soft p-4">
                            <h2 class="h5 fw-bold mb-3">Validation</h2>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted">Total</span>
                                <span class="fw-semibold">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                            </div>
                            <form method="POST" action="{{ route('cart.checkout') }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="client_name">Nom</label>
                                    <input id="client_name" class="form-control" name="client_name" type="text" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="client_contact">Contact</label>
                                    <input id="client_contact" class="form-control" name="client_contact" type="text" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="client_comment">Commentaire (optionnel)</label>
                                    <textarea id="client_comment" class="form-control" name="client_comment" rows="3"></textarea>
                                </div>
                                <button class="btn btn-brand w-100" type="submit">Valider la commande</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
