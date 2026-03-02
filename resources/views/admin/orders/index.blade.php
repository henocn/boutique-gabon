<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-0">Commandes</h1>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <style>
        table tbody tr.order-row-remind,
        table tbody tr.order-row-remind td {
            background-color: #cff4fc !important;
        }
        table tbody tr.order-row-unreachable,
        table tbody tr.order-row-unreachable td {
            background-color: #f8d7da !important;
        }
        table tbody tr.order-row-processing,
        table tbody tr.order-row-processing td {
            background-color: #fff3cd !important;
        }
    </style>

    <ul class="nav nav-pills gap-2 flex-wrap mb-3">
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-pill {{ $tab === 'to-process' ? 'active bg-transparent border border-2 border-warning text-warning' : 'bg-white border text-muted' }}"
                href="{{ route('admin.orders.index', ['tab' => 'to-process']) }}">
                <i class='bi bi-clock-history'></i>
                <span class="fw-semibold">À traiter</span>
                <span class="badge rounded-pill text-bg-primary">{{ $counts['to_process'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-pill {{ $tab === 'unreachable' ? 'active bg-transparent border border-2 border-warning text-warning' : 'bg-white border text-muted' }}"
                href="{{ route('admin.orders.index', ['tab' => 'unreachable']) }}">
                <i class='bi bi-telephone-x'></i>
                <span class="fw-semibold">Injoignables</span>
                <span class="badge rounded-pill text-bg-danger">{{ $counts['unreachable'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-pill {{ $tab === 'processing' ? 'active bg-transparent border border-2 border-warning text-warning' : 'bg-white border text-muted' }}"
                href="{{ route('admin.orders.index', ['tab' => 'processing']) }}">
                <i class='bi bi-calendar-check'></i>
                <span class="fw-semibold">Programmées</span>
                <span class="badge rounded-pill text-bg-info">{{ $counts['processing'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-pill {{ $tab === 'delivered-today' ? 'active bg-transparent border border-2 border-warning text-warning' : 'bg-white border text-muted' }}"
                href="{{ route('admin.orders.index', ['tab' => 'delivered-today']) }}">
                <i class='bi bi-check-circle'></i>
                <span class="fw-semibold">Livrées aujourd'hui</span>
                <span class="badge rounded-pill text-bg-success">{{ $counts['delivered_today'] ?? 0 }}</span>
            </a>
        </li>
    </ul>

    <div class="card card-soft p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Produit</th>
                        <th>Prix vente</th>
                        <th>Qt</th>
                        <th>Commentaire</th>
                        @if ($tab === 'delivered-today')
                            <th>Statut</th>
                        @endif
                        <th>Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr @class([
                            'order-row-remind' => $order->status === \App\Enums\OrderStatus::Remind,
                            'order-row-unreachable' => $order->status === \App\Enums\OrderStatus::Unreachable,
                            'order-row-processing' => $order->status === \App\Enums\OrderStatus::Processing,
                        ])>
                            <td>{{ $order->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $order->client_name }}</div>
                                <div class="text-muted small">{{ $order->client_contact }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $order->product?->name }}</div>
                                @if ($order->product?->manager)
                                    <div class="text-muted small">Géré par {{ $order->product->manager->name }}</div>
                                @endif
                            </td>
                            <td>
                                @if ($order->product?->price_sell !== null)
                                    {{ number_format($order->product?->price_sell, 0, ',', ' ') }} FCFA
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $order->quantity ?? 1 }}</td>
                            <td>
                                @if ($order->client_comment)
                                    <span class="text-muted small">{{ $order->client_comment }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            @if ($tab === 'delivered-today')
                                <td>
                                    <span class="badge {{ $order->status->badgeClass() }}">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                            @endif
                            <td>{{ $order->created_at?->format('d/m/Y à H:i') }}</td>
                            <td class="text-end">
                                @if ($tab === 'delivered-today')
                                    <span class="text-muted small">-</span>
                                @else
                                    <form class="d-inline-flex gap-2" method="POST" action="{{ route('admin.orders.update', $order) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select form-select-sm">
                                            @php
                                                $availableStatuses = match($tab) {
                                                    'to-process', 'unreachable' => [
                                                        \App\Enums\OrderStatus::Processing,
                                                        \App\Enums\OrderStatus::Delivered,
                                                        \App\Enums\OrderStatus::Remind,
                                                        \App\Enums\OrderStatus::Unreachable,
                                                        \App\Enums\OrderStatus::Cancelled,
                                                    ],
                                                    'processing' => [
                                                        \App\Enums\OrderStatus::Delivered,
                                                        \App\Enums\OrderStatus::Cancelled,
                                                    ],
                                                    default => []
                                                };
                                            @endphp
                                            @foreach ($availableStatuses as $status)
                                                <option value="{{ $status->value }}" @selected($order->status === $status)>
                                                    {{ $status->label() }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-outline-secondary" type="submit" title="Mettre a jour">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $tab === 'delivered-today' ? 9 : 8 }}" class="text-center text-muted">Aucune commande</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $orders->links() }}
    </div>

    <script>
        const REFRESH_INTERVAL = 60000; // 60 secondes
        let isRefreshing = false;

        async function refreshOrdersContent() {
            if (isRefreshing) return;
            isRefreshing = true;

            try {
                const currentUrl = window.location.href;
                const response = await fetch(currentUrl, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    console.error('Erreur lors du refresh:', response.status);
                    isRefreshing = false;
                    return;
                }

                const html = await response.text();
                
                // Créer un parser DOM temporaire
                const parser = new DOMParser();
                const newDoc = parser.parseFromString(html, 'text/html');

                // Mettre à jour juste la badge et la table
                const oldTabs = document.querySelector('ul.nav');
                const newTabs = newDoc.querySelector('ul.nav');
                if (newTabs) {
                    oldTabs.replaceWith(newTabs);
                }

                const oldTable = document.querySelector('.card.card-soft');
                const newTable = newDoc.querySelector('.card.card-soft');
                if (newTable) {
                    oldTable.replaceWith(newTable);
                }

                const oldPagination = document.querySelector('.mt-3');
                const newPagination = newDoc.querySelector('.mt-3');
                if (newPagination && oldPagination) {
                    oldPagination.replaceWith(newPagination);
                }

                console.log('✅ Commandes actualisées');
            } catch (error) {
                console.error('Erreur refresh:', error);
            } finally {
                isRefreshing = false;
            }
        }

        // Actualiser toutes les minutes
        setInterval(refreshOrdersContent, REFRESH_INTERVAL);

        // Timer visuel optionnel
        let nextRefresh = Math.ceil(REFRESH_INTERVAL / 1000);
        setInterval(() => {
            nextRefresh--;
            if (nextRefresh <= 0) {
                nextRefresh = Math.ceil(REFRESH_INTERVAL / 1000);
            }
            console.log(`⏱️ Prochaine actualisation dans ${nextRefresh}s`);
        }, 1000);
    </script>
</x-app-layout>
