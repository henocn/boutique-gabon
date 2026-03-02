<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $product->name }} - {{ config('app.shop_name', config('app.name')) }}</title>
        <meta property="og:title" content="{{ $product->name }}" />
        <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($product->description_html ?? ''), 150) }}" />
        @if ($product->productImages->first())
            <meta property="og:image" content="{{ \Illuminate\Support\Facades\Storage::url($product->productImages->first()->path) }}" />
        @endif
        <meta property="og:type" content="product" />
        <meta property="og:site_name" content="{{ config('app.shop_name') }}" />

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="{{ asset('js/tracking-manager.js') }}"></script>
        <script src="{{ asset('js/order-tracking.js') }}"></script>

        <style>
            :root {
                --primary-color: rgb(56, 89, 161);
                --secondary-color: #33696e;
                --neutral-color: #16191b;
                --neutral-light-color: #e3e3e3;
            }

            body {
                font-family: 'Manrope', sans-serif;
                color: var(--neutral-color);
                background-color: #f7f7f7;
            }

            .header {
                background-color: #fff;
                border-bottom: 1px solid var(--primary-color);
                padding: 15px 0;
                position: sticky;
                top: 0;
                z-index: 100;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
                margin-bottom: 30px;
            }

            .navbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 20px;
            }

            .logo {
                max-width: 140px;
            }

            .logo img {
                width: 100%;
                height: auto;
            }

            /* PRODUCT LAYOUT */
            .product-layout {
                display: flex;
                gap: 40px;
                background: #fff;
                border-radius: 8px;
                padding: 30px;
                margin-bottom: 30px;
            }

            .product-images {
                flex: 1;
                min-width: 300px;
            }

            .main-image-wrapper {
                width: 100%;
                aspect-ratio: 1 / 1;
                background: #f0f0f0;
                border-radius: 8px;
                overflow: hidden;
                margin-bottom: 20px;
            }

            .main-image-wrapper img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.3s ease;
            }

            .main-image-wrapper:hover img {
                transform: scale(1.05);
            }

            .carousel-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
                gap: 10px;
            }

            .carousel-item {
                cursor: pointer;
                border-radius: 4px;
                overflow: hidden;
                border: 2px solid transparent;
                transition: border-color 0.2s;
            }

            .carousel-item:hover {
                border-color: var(--primary-color);
            }

            .carousel-item img {
                width: 100%;
                aspect-ratio: 1 / 1;
                object-fit: cover;
            }

            .product-details {
                flex: 1;
                min-width: 300px;
                display: flex;
                flex-direction: column;
            }

            .product-name {
                font-size: 28px;
                color: var(--neutral-color);
                margin-bottom: 15px;
            }

            .product-price {
                font-size: 32px;
                color: var(--primary-color);
                font-weight: bold;
                margin-bottom: 25px;
            }

            .express-checkout-form {
                border: 1px solid #e9ecef;
                border-radius: 8px;
                padding: 20px;
                background: #fafafa;
            }

            .express-checkout-fields {
                display: flex;
                flex-direction: column;
                gap: 15px;
                margin-bottom: 15px;
            }

            .form-control-custom {
                width: 100%;
                padding: 10px 12px;
                border: 1px solid var(--neutral-light-color);
                border-radius: 4px;
                font-size: 14px;
                font-family: 'Manrope', sans-serif;
            }

            .form-control-custom:focus {
                outline: none;
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(56, 89, 161, 0.1);
            }

            .phone-input-wrapper {
                display: flex;
                gap: 10px;
            }

            .form-control-country {
                padding: 10px 12px;
                border: 1px solid var(--neutral-light-color);
                border-radius: 4px;
                font-size: 14px;
                font-family: 'Manrope', sans-serif;
                flex-shrink: 0;
            }

            .form-control-country:focus {
                outline: none;
                border-color: var(--primary-color);
                box-shadow: 0 0 0 3px rgba(56, 89, 161, 0.1);
            }

            .phone-input-wrapper input {
                flex: 1;
            }

            .modal-footer-custom {
                padding-top: 15px;
                border-top: 1px solid #e9ecef;
            }

            .btn-submit-order {
                width: 100%;
                background-color: var(--primary-color);
                color: #fff;
                border: none;
                padding: 12px;
                font-size: 16px;
                font-weight: 600;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.2s;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .btn-submit-order:hover {
                background-color: var(--secondary-color);
            }

            .btn-submit-order:disabled {
                background-color: #ccc;
                cursor: not-allowed;
            }

            .product-description {
                background: #fff;
                border-radius: 8px;
                padding: 30px;
                margin-bottom: 30px;
            }

            .product-description h1,
            .product-description h2,
            .product-description h3 {
                color: var(--neutral-color);
                margin-top: 20px;
                margin-bottom: 15px;
            }

            .product-description a {
                color: var(--primary-color);
                text-decoration: none;
            }

            .product-description a:hover {
                text-decoration: underline;
            }

            .product-description img {
                max-width: 100%;
                height: auto;
                border-radius: 4px;
                margin: 15px 0;
            }

            /* FOOTER */
            footer {
                background-color: var(--primary-color);
                color: #fff;
                padding: 40px 0 10px;
                margin-top: 60px;
            }

            footer .columns {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 30px;
                margin-bottom: 20px;
            }

            footer .column img {
                max-width: 110px;
                height: auto;
            }

            footer .column h1 {
                font-size: 16px;
                margin-bottom: 15px;
            }

            footer .column a, footer .column h5 {
                font-size: 14px;
                opacity: 0.9;
                text-decoration: none;
                color: #fff;
                display: block;
                margin-bottom: 8px;
            }

            footer .column a:hover {
                opacity: 1;
                text-decoration: underline;
            }

            footer .copyright-wrapper {
                text-align: center;
                padding-top: 20px;
                border-top: 1px solid rgba(255,255,255,0.2);
                font-size: 13px;
            }

            /* TOAST */
            .toast-container {
                position: fixed;
                bottom: 2rem;
                right: 2rem;
                z-index: 9999;
            }

            /* RESPONSIVE */
            @media (max-width: 768px) {
                .product-layout {
                    flex-direction: column;
                    padding: 20px;
                    gap: 20px;
                }

                .carousel-grid {
                    grid-template-columns: repeat(4, 1fr);
                }

                .product-name {
                    font-size: 22px;
                }

                .product-price {
                    font-size: 24px;
                }
            }
        </style>
    </head>
    <body>
        <!-- HEADER -->
        <header class="header">
            <nav class="navbar container-lg">
                <div class="logo">
                    <a href="/" aria-label="Accueil">
                        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.shop_name') }}">
                    </a>
                </div>
            </nav>
        </header>

        <!-- MAIN CONTENT -->
        <main class="container-lg">
            <div id="toast-container" class="toast-container"></div>

            <section class="product-layout" id="product_details">
                <!-- IMAGES -->
                <div class="product-images">
                    <div class="main-image-wrapper">
                        @php($firstImage = $product->productImages->first())
                        @if ($firstImage)
                            <img id="main-image" src="{{ \Illuminate\Support\Facades\Storage::url($firstImage->path) }}" alt="{{ $product->name }}">
                        @else
                            <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #e9ecef; color: #999;">
                                Pas d'image
                            </div>
                        @endif
                    </div>

                    @if ($product->productImages->count() > 1)
                        <div class="carousel-grid">
                            @foreach ($product->productImages as $image)
                                <div class="carousel-item" onclick="document.getElementById('main-image').src = '{{ \Illuminate\Support\Facades\Storage::url($image->path) }}'">
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($image->path) }}" alt="{{ $product->name }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- DETAILS + FORM -->
                <div class="product-details">
                    <h1 class="product-name">{{ $product->name }}</h1>
                    <h2 class="product-price">{{ number_format($product->price_sell, 0, ',', ' ') }} XOF</h2>

                    <form class="express-checkout-form" method="POST" action="{{ route('order.modal.store') }}">
                        @csrf
                        <div class="express-checkout-fields">
                            <input type="text" name="client_name" class="form-control-custom" 
                                   placeholder="Nom complet" required 
                                   value="{{ session('order_client_name', '') }}">

                            <div class="phone-input-wrapper">
                                <select name="client_country" class="form-control-country" required>
                                    <option value="">Pays</option>
                                    @if ($product->countries && count($product->countries) > 0)
                                        @foreach ($product->countries as $country)
                                            @if ($countryEnum = \App\Enums\Country::tryFrom($country))
                                                <option value="{{ $country }}">{{ $countryEnum->flag() }} {{ $countryEnum->label() }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                <input type="tel" name="client_contact" class="form-control-custom" 
                                       placeholder="Numéro de téléphone" required 
                                       value="{{ session('order_client_contact', '') }}">
                            </div>

                            <input type="text" name="client_address" class="form-control-custom" 
                                   placeholder="Adresse (Ville, Quartier)" 
                                   value="{{ session('order_client_address', '') }}">

                            <input type="number" name="quantity" class="form-control-custom" 
                                   min="1" max="99" value="1" placeholder="Quantité" required>

                            <textarea name="client_comment" class="form-control-custom" 
                                      rows="3" placeholder="Note éventuelle (optionnel)"></textarea>

                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                        </div>

                        <div class="modal-footer-custom">
                            <button type="submit" class="btn-submit-order">
                                <i class="bi bi-check-circle"></i>
                                <span>Valider la commande</span>
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- DESCRIPTION -->
            @if ($product->description_html)
                <div class="product-description">
                    {!! $product->description_html !!}
                </div>
            @endif
        </main>

        <!-- FOOTER -->
        <footer>
            <div class="columns container-lg">
                <div class="column logo">
                    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.shop_name') }}" width="110" height="70">
                </div>
                <div class="column">
                    <h1>À propos</h1>
                    <a href="#">À propos de nous</a>
                    <a href="#">Modes de paiement</a>
                    <a href="#">Livraison</a>
                </div>
                <div class="column">
                    <h1>Services</h1>
                    <h5>Nous sommes une boutique en ligne</h5>
                    <h5>Nous proposons des services d'achat</h5>
                    <a href="#">Politique de confidentialité</a>
                </div>
            </div>
            <div class="copyright-wrapper">
                <p><strong>&copy; {{ date('Y') }} {{ config('app.shop_name') }} - Tous droits réservés</strong></p>
            </div>
        </footer>

        <script>
            function showToast(message, type = 'success') {
                var container = document.getElementById('toast-container');
                if (!container) return;
                var toast = document.createElement('div');
                toast.className = 'toast align-items-center text-bg-' + type + ' border-0 show';
                toast.style.minWidth = '250px';
                toast.style.marginBottom = '0.5rem';
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
                container.appendChild(toast);
                setTimeout(function () {
                    toast.classList.remove('show');
                    setTimeout(function () { toast.remove(); }, 300);
                }, 4000);
            }

            @if (session('status'))
                window.addEventListener('DOMContentLoaded', function () {
                    showToast("{{ session('status') }}", 'success');
                });
            @endif

            @if ($errors->has('order'))
                window.addEventListener('DOMContentLoaded', function () {
                    showToast("{{ $errors->first('order') }}", 'danger');
                });
            @endif

            // FORM TRACKING
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    if (typeof trackWhenReady === 'function') {
                        trackWhenReady('QualifiedVisit', {
                            content_ids: ['{{ $product->id }}'],
                            content_name: '{{ $product->name }}',
                            value: {{ $product->price_sell }},
                            currency: 'XOF'
                        });
                    }
                }, 2000);

                const form = document.querySelector('.express-checkout-form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        if (typeof trackWhenReady === 'function') {
                            trackWhenReady('Purchase', {
                                content_ids: ['{{ $product->id }}'],
                                contents: [{
                                    id: '{{ $product->id }}',
                                    quantity: document.querySelector('input[name="quantity"]').value || 1,
                                    item_price: {{ $product->price_sell }}
                                }],
                                currency: 'XOF',
                                num_items: 1,
                                value: {{ $product->price_sell }}
                            });
                        }

                        setTimeout(function() {
                            form.submit();
                        }, 300);
                    });
                }
            });
        </script>
    </body>
</html>
