<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-0">Categories</h1>
        </div>
        <button class="btn btn-brand" type="button" data-bs-toggle="modal" data-bs-target="#categoryCreateModal" aria-label="Ajouter">
            <i class="bi bi-plus-lg"></i>
        </button>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card card-soft p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Statut</th>
                        <th>Image</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>
                                <span class="badge {{ $category->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $category->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </td>
                            <td>
                                @if ($category->image_path)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($category->image_path) }}" alt="{{ $category->name }}" width="48" height="48" class="rounded">
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.categories.edit', $category) }}" aria-label="Modifier" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form class="d-inline" method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Supprimer cette categorie ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Supprimer" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Aucune categorie</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $categories->links() }}
    </div>

    <div class="modal fade" id="categoryCreateModal" tabindex="-1" aria-labelledby="categoryCreateLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryCreateLabel">Nouvelle categorie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
                        @csrf
                        @include('admin.categories.partials.form', ['submitLabel' => 'Creer', 'compactActions' => true])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
