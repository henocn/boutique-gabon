<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-1">Nouvelle categorie</h1>
            <p class="text-muted mb-0">Ajouter une categorie.</p>
        </div>
    </x-slot>

    <div class="card card-soft p-4">
        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.categories.partials.form', ['submitLabel' => 'Creer'])
        </form>
    </div>
</x-app-layout>
