<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Vue rapide des commandes et produits.</p>
        </div>
    </x-slot>

        <form method="GET" id="rangeForm" class="row g-3 mb-3 align-items-end">
            <div class="col-auto">
                <label class="form-label small">Début</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar2-event" viewBox="0 0 16 16">
                          <path d="M6.5 7a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5v1h-3V7z"/>
                          <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h.5A1.5 1.5 0 0 1 15 2.5v11A1.5 1.5 0 0 1 13.5 15h-11A1.5 1.5 0 0 1 1 13.5v-11A1.5 1.5 0 0 1 2.5 1H3v-.5a.5.5 0 0 1 .5-.5zM2.5 3a.5.5 0 0 0-.5.5V4h12v-.5a.5.5 0 0 0-.5-.5h-11zM2 5v8.5c0 .276.224.5.5.5H13.5a.5.5 0 0 0 .5-.5V5H2z"/>
                        </svg>
                    </span>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request()->query('start_date') }}">
                </div>
            </div>
            <div class="col-auto">
                <label class="form-label small">Fin</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar2" viewBox="0 0 16 16">
                          <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h.5A1.5 1.5 0 0 1 15 2.5v11A1.5 1.5 0 0 1 13.5 15h-11A1.5 1.5 0 0 1 1 13.5v-11A1.5 1.5 0 0 1 2.5 1H3v-.5a.5.5 0 0 1 .5-.5zM2.5 3a.5.5 0 0 0-.5.5V4h12v-.5a.5.5 0 0 0-.5-.5h-11zM2 5v8.5c0 .276.224.5.5.5H13.5a.5.5 0 0 0 .5-.5V5H2z"/>
                        </svg>
                    </span>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request()->query('end_date') }}">
                </div>
            </div>
            <div class="col-auto d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-primary" title="Filtrer" aria-label="Filtrer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                      <path d="M11 6a5 5 0 1 1-10 0 5 5 0 0 1 10 0zM6 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>
                      <path d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.415 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/>
                    </svg>
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary ms-2" title="Réinitialiser" aria-label="Réinitialiser">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
                      <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/>
                      <path d="M8 1.5a.5.5 0 0 1 .5.5v3.793l1.146-1.147a.5.5 0 0 1 .708.708L8.354 7.854a.5.5 0 0 1-.708 0L6.646 6.707a.5.5 0 1 1 .708-.708L8 6.293V2a.5.5 0 0 1 .5-.5z"/>
                    </svg>
                </a>
            </div>
            <div class="col-12 mt-2">
                <div class="btn-group" role="group" aria-label="Presets">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-days="7">7 jours</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-days="30">30 jours</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-days="90">90 jours</button>
                </div>
            </div>
        </form>

        @if(!empty($appliedRangeLabel))
            <div class="mb-2">
                <span class="badge bg-light text-dark border">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-calendar3 me-1" viewBox="0 0 16 16" style="vertical-align: -2px;">
                      <path d="M14 4h-1V2.5a.5.5 0 0 0-1 0V4H4V2.5a.5.5 0 0 0-1 0V4H2a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1zM1 6v7a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V6H1z"/>
                      </svg>
                    <strong class="me-2">Période :</strong> {{ $appliedRangeLabel }}
                    <a href="{{ route('dashboard') }}" class="ms-2 text-decoration-none" title="Réinitialiser la période" aria-label="Réinitialiser la période">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-x-lg ms-1" viewBox="0 0 16 16" style="vertical-align: -2px;">
                          <path d="M2.146 2.146a.5.5 0 0 1 .708 0L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8 2.146 2.854a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </a>
                </span>
            </div>
        @endif

        <div class="row g-3">
        <!-- Card 1: Commandes -->
        <div class="col-md-3">
            <div class="card card-soft p-3 h-100 border-2 border-warning-subtle" style="min-height: 210px;">
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
            <div class="card card-soft p-3 h-100 border-2 border-warning-subtle" style="min-height: 210px;">
                <p class="fw-semibold mb-2">Produits</p>
                <div class="d-flex flex-column gap-0">
                    <span class="small text-muted py-1">Produits actifs : <span class="fw-bold">{{ $activeProducts ?? 0 }}</span></span>
                    <span class="small text-muted py-1">Produits total : <span class="fw-bold">{{ $totalProducts ?? 0 }}</span></span>
                </div>
            </div>
        </div>
        <!-- Card 3: Économie -->
        <div class="col-md-3">
            <div class="card card-soft p-3 h-100 border-2 border-warning-subtle" style="min-height: 210px;">
                <p class="fw-semibold mb-2">Économie</p>
                <div class="d-flex flex-column gap-0">
                    <span class="small text-muted py-1">Chiffre d'affaires : <span class="fw-bold">{{ number_format($deliveredRevenue, 0, ',', ' ') }} FCFA</span></span>
                    <span class="small text-muted py-1">Bénéfice total : <span class="fw-bold">{{ number_format($totalBenefit ?? 0, 0, ',', ' ') }} FCFA</span></span>
                </div>
            </div>
        </div>
        <!-- Card 4: Utilisateurs -->
        <div class="col-md-3">
            <div class="card card-soft p-3 h-100 border-2 border-warning-subtle" style="min-height: 210px;">
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
            <div class="card card-soft p-3 h-100 border-2 border-warning-subtle">
                <p class="fw-semibold mb-3">Top 5 produits les plus vendus</p>
                <div class="d-grid gap-2">
                    @forelse ($topSold as $item)
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                @php($thumb = $item->product?->productImages?->first())
                                @if ($thumb)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($thumb->path) }}" alt="{{ $item->product?->name }}" width="38" height="38" class="rounded border">
                                @else
                                    <div class="bg-light border rounded" style="width: 38px; height: 38px;"></div>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $item->product?->name }}</div>
                                    <div class="text-muted small">{{ $item->product?->manager?->name }}</div>
                                </div>
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
                            <div class="d-flex align-items-center gap-2">
                                @php($thumb = $item['product']?->productImages?->first())
                                @if ($thumb)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::url($thumb->path) }}" alt="{{ $item['product']?->name }}" width="38" height="38" class="rounded border">
                                @else
                                    <div class="bg-light border rounded" style="width: 38px; height: 38px;"></div>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $item['product']?->name }}</div>
                                    <div class="text-muted small">{{ $item['product']?->manager?->name }}</div>
                                </div>
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
            <div class="card card-soft p-3 h-100  border-2 border-warning-subtle">
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        (function () {
            const startInput = document.querySelector('input[name="start_date"]');
            const endInput = document.querySelector('input[name="end_date"]');
            if (startInput && endInput && window.flatpickr) {
                flatpickr(startInput, { dateFormat: 'Y-m-d', maxDate: 'today' });
                flatpickr(endInput, { dateFormat: 'Y-m-d', maxDate: 'today' });
            }

            // Preset buttons
            document.querySelectorAll('[data-days]').forEach(btn => {
                btn.addEventListener('click', function () {
                    const days = parseInt(this.getAttribute('data-days'), 10);
                    const end = new Date();
                    const start = new Date();
                    start.setDate(end.getDate() - (days - 1));
                    const fmt = d => d.toISOString().slice(0,10);
                    startInput.value = fmt(start);
                    endInput.value = fmt(end);
                    document.getElementById('rangeForm').submit();
                });
            });
        })();
    </script>
</x-app-layout>
