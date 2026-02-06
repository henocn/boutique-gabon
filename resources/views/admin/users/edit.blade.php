<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-1">Modifier utilisateur</h1>
            <p class="text-muted mb-0">Mettre a jour l'utilisateur.</p>
        </div>
    </x-slot>

    <div class="card card-soft p-4">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            @include('admin.users.partials.form', ['submitLabel' => 'Mettre a jour'])
        </form>
    </div>
</x-app-layout>
