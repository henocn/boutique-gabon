@extends('layouts.app')

@php
    $t = [
        'commander' => 'Commander',
        'order_button_text' => 'Valider la commande',
        'fullname' => 'Nom complet',
        'number' => 'Numero',
        'address' => 'Ville, Quartier',
        'note' => 'Note eventuelle',
        'about' => 'A propos',
        'about_us' => 'A propos de nous',
        'payment' => 'Modes de paiement',
        'shipping' => 'Livraison',
        'online_store' => 'Nous sommes une boutique en ligne',
        'buy_services' => 'Nous proposons des services d\'achat',
        'privacy' => 'Politique de confidentialite',
        'copyright' => 'Tous les droits reserves © Maxora Market 2025',
    ];
    $displayTitle = trim(preg_replace('/[\x{0600}-\x{06FF}]+/u', '', $product->name));
    $displayDescription = trim(preg_replace('/[\x{0600}-\x{06FF}]+/u', '', $product->description ?? ''));
@endphp

@section('title', $displayTitle)

@push('styles')
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;500;700&display=swap">
    <link rel="stylesheet" href="{{ asset('assets/css/index2.css') }}">
@endpush

@section('body')
    <header class="yc-header">
        <nav class="yc-navbar container">
            <div class="logo">
                <a href="{{ url('/') }}" aria-label="home">
                    <img src="{{ asset('favicon.ico') }}" alt="TUBKAL MARKET">
                </a>
            </div>
            <div class="corner">
                <button class="commander-btn" onclick="location.href='#product_details'">{{ $t['commander'] }}</button>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <section class="container product-layout">
            <div class="product-images">
                <div class="main-image-wrapper">
                    <img id="main-image" src="{{ asset('uploads/main/' . $product->image) }}" alt="{{ $displayTitle }}">
                </div>
                <div class="carousel-grid">
                    <div class="carousel-item">
                        <img src="{{ asset('uploads/main/' . $product->image) }}" alt="{{ $displayTitle }}">
                    </div>
                    @foreach (['carousel1', 'carousel2', 'carousel3', 'carousel4', 'carousel5'] as $slot)
                        @if ($product->{$slot})
                            <div class="carousel-item">
                                <img src="{{ asset('uploads/carousel/' . $product->{$slot}) }}" alt="{{ $displayTitle }}">
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="product-details" id="product_details">
                <h1 class="product-name">{{ $displayTitle }}</h1>
                <h2 class="product-price">{{ number_format($sellingPrice, 0, ',', ' ') }} CFA</h2>
                <form class="express-checkout-form" method="POST" action="{{ route('order.store') }}">
                    @csrf
                    <div class="express-checkout-fields">
                        <input type="text" name="client_name" class="form-control-custom" placeholder="{{ $t['fullname'] }}" required>
                        <div class="phone-input-wrapper">
                            @if ($product->countries->count() > 0)
                                <select name="client_country" class="form-control-country" required>
                                    @foreach ($product->countries as $ctry)
                                        <option value="{{ $ctry->id }}">+{{ $ctry->phone_code }}</option>
                                    @endforeach
                                </select>
                            @else
                                <span class="form-control-country">+{{ $country?->phone_code ?? '241' }}</span>
                            @endif
                            <input type="tel" name="client_phone" class="form-control-custom" placeholder="{{ $t['number'] }}" required>
                        </div>
                        <input type="text" name="client_adress" class="form-control-custom" placeholder="{{ $t['address'] }}" required>
                        <textarea name="client_note" class="form-control-custom" rows="2" placeholder="{{ $t['note'] }}"></textarea>

                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                    </div>
                    <div class="modal-footer-custom">
                        <button type="submit" class="btn-submit-order">
                            <i class='bx bx-check-circle'></i>
                            <span>{{ $t['order_button_text'] }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <div class="product-description">
                {!! $displayDescription !!}
            </div>

            <div class="toast-container">
                <div id="liveToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
                    <div class="d-flex">
                        <div id="toastMessage" class="toast-body">
                            {{ session('order_message') ?? '' }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="columns container">
            <div class="column logo">
                <img src="{{ asset('favicon.ico') }}" alt="MAXORA MARKET" width="110" height="70">
            </div>
            <div class="column">
                <h1>{{ $t['about'] }}</h1>
                <a href="#">{{ $t['about_us'] }}</a>
                <a href="#">{{ $t['payment'] }}</a>
                <a href="#">{{ $t['shipping'] }}</a>
            </div>
            <div class="column">
                <h1>{{ $t['about'] }}</h1>
                <h5>{{ $t['online_store'] }}</h5>
                <h5>{{ $t['buy_services'] }}</h5>
                <h5>{{ $t['privacy'] }}</h5>
            </div>
        </div>
        <div class="copyright-wrapper">
            <p><strong>{{ $t['copyright'] }}</strong></p>
        </div>
    </footer>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const carouselItems = document.querySelectorAll(".carousel-item img");
            const mainImage = document.getElementById("main-image");
            carouselItems.forEach((img) => {
                img.addEventListener("click", () => {
                    if (mainImage) {
                        mainImage.src = img.src;
                    }
                });
            });

            const toastEl = document.getElementById("liveToast");
            const toastBody = document.getElementById("toastMessage");
            if (toastEl && toastBody) {
                const message = toastBody.textContent.trim();
                if (message.length > 0) {
                    toastEl.className = "toast align-items-center text-white border-0";
                    toastEl.classList.add(/succes|success/i.test(message) ? "bg-success" : "bg-danger");
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                }
            }
        });
    </script>
@endpush
