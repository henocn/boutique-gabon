@extends('management.layout')

@section('title', 'Commandes')

@push('styles')
    <link href="{{ asset('assets/css/orders.css') }}" rel="stylesheet">
@endpush

@section('content')
@php
    $toProcess = $orders->filter(fn ($order) => in_array($order->status, ['new', 'remind'], true));
    $unreachable = $orders->filter(fn ($order) => $order->status === 'unreachable');
    $processing = $orders->filter(fn ($order) => $order->status === 'processing');
    $deliveredToday = $orders->filter(fn ($order) => $order->status === 'deliver' && $order->updated_at && $order->updated_at->isToday());

    $statusClasses = [
        'unreachable' => 'order-row-unreachable',
        'remind' => 'order-row-remind',
        'processing' => 'order-row-processing',
        'new' => 'order-row-default',
    ];
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class='bx bx-package me-2'></i>Gestion des Commandes</h4>
    <a href="{{ route('management.orders.archive') }}" class="btn btn-outline-primary">
        <i class='bx bx-archive me-2'></i>Voir les Archives
    </a>
</div>

@if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
@endif

<ul class="nav nav-tabs" id="ordersTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-to-process" data-bs-toggle="tab" data-bs-target="#pane-to-process" type="button" role="tab">
            <i class='bx bx-time-five me-2'></i>A traiter
            <span class="badge bg-primary ms-2">{{ $toProcess->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-unreachable" data-bs-toggle="tab" data-bs-target="#pane-unreachable" type="button" role="tab">
            <i class='bx bx-phone-off me-2'></i>Injoignable
            <span class="badge bg-danger ms-2">{{ $unreachable->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-processing" data-bs-toggle="tab" data-bs-target="#pane-processing" type="button" role="tab">
            <i class='bx bx-calendar-check me-2'></i>Programmer
            <span class="badge bg-warning ms-2">{{ $processing->count() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-delivered" data-bs-toggle="tab" data-bs-target="#pane-delivered" type="button" role="tab">
            <i class='bx bx-check-circle me-2'></i>Livrer aujourd'hui
            <span class="badge bg-success ms-2">{{ $deliveredToday->count() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content" id="ordersTabsContent">
    <div class="tab-pane fade show active" id="pane-to-process" role="tabpanel">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-3 search-compact">
                    <div class="card-body p-2">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-7">
                                <input type="text" class="form-control form-control-sm" id="searchInput" placeholder="Rechercher par nom, telephone ou produit...">
                            </div>
                            <div class="col-md-5">
                                <select class="form-select form-select-sm" id="statusFilter">
                                    <option value="all">--Filtrer--</option>
                                    <option value="new">Nouvelles</option>
                                    <option value="remind">Rappeler</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <h6>Commandes a traiter (<span id="order-count">{{ $toProcess->count() }}</span>)</h6>
                @if ($toProcess->isEmpty())
                    <p class="text-muted">Aucune commande a traiter.</p>
                @else
                    <div class="table-responsive">
                        <table class="table" id="orders-table">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Numero</th>
                                    <th scope="col">Produit</th>
                                    <th scope="col">Qte</th>
                                    <th scope="col">Prix_Unitaire</th>
                                    <th scope="col">Prix_Total</th>
                                    <th scope="col">Mes_Notes</th>
                                    <th scope="col">Actions</th>
                                    <th scope="col">Passer le</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($toProcess as $order)
                                    @php
                                        $statusClass = $statusClasses[$order->status] ?? 'order-row-default';
                                    @endphp
                                    <tr class="order-row {{ $statusClass }}"
                                        data-status="{{ $order->status }}"
                                        data-client="{{ strtolower($order->client_name) }}"
                                        data-phone="{{ $order->client_phone }}"
                                        data-product="{{ strtolower($order->product?->name ?? '') }}">
                                        <td>#{{ $order->id }}</td>
                                        <td class="client-name-cell" title="{{ $order->client_name }}">{{ $order->client_name }}</td>
                                        <td>{{ $order->client_phone }}</td>
                                        <td class="product-name-cell" title="{{ $order->product?->name }}">{{ $order->product?->name }}</td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>{{ number_format($order->unit_price ?? 0, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ number_format($order->total_price, 0, ',', ' ') }} FCFA</td>
                                        <td class="note-cell" title="{{ $order->manager_note }}">{{ $order->manager_note }}</td>
                                        <td>
                                            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                                                <i class='bx bx-edit'></i>
                                            </button>
                                        </td>
                                        <td>{{ $order->created_at?->format('d/m/Y a H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pane-unreachable" role="tabpanel">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-3 search-compact">
                    <div class="card-body p-2">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-12">
                                <input type="text" class="form-control form-control-sm" id="searchInputUnreachable" placeholder="Rechercher par nom, telephone ou produit...">
                            </div>
                        </div>
                    </div>
                </div>

                <h6>Clients injoignables ({{ $unreachable->count() }})</h6>
                @if ($unreachable->isEmpty())
                    <p class="text-muted">Aucune commande injoignable.</p>
                @else
                    <div class="table-responsive">
                        <table class="table" id="orders-table-unreachable">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Numero</th>
                                    <th scope="col">Produit</th>
                                    <th scope="col">Qte</th>
                                    <th scope="col">Prix_Unitaire</th>
                                    <th scope="col">Prix_Total</th>
                                    <th scope="col">Mes_Notes</th>
                                    <th scope="col">Actions</th>
                                    <th scope="col">Passer le</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($unreachable as $order)
                                    <tr class="order-row order-row-unreachable"
                                        data-status="{{ $order->status }}"
                                        data-client="{{ strtolower($order->client_name) }}"
                                        data-phone="{{ $order->client_phone }}"
                                        data-product="{{ strtolower($order->product?->name ?? '') }}">
                                        <td>#{{ $order->id }}</td>
                                        <td class="client-name-cell" title="{{ $order->client_name }}">{{ $order->client_name }}</td>
                                        <td>{{ $order->client_phone }}</td>
                                        <td class="product-name-cell" title="{{ $order->product?->name }}">{{ $order->product?->name }}</td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>{{ number_format($order->unit_price ?? 0, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ number_format($order->total_price, 0, ',', ' ') }} FCFA</td>
                                        <td class="note-cell" title="{{ $order->manager_note }}">{{ $order->manager_note }}</td>
                                        <td>
                                            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                                                <i class='bx bx-edit'></i>
                                            </button>
                                        </td>
                                        <td>{{ $order->created_at?->format('d/m/Y a H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pane-processing" role="tabpanel">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-3 search-compact">
                    <div class="card-body p-2">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-12">
                                <input type="text" class="form-control form-control-sm" id="searchInputProcessing" placeholder="Rechercher par nom, telephone ou produit...">
                            </div>
                        </div>
                    </div>
                </div>

                <h6>Commandes programmees ({{ $processing->count() }})</h6>
                @if ($processing->isEmpty())
                    <p class="text-muted">Aucune commande programmee.</p>
                @else
                    <div class="table-responsive">
                        <table class="table" id="orders-table-processing">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Numero</th>
                                    <th scope="col">Produit</th>
                                    <th scope="col">Qte</th>
                                    <th scope="col">Prix_Unitaire</th>
                                    <th scope="col">Prix_Total</th>
                                    <th scope="col">Mes_Notes</th>
                                    <th scope="col">Actions</th>
                                    <th scope="col">Passer le</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($processing as $order)
                                    <tr class="order-row order-row-processing"
                                        data-status="{{ $order->status }}"
                                        data-client="{{ strtolower($order->client_name) }}"
                                        data-phone="{{ $order->client_phone }}"
                                        data-product="{{ strtolower($order->product?->name ?? '') }}">
                                        <td>#{{ $order->id }}</td>
                                        <td class="client-name-cell" title="{{ $order->client_name }}">{{ $order->client_name }}</td>
                                        <td>{{ $order->client_phone }}</td>
                                        <td class="product-name-cell" title="{{ $order->product?->name }}">{{ $order->product?->name }}</td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>{{ number_format($order->unit_price ?? 0, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ number_format($order->total_price, 0, ',', ' ') }} FCFA</td>
                                        <td class="note-cell" title="{{ $order->manager_note }}">{{ $order->manager_note }}</td>
                                        <td>
                                            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">
                                                <i class='bx bx-edit'></i>
                                            </button>
                                        </td>
                                        <td>{{ $order->created_at?->format('d/m/Y a H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="pane-delivered" role="tabpanel">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-3 search-compact">
                    <div class="card-body p-2">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-12">
                                <input type="text" class="form-control form-control-sm" id="searchInputDelivered" placeholder="Rechercher par nom, telephone ou produit...">
                            </div>
                        </div>
                    </div>
                </div>

                <h6>Commandes livrees aujourd'hui ({{ $deliveredToday->count() }})</h6>
                @if ($deliveredToday->isEmpty())
                    <p class="text-muted">Aucune commande livree aujourd'hui.</p>
                @else
                    <div class="table-responsive">
                        <table class="table" id="orders-table-delivered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Produit</th>
                                    <th>Qte</th>
                                    <th>Total</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deliveredToday as $order)
                                    <tr class="order-row" data-client="{{ strtolower($order->client_name) }}" data-phone="{{ $order->client_phone }}" data-product="{{ strtolower($order->product?->name ?? '') }}">
                                        <td>#{{ $order->id }}</td>
                                        <td class="client-name-cell" title="{{ $order->client_name }}">{{ $order->client_name }}</td>
                                        <td class="product-name-cell" title="{{ $order->product?->name }}">{{ $order->product?->name }}</td>
                                        <td>{{ $order->quantity }}</td>
                                        <td>{{ number_format($order->total_price, 0, ',', ' ') }} FCFA</td>
                                        <td><span class="badge bg-success">Livre</span></td>
                                        <td>{{ $order->updated_at?->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@foreach ($orders as $order)
    @php
        $actions = match ($order->status) {
            'processing' => [
                ['value' => 'deliver', 'label' => 'Livrer'],
                ['value' => 'canceled', 'label' => 'Annuler'],
            ],
            default => [
                ['value' => 'deliver', 'label' => 'Livrer'],
                ['value' => 'processing', 'label' => 'Programmer'],
                ['value' => 'remind', 'label' => 'Rappeler'],
                ['value' => 'unreachable', 'label' => 'Injoignable'],
                ['value' => 'canceled', 'label' => 'Annuler'],
            ],
        };
    @endphp
    <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1" aria-labelledby="orderModal{{ $order->id }}Label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header py-2">
                    <h6 class="modal-title mb-0" id="orderModal{{ $order->id }}Label">
                        <i class='bx bx-edit-alt me-1'></i>
                        Commande #{{ $order->id }}
                    </h6>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form action="{{ route('management.orders.status', $order) }}" method="POST">
                    @csrf
                    <div class="modal-body py-2">
                        @if (!empty($order->client_note))
                            <div class="alert alert-info mb-2 py-1 small">
                                <strong><i class='bx bx-message-detail me-1'></i>Note:</strong>
                                {{ $order->client_note }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-6">
                                <div class="mb-2">
                                    <label for="modalQuantity{{ $order->id }}" class="form-label mb-1 small fw-bold">Quantite</label>
                                    <input type="number" class="form-control form-control-sm" id="modalQuantity{{ $order->id }}" name="quantity" value="{{ $order->quantity }}" min="1" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-2">
                                    <label for="modalTotal{{ $order->id }}" class="form-label mb-1 small fw-bold">Prix (FCFA)</label>
                                    <input type="number" class="form-control form-control-sm" id="modalTotal{{ $order->id }}" name="total_price" value="{{ $order->total_price }}" min="0" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-2">
                                    <label for="actionSelect{{ $order->id }}" class="form-label mb-1 small fw-bold">Action</label>
                                    <select class="form-select form-select-sm" id="actionSelect{{ $order->id }}" name="status" required>
                                        <option value="">-- Choisir une action --</option>
                                        @foreach ($actions as $action)
                                            <option value="{{ $action['value'] }}" @selected($order->status === $action['value'])>
                                                {{ $action['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">
                                        <small class="text-muted">
                                            Statut: <strong>{{ ucfirst($order->status) }}</strong>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-2">
                                    <label for="modalManagerNote{{ $order->id }}" class="form-label mb-1 small fw-bold">Note manager</label>
                                    <textarea class="form-control form-control-sm" id="modalManagerNote{{ $order->id }}" name="manager_note" rows="2" placeholder="Notes...">{{ $order->manager_note }}</textarea>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="mb-2">
                                    <label for="modalDeliveryFee{{ $order->id }}" class="form-label mb-1 small fw-bold">Frais de livraison</label>
                                    <input type="number" class="form-control form-control-sm" id="modalDeliveryFee{{ $order->id }}" name="delivery_fee" value="0" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                            <i class='bx bx-x me-1'></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class='bx bx-save me-1'></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
@endsection

@push('scripts')
<script>
    function filterTable(tableId, inputId, statusFilterId) {
        const table = document.getElementById(tableId);
        const input = document.getElementById(inputId);
        const statusFilter = statusFilterId ? document.getElementById(statusFilterId) : null;
        if (!table || !input) return;

        const rows = table.querySelectorAll("tbody .order-row");
        const countTarget = document.getElementById("order-count");

        function runFilter() {
            const term = input.value.toLowerCase();
            const statusValue = statusFilter ? statusFilter.value : "all";
            let visible = 0;

            rows.forEach((row) => {
                const status = (row.getAttribute("data-status") || "").toLowerCase();
                const client = (row.getAttribute("data-client") || "").toLowerCase();
                const phone = (row.getAttribute("data-phone") || "").toLowerCase();
                const product = (row.getAttribute("data-product") || "").toLowerCase();

                const statusMatch = !statusFilter || statusValue === "all" || status === statusValue;
                const searchMatch =
                    term === "" ||
                    client.includes(term) ||
                    phone.includes(term) ||
                    product.includes(term);

                if (statusMatch && searchMatch) {
                    row.style.display = "";
                    visible++;
                } else {
                    row.style.display = "none";
                }
            });

            if (countTarget && tableId === "orders-table") {
                countTarget.textContent = visible;
            }
        }

        input.addEventListener("input", runFilter);
        if (statusFilter) {
            statusFilter.addEventListener("change", runFilter);
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        filterTable("orders-table", "searchInput", "statusFilter");
        filterTable("orders-table-unreachable", "searchInputUnreachable");
        filterTable("orders-table-processing", "searchInputProcessing");
        filterTable("orders-table-delivered", "searchInputDelivered");
    });
</script>
@endpush
