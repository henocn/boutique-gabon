<section>
    <header class="mb-3">
        <h2 class="h5 fw-semibold mb-1">Mot de passe</h2>
        <p class="text-muted mb-0">Utilisez un mot de passe fort.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label class="form-label" for="update_password_current_password">Mot de passe actuel</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control" autocomplete="current-password">
            @if ($errors->updatePassword->get('current_password'))
                <div class="text-danger small">{{ $errors->updatePassword->first('current_password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label" for="update_password_password">Nouveau mot de passe</label>
            <input id="update_password_password" name="password" type="password" class="form-control" autocomplete="new-password">
            @if ($errors->updatePassword->get('password'))
                <div class="text-danger small">{{ $errors->updatePassword->first('password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label" for="update_password_password_confirmation">Confirmer</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
            @if ($errors->updatePassword->get('password_confirmation'))
                <div class="text-danger small">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
        </div>

        @if (session('status') === 'password-updated')
            <div class="alert alert-success">Mot de passe mis a jour.</div>
        @endif

        <button class="btn btn-brand" type="submit">Enregistrer</button>
    </form>
</section>
