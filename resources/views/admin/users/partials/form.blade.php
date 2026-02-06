@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-lg-6">
        <label class="form-label" for="name">Nom</label>
        <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required>
    </div>
    <div class="col-lg-6">
        <label class="form-label" for="email">Email</label>
        <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
    </div>
    <div class="col-lg-6">
        <label class="form-label" for="role">Role</label>
        <select id="role" name="role" class="form-select" required>
            <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
            <option value="manager" @selected(old('role', $user->role) === 'manager')>Manager</option>
        </select>
    </div>
    <div class="col-lg-6">
        <label class="form-label" for="password">Mot de passe</label>
        <input id="password" name="password" type="password" class="form-control" {{ $user->exists ? '' : 'required' }}>
        @if ($user->exists)
            <div class="form-text">Laisser vide pour conserver l'existant.</div>
        @endif
    </div>
</div>

<div class="form-check mt-3">
    <input id="is_active" name="is_active" type="checkbox" class="form-check-input" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Actif</label>
</div>

<div class="d-flex gap-2 mt-3">
    <button class="btn btn-brand" type="submit">{{ $submitLabel }}</button>
    <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Annuler</a>
</div>
