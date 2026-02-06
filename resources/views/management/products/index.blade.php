@extends('management.layout')

@section('title', 'Produits')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Liste des produits</h2>
    <a href="{{ route('management.products.create') }}" class="btn btn-dark">
        <i class='bx bx-plus'></i> Ajouter
    </a>
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

@if ($products->isEmpty())
    <div class="alert alert-info">Aucun produit disponible.</div>
@else
    <div class="products-table-container table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Nom</th>
                    <th>Categorie</th>
                    <th>Prix Gabon</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    @php
                        $rowClass = ($product->status ?? 1) ? 'status-active' : 'status-inactive';
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td>{{ $product->id }}</td>
                        <td>
                            <img src="{{ asset('uploads/main/' . $product->image) }}" alt="{{ $product->name }}" class="product-image">
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category?->name }}</td>
                        <td>{{ number_format($product->countries->first()?->pivot?->selling_price ?? 0, 0, ',', ' ') }} FCFA</td>
                        <td class="position-relative">
                            <button type="button" class="action-btn context-menu-btn" data-id="{{ $product->id }}">
                                <i class='bx bx-dots-vertical-rounded'></i>
                            </button>
                            <div class="product-actions-menu" id="contextMenu{{ $product->id }}">
                                <button type="button" class="menu-item" onclick="copyProductLink({{ $product->id }})">
                                    <i class='bx bx-link'></i> Partager
                                </button>
                                <a href="{{ route('product.show', $product) }}" class="menu-item" target="_blank" rel="noopener">
                                    <i class='bx bx-show'></i> Voir
                                </a>
                                <a href="{{ route('management.products.edit', $product) }}" class="menu-item">
                                    <i class='bx bx-edit'></i> Modifier
                                </a>
                                <form method="POST" action="{{ route('management.products.destroy', $product) }}" onsubmit="return confirm('Supprimer ce produit ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="menu-item delete-item">
                                        <i class='bx bx-trash'></i> Supprimer
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
@endsection

@push('scripts')
<script>
    function copyProductLink(productId) {
        const shareUrl = "{{ url('/product') }}/" + productId;
        navigator.clipboard.writeText(shareUrl);
    }

    document.querySelectorAll('.context-menu-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            document.querySelectorAll('.product-actions-menu').forEach(function (menu) {
                menu.style.display = 'none';
            });
            const menu = document.getElementById('contextMenu' + btn.getAttribute('data-id'));
            if (menu) menu.style.display = 'block';
        });
    });

    document.addEventListener('click', function () {
        document.querySelectorAll('.product-actions-menu').forEach(function (menu) {
            menu.style.display = 'none';
        });
    });
</script>
@endpush
