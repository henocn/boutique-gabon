<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-0">Produits</h1>
        </div>
        <a class="btn btn-brand" href="{{ route('admin.products.create') }}" aria-label="Ajouter">
            <i class="bi bi-plus-lg"></i>
        </a>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card card-soft p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Produit</th>
                        <th>Categorie</th>
                        <th>Manager</th>
                        <th>Stock</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>
                                @php($thumb = $product->productImages->first())
                                @if ($thumb)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($thumb->path) }}" alt="Image" width="48" height="48" class="rounded border">
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                <div class="text-muted small">{{ number_format($product->price_sell, 0, ',', ' ') }} FCFA</div>
                            </td>
                            <td>{{ $product->category?->name }}</td>
                            <td>{{ $product->manager?->name }}</td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                <span class="badge text-bg-secondary">{{ ucfirst(str_replace('_', ' ', $product->status->value)) }}</span>
                            </td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.products.edit', $product) }}" aria-label="Modifier" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form class="d-inline" method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Supprimer ce produit ?');">
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
                            <td colspan="7" class="text-center text-muted">Aucun produit</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $products->links() }}
    </div>
</x-app-layout>
