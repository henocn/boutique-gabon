<footer class="footer-custom">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5 class="fw-bold">
                    <i class='bx bx-store'></i> MyShop
                </h5>
                <p class="small mb-0">
                    Votre boutique en ligne pour trouver les meilleurs produits au meilleur prix.
                </p>
            </div>
            <div class="col-md-4 mb-3">
                <h6 class="fw-bold mb-2">Navigation</h6>
                <ul class="list-unstyled small">
                    <li><a href="{{ url('/') }}" class="text-white-50 text-decoration-none"><i class='bx bx-home'></i> Home</a></li>
                    <li><a href="{{ route('management.products.index') }}" class="text-white-50 text-decoration-none"><i class='bx bx-box'></i> Products</a></li>
                    <li><a href="{{ route('management.orders.index') }}" class="text-white-50 text-decoration-none"><i class='bx bx-cart'></i> Orders</a></li>
                    <li><a href="{{ route('management.users.index') }}" class="text-white-50 text-decoration-none"><i class='bx bx-user'></i> Users</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h6 class="fw-bold mb-2">Contact</h6>
                <p class="small mb-1"><i class='bx bx-envelope'></i> contact@myshop.com</p>
                <p class="small mb-0"><i class='bx bx-phone'></i> +241 00 00 00 00</p>
            </div>
        </div>
    </div>
</footer>
