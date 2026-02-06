<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-1">Nouveau produit</h1>
            <p class="text-muted mb-0">Ajouter un produit.</p>
        </div>
    </x-slot>

    <div class="card card-soft p-4">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.products.partials.form', ['submitLabel' => 'Creer'])
        </form>
    </div>
</x-app-layout>
