<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Vue rapide des commandes et produits.</p>
        </div>
    </x-slot>

    <div class="row g-3">
        <!-- Card 1: Commandes -->
        <div class="col-md-3">
            <div class="card card-soft p-3 h-100 border border-2 border-warning-subtle" style="min-height: 210px;">
                <p class="fw-semibold mb-2">Commandes</p>
                <div class="d-flex flex-column gap-0">
                    <span class="small text-muted py-1">Total : <span class="fw-bold">{{ $totalOrders }}</span></span>
                    <span class="small text-success py-1">Livrées : <span class="fw-bold">{{ $deliveredOrders }}</span></span>
                    <span class="small text-warning py-1">En attente : <span class="fw-bold">{{ $pendingOrders ?? 0 }}</span></span>
                    <span class="small text-secondary py-1">Annulées : <span class="fw-bold">{{ $cancelledOrders ?? 0 }}</span></span>
                </div>
            </div>
        </div>
        <!-- Card 2: Produits & Catégories -->
        <div class="col-md-3">
            <div class="card card-soft p-3 h-100 border border-2 border-warning-subtle" style="min-height: 210px;">
                <p class="fw-semibold mb-2">Produits & Catégories</p>
                <div class="d-flex flex-column gap-0">
                    <span class="small text-muted py-1">Produits actifs : <span class="fw-bold">{{ $activeProducts ?? 0 }}</span></span>
                    <span class="small text-muted py-1">Produits total : <span class="fw-bold">{{ $totalProducts ?? 0 }}</span></span>
                    <span class="small text-muted py-1">Catégories actives : <span class="fw-bold">{{ $activeCategories ?? 0 }}</span></span>
                    <span class="small text-muted py-1">Catégories total : <span class="fw-bold">{{ $totalCategories ?? 0 }}</span></span>
                </div>
            </div>
        </div>
        <!-- Card 3: Économie -->
        <div class="col-md-3">
            <div class="card card-soft p-3 h-100 border border-2 border-warning-subtle" style="min-height: 210px;">
                <p class="fw-semibold mb-2">Économie</p>
                <div class="d-flex flex-column gap-0">
                    <span class="small text-muted py-1">Chiffre d'affaires : <span class="fw-bold">{{ number_format($deliveredRevenue, 0, ',', ' ') }} FCFA</span></span>
                    <span class="small text-muted py-1">Bénéfice total : <span class="fw-bold">{{ number_format($totalBenefit ?? 0, 0, ',', ' ') }} FCFA</span></span>
                </div>
            </div>
        </div>
        <!-- Card 4: Utilisateurs -->
        <div class="col-md-3">
            <div class="card card-soft p-3 h-100 border border-2 border-warning-subtle" style="min-height: 210px;">
                <p class="fw-semibold mb-2">Utilisateurs</p>
                <div class="d-flex flex-column gap-0">
                    <span class="small text-muted py-1">Total : <span class="fw-bold">{{ $totalUsers ?? 0 }}</span></span>
                    <span class="small text-muted py-1">Admins : <span class="fw-bold">{{ $adminUsers ?? 0 }}</span></span>
                    <span class="small text-muted py-1">Managers : <span class="fw-bold">{{ $managerUsers ?? 0 }}</span></span>
                    <span class="small text-muted py-1">Actifs : <span class="fw-bold">{{ $activeUsers ?? 0 }}</span></span>
                    <span class="small text-muted py-1">Inactifs : <span class="fw-bold">{{ $inactiveUsers ?? 0 }}</span></span>
                </div>
            </div>
        </div>
    </div>
    @if ($isAdmin)
    <div class="row g-3 mt-1">
        <div class="col-lg-6">
            <div class="card card-soft p-3 h-100 border border-2 border-warning-subtle">
                <p class="fw-semibold mb-3">Top 5 produits les plus vendus</p>
                <div class="d-grid gap-2">
                    @forelse ($topSold as $item)
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-semibold">{{ $item->product?->name }}</div>
                                <div class="text-muted small">{{ $item->product?->category?->name }}</div>
                            </div>
                            <span class="badge text-bg-secondary">{{ $item->sold_count }} ventes</span>
                        </div>
                    @empty
                        <div class="text-muted">Aucune vente livrée.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card card-soft p-3 h-100 border border-2 border-warning-subtle">
                <p class="fw-semibold mb-3">Top 5 produits les plus rentables</p>
                <div class="d-grid gap-2">
                    @forelse ($topRevenue as $item)
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="fw-semibold">{{ $item['product']?->name }}</div>
                                <div class="text-muted small">{{ $item['product']?->category?->name }}</div>
                            </div>
                            <span class="badge text-bg-success">{{ number_format($item['revenue'], 0, ',', ' ') }} FCFA</span>
                        </div>
                    @empty
                        <div class="text-muted">Aucune vente livrée.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3 mt-1">
        <div class="col-lg-12">
            <div class="card card-soft p-3 h-100 border border-2 border-warning-subtle">
                <p class="fw-semibold mb-3">Top vendeurs (assistantes/managers)</p>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom & Prénom</th>
                                <th>Rôle</th>
                                <th>Produits vendus</th>
                                <th>Chiffre d'affaires</th>
                                <th>Bénéfice total</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(isset($topSellers) && count($topSellers))
                            @foreach($topSellers as $i => $seller)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $seller['user']?->name }}</td>
                                    <td>{{ ucfirst($seller['user']?->role) }}</td>
                                    <td>{{ $seller['total_sales'] }}</td>
                                    <td>{{ number_format($seller['total_revenue'], 0, ',', ' ') }} FCFA</td>
                                    <td>{{ number_format($seller['total_benefit'], 0, ',', ' ') }} FCFA</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center text-muted">Aucun vendeur trouvé.</td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- Section Produits récents supprimée -->
    </div>
</x-app-layout>
