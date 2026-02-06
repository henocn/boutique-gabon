<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-1">Nouvel utilisateur</h1>
            <p class="text-muted mb-0">Ajouter un admin ou manager.</p>
        </div>
    </x-slot>

    <div class="card card-soft p-4">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            @include('admin.users.partials.form', ['submitLabel' => 'Creer'])
        </form>
    </div>
</x-app-layout>
