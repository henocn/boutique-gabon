@extends('layouts.app')

@section('title', 'Connexion')

@push('styles')
    <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
@endpush

@section('body')
<section class="py-5">
    <div class="container" style="max-width: 420px;">
        <h2 class="mb-4 text-center">Connexion admin</h2>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('management.login.submit') }}" class="card p-4 shadow-sm">
            @csrf
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-dark w-100">Se connecter</button>
        </form>
    </div>
</section>
@endsection
