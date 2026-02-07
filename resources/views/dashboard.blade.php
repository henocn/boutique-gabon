<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Vue rapide des commandes et produits.</p>
        </div>
    </x-slot>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card card-soft p-3">
                <p class="text-muted mb-1">Commandes</p>
                <p class="h4 fw-bold mb-0">{{ $totalOrders }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-soft p-3">
                <p class="text-muted mb-1">Livrees</p>
                <p class="h4 fw-bold mb-0">{{ $deliveredOrders }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-soft p-3">
                <p class="text-muted mb-1">Rentabilite</p>
                <p class="h4 fw-bold mb-0">{{ number_format($deliveredRevenue, 0, ',', ' ') }} FCFA</p>
            </div>
        </div>
        @if ($isAdmin)
            <div class="col-md-3">
                <div class="card card-soft p-3">
                    <p class="text-muted mb-1">Produits actifs</p>
                    <p class="h4 fw-bold mb-0">{{ $activeProducts }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-soft p-3">
                    <p class="text-muted mb-1">Categories actives</p>
                    <p class="h4 fw-bold mb-0">{{ $activeCategories }}</p>
                </div>
            </div>
        @endif
    </div>

    <div class="row g-3 mt-1">
        <div class="col-lg-6">
            <div class="card card-soft p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <p class="fw-semibold mb-0">Commandes 7 derniers jours</p>
                    <span class="text-muted small">Total</span>
                </div>
                <div class="d-flex align-items-end gap-2" style="height: 120px;">
                    @foreach ($orderSeries as $point)
                        <div class="d-flex flex-column align-items-center">
                            <div class="bg-brand rounded" style="width: 14px; height: {{ $point['height'] }}%;"></div>
                            <div class="small text-muted mt-1">{{ $point['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @if ($isAdmin)
            <div class="col-lg-6">
                <div class="card card-soft p-3 h-100">
                    <p class="fw-semibold mb-3">Produits recents</p>
                    <div class="d-grid gap-2">
                        @forelse ($latestProducts as $product)
                            <div class="d-flex align-items-center gap-3">
                                @php($thumb = $product->productImages->first())
                                @if ($thumb)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($thumb->path) }}" alt="{{ $product->name }}" width="44" height="44" class="rounded border">
                                @else
                                    <div class="bg-light border rounded" style="width: 44px; height: 44px;"></div>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                    <div class="text-muted small">{{ $product->category?->name }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted">Aucun produit recent.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if ($isAdmin)
        <div class="row g-3 mt-1">
            <div class="col-lg-6">
                <div class="card card-soft p-3 h-100">
                    <p class="fw-semibold mb-3">Produits les plus vendus</p>
                    <div class="d-grid gap-2">
                        @forelse ($topSold as $item)
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-semibold">{{ $item->name }}</div>
                                    <div class="text-muted small">{{ $item->category?->name }}</div>
                                </div>
                                <span class="badge text-bg-secondary">{{ $item->sold_count }}</span>
                            </div>
                        @empty
                            <div class="text-muted">Aucune vente livree.</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card card-soft p-3 h-100">
                    <p class="fw-semibold mb-3">Produits les plus rentables</p>
                    <div class="d-grid gap-2">
                        @forelse ($topRevenue as $item)
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-semibold">{{ $item['product']?->name }}</div>
                                    <div class="text-muted small">{{ $item['product']?->category?->name }}</div>
                                </div>
                                <span class="badge text-bg-secondary">{{ number_format($item['revenue'], 0, ',', ' ') }} FCFA</span>
                            </div>
                        @empty
                            <div class="text-muted">Aucune vente livree.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
