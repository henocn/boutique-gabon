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
        <nav class="navbar navbar-expand-lg bg-white border-bottom">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/">Boutique Gabon</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                        <li class="nav-item"><a class="nav-link" href="#categories">Categories</a></li>
                        <li class="nav-item"><a class="nav-link" href="#products">Produits</a></li>
                        <li class="nav-item"><a class="nav-link" href="#cart">Panier</a></li>
                        <li class="nav-item">
                            <a class="btn btn-brand" href="{{ route('login') }}">Espace Admin</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <header class="py-5">
            <div class="container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <p class="badge badge-soft text-uppercase mb-3">E-commerce local</p>
                        <h1 class="display-5 fw-bold mb-3">Vente rapide, gestion simple, experience propre.</h1>
                        <p class="text-muted mb-4">Catalogue clair, panier en session et commandes gerees par role. Tout ce qu'il faut pour vendre vite.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-brand btn-lg" href="#products">Voir les produits</a>
                            <a class="btn btn-outline-secondary btn-lg" href="#categories">Explorer les categories</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card card-soft p-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="bg-white border rounded-4 p-3">
                                        <p class="text-muted mb-1">Panier</p>
                                        <p class="fw-semibold mb-0">Session rapide</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-white border rounded-4 p-3">
                                        <p class="text-muted mb-1">Commandes</p>
                                        <p class="fw-semibold mb-0">Suivi clair</p>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="bg-white border rounded-4 p-3">
                                        <p class="text-muted mb-1">Roles</p>
                                        <p class="fw-semibold mb-0">Admin & Manager securises</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <section id="categories" class="py-5">
            <div class="container">
                <div class="d-flex align-items-end justify-content-between mb-3">
                    <h2 class="h3 fw-bold mb-0">Categories</h2>
                    <a class="text-brand fw-semibold" href="#products">Voir tout</a>
                </div>
                <div class="row g-3">
                    @for ($i = 0; $i < 4; $i++)
                        <div class="col-6 col-lg-3">
                            <div class="card card-soft p-3">
                                <div class="ratio ratio-4x3 bg-light rounded-4"></div>
                                <div class="mt-3">
                                    <p class="fw-semibold mb-1">Categorie {{ $i + 1 }}</p>
                                    <p class="text-muted small mb-0">Description courte</p>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>

        <section id="products" class="py-5 bg-white">
            <div class="container">
                <div class="d-flex align-items-end justify-content-between mb-3">
                    <h2 class="h3 fw-bold mb-0">Produits</h2>
                    <form class="d-flex gap-2">
                        <input class="form-control" type="search" placeholder="Rechercher">
                        <button class="btn btn-outline-secondary" type="button">Filtrer</button>
                    </form>
                </div>
                <div class="row g-3">
                    @for ($i = 0; $i < 6; $i++)
                        <div class="col-6 col-lg-4">
                            <div class="card card-soft p-3 h-100">
                                <div class="ratio ratio-4x3 bg-light rounded-4"></div>
                                <div class="mt-3">
                                    <p class="fw-semibold mb-1">Produit {{ $i + 1 }}</p>
                                    <p class="text-muted small mb-2">Livraison rapide</p>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="fw-bold">12 000 FCFA</span>
                                        <button class="btn btn-sm btn-brand" type="button">Ajouter</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>

        <section id="cart" class="py-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-7">
                        <h2 class="h3 fw-bold mb-3">Panier</h2>
                        <div class="card card-soft p-4">
                            <p class="text-muted mb-0">Votre panier est vide pour l'instant.</p>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <h2 class="h3 fw-bold mb-3">Validation</h2>
                        <div class="card card-soft p-4">
                            <div class="mb-3">
                                <label class="form-label">Nom</label>
                                <input class="form-control" type="text" placeholder="Votre nom">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Contact</label>
                                <input class="form-control" type="text" placeholder="+241">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Commentaire (optionnel)</label>
                                <textarea class="form-control" rows="3" placeholder="Infos livraison"></textarea>
                            </div>
                            <button class="btn btn-brand w-100" type="button">Valider la commande</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer class="py-4 border-top bg-white">
            <div class="container d-flex flex-wrap justify-content-between align-items-center">
                <p class="mb-0 text-muted">{{ date('Y') }} Boutique Gabon. Tous droits reserves.</p>
                <span class="text-muted">Support: +241 00 00 00 00</span>
            </div>
        </footer>
    </body>
</html>
