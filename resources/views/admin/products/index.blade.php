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
                        <th>Manager</th>
                        <th>Pays</th>
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
                            <td>{{ $product->manager?->name }}</td>
                            <td>
                                @if ($product->countries && count($product->countries) > 0)
                                    <div class="d-flex gap-1 flex-wrap">
                                        @foreach ($product->countries as $country)
                                            <span class="badge text-bg-info">{{ \App\Enums\Country::tryFrom($country)?->label() }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>{{ $product->stock }}</td>
                            <td>
                                <span class="badge text-bg-secondary">{{ ucfirst(str_replace('_', ' ', $product->status->value)) }}</span>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-info btn-copy-product-link" type="button" data-product-url="{{ route('products.show', $product) }}" data-product-name="{{ $product->name }}" aria-label="Copier le lien" title="Copier le lien du produit">
                                    <i class="bi bi-link-45deg"></i>
                                </button>
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

    <script>
        // Copier le lien du produit
        document.querySelectorAll('.btn-copy-product-link').forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                var productUrl = this.getAttribute('data-product-url');
                var productName = this.getAttribute('data-product-name');
                
                // Copier le lien dans le presse-papiers
                navigator.clipboard.writeText(productUrl).then(function() {
                    // Notification simple
                    var originalIcon = button.querySelector('i').className;
                    button.querySelector('i').className = 'bi bi-check-circle';
                    button.classList.remove('btn-outline-info');
                    button.classList.add('btn-outline-success');
                    
                    setTimeout(function() {
                        button.querySelector('i').className = originalIcon;
                        button.classList.remove('btn-outline-success');
                        button.classList.add('btn-outline-info');
                    }, 2000);
                    
                    console.log('Lien copié: ' + productUrl);
                }).catch(function() {
                    alert('Erreur lors de la copie du lien');
                });
            });
        });
    </script>
</x-app-layout>
