<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-0">Commandes</h1>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <ul class="nav nav-pills gap-2 flex-wrap mb-3">
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-pill {{ $tab === 'active' ? 'bg-white border border-2 border-warning text-warning-emphasis' : 'bg-white border text-dark' }}"
                href="{{ route('admin.orders.index', ['tab' => 'active']) }}">
                <span class="fw-semibold">Actives</span>
                <span class="badge rounded-pill text-bg-danger">{{ $counts['new'] ?? 0 }} Nouv.</span>
                <span class="badge rounded-pill text-bg-secondary">{{ $counts['processed'] ?? 0 }} Traitees</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-pill {{ $tab === 'unreachable' ? 'bg-white border border-2 border-warning text-warning-emphasis' : 'bg-white border text-dark' }}"
                href="{{ route('admin.orders.index', ['tab' => 'unreachable']) }}">
                <span class="fw-semibold">Injoignables</span>
                <span class="badge rounded-pill text-bg-secondary">{{ $counts['unreachable'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-pill {{ $tab === 'delivered' ? 'bg-white border border-2 border-warning text-warning-emphasis' : 'bg-white border text-dark' }}"
                href="{{ route('admin.orders.index', ['tab' => 'delivered']) }}">
                <span class="fw-semibold">Livrees</span>
                <span class="badge rounded-pill text-bg-secondary">{{ $counts['delivered'] ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 px-3 py-2 rounded-pill {{ $tab === 'other' ? 'bg-white border border-2 border-warning text-warning-emphasis' : 'bg-white border text-dark' }}"
                href="{{ route('admin.orders.index', ['tab' => 'other']) }}">
                <span class="fw-semibold">Autres</span>
                <span class="badge rounded-pill text-bg-secondary">{{ $counts['other'] ?? 0 }}</span>
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
                        <th>Statut</th>
                        <th>Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr class="{{ $order->status === \App\Enums\OrderStatus::Processed ? 'table-success' : '' }}">
                            <td>{{ $order->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $order->client_name }}</div>
                                <div class="text-muted small">{{ $order->client_contact }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $order->product?->name }}</div>
                                <div class="text-muted small">{{ $order->product?->category?->name }}</div>
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
                                <span class="badge text-bg-secondary">{{ ucfirst(str_replace('_', ' ', $order->status->value)) }}</span>
                            </td>
                            <td>{{ $order->created_at?->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <form class="d-inline-flex gap-2" method="POST" action="{{ route('admin.orders.update', $order) }}">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->value }}" @selected($order->status === $status)>
                                                {{ ucfirst(str_replace('_', ' ', $status->value)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-outline-secondary" type="submit" title="Mettre a jour">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Aucune commande</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $orders->links() }}
    </div>
</x-app-layout>
