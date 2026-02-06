<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Vue rapide des commandes et produits.</p>
        </div>
    </x-slot>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card card-soft p-3">
                <p class="text-muted mb-1">Commandes</p>
                <p class="h4 fw-bold mb-0">0</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft p-3">
                <p class="text-muted mb-1">Produits</p>
                <p class="h4 fw-bold mb-0">0</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-soft p-3">
                <p class="text-muted mb-1">Categories</p>
                <p class="h4 fw-bold mb-0">0</p>
            </div>
        </div>
    </div>
</x-app-layout>
