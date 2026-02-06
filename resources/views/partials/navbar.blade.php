@php
    $isAdmin = auth()->user()?->role === 1;
@endphp

<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('management.dashboard') }}">
            <i class='bx bx-store'></i>
            <span>MyShop</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav gap-2 mx-4">
                @if ($isAdmin)
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('management') || request()->is('management/dashboard')) active @endif" href="{{ route('management.dashboard') }}">
                            <i class='bx bx-home'></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('management/products*')) active @endif" href="{{ route('management.products.index') }}">
                            <i class='bx bx-box'></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('management/categories*')) active @endif" href="{{ route('management.categories.index') }}">
                            <i class='bx bx-purchase-tag'></i> Categories
                        </a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link @if(request()->is('management/orders*')) active @endif" href="{{ route('management.orders.index') }}">
                        <i class='bx bx-cart'></i> Orders
                    </a>
                </li>
                @if ($isAdmin)
                    <li class="nav-item">
                        <a class="nav-link @if(request()->is('management/users*')) active @endif" href="{{ route('management.users.index') }}">
                            <i class='bx bx-user'></i> Users
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('management.password') }}" class="btn btn-login">Change Password</a>
            <span class="btn btn-login">{{ auth()->user()?->name ?? 'Profile' }}</span>
            <form method="POST" action="{{ route('management.logout') }}">
                @csrf
                <button type="submit" class="btn btn-signup">Logout</button>
            </form>
        </div>
    </div>
</nav>
