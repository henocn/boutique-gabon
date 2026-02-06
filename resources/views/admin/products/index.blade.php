<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-1">Produits</h1>
            <p class="text-muted mb-0">Gestion des produits et assignations.</p>
        </div>
        <a class="btn btn-brand" href="{{ route('admin.products.create') }}">Nouveau produit</a>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card card-soft p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
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
                                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.products.edit', $product) }}">Modifier</a>
                                <form class="d-inline" method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Supprimer ce produit ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Aucun produit</td>
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
