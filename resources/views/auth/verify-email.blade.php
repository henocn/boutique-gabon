<x-guest-layout>
    <h1 class="h4 fw-bold mb-3 text-center">Verification email</h1>
    <p class="text-muted">Cliquez sur le lien recu par email pour activer votre compte.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success">Un nouveau lien a ete envoye.</div>
    @endif

    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="btn btn-brand" type="submit">Renvoyer</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-secondary" type="submit">Deconnexion</button>
        </form>
    </div>
</x-guest-layout>
