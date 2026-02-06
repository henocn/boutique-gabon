@extends('management.layout')

@section('title', 'Dashboard')

@section('content')
<h2 class="fw-bold mb-4">Dashboard</h2>
<div class="row g-3">
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Produits</h6>
                <h3 class="fw-bold mb-0">0</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Commandes</h6>
                <h3 class="fw-bold mb-0">0</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Utilisateurs</h6>
                <h3 class="fw-bold mb-0">0</h3>
            </div>
        </div>
    </div>
</div>
@endsection
