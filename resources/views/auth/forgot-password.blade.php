<x-guest-layout>
    <h1 class="h4 fw-bold mb-3 text-center">Mot de passe oublie</h1>
    <p class="text-muted">Saisissez votre email pour recevoir un lien de reinitialisation.</p>

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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <button class="btn btn-brand w-100" type="submit">Envoyer le lien</button>
    </form>
</x-guest-layout>
