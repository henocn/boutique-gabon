<section>
    <header class="mb-3">
        <h2 class="h5 fw-semibold mb-1">Informations du profil</h2>
        <p class="text-muted mb-0">Mettre a jour votre nom et email.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label class="form-label" for="name">Nom</label>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label" for="email">Email</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success">Profil mis a jour.</div>
        @endif

        <button class="btn btn-brand" type="submit">Enregistrer</button>
    </form>
</section>
