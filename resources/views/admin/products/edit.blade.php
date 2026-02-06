<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-1">Modifier produit</h1>
            <p class="text-muted mb-0">Mettre a jour le produit.</p>
        </div>
    </x-slot>

    <div class="card card-soft p-4">
        <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.products.partials.form', ['submitLabel' => 'Mettre a jour'])
        </form>
    </div>
</x-app-layout>
