<x-guest-layout>
    <h1 class="h4 fw-bold mb-3 text-center">Connexion</h1>

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

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        </div>

        <div class="mb-3">
            <label class="form-label" for="password">Mot de passe</label>
            <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password">
        </div>

        <div class="form-check mb-3">
            <input id="remember_me" class="form-check-input" type="checkbox" name="remember">
            <label class="form-check-label" for="remember_me">Se souvenir de moi</label>
        </div>

        <div class="d-flex align-items-center justify-content-between">
            @if (Route::has('password.request'))
                <a class="text-decoration-none text-muted" href="{{ route('password.request') }}">Mot de passe oublie ?</a>
            @endif
            <button class="btn btn-brand" type="submit">Se connecter</button>
        </div>
    </form>
</x-guest-layout>
