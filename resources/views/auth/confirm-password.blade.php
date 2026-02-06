<x-guest-layout>
    <h1 class="h4 fw-bold mb-3 text-center">Confirmer</h1>
    <p class="text-muted">Confirmez votre mot de passe pour continuer.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label" for="password">Mot de passe</label>
            <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password">
        </div>

        <button class="btn btn-brand w-100" type="submit">Confirmer</button>
    </form>
</x-guest-layout>
