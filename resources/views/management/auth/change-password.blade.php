@extends('management.layout')

@section('title', 'Changer le mot de passe')

@section('content')
<h3 class="mb-4">Changer le mot de passe</h3>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('management.password.update') }}" class="card p-4">
    @csrf
    <div class="mb-3">
        <label class="form-label">Mot de passe actuel</label>
        <input type="password" name="current_password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Nouveau mot de passe</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Confirmer</label>
        <input type="password" name="password_confirmation" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-dark">Mettre a jour</button>
</form>
@endsection
