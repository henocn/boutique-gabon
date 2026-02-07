<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h4 fw-bold mb-0">Commandes</h1>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="card card-soft p-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Produit</th>
                        @if (Auth::user()->isAdmin())
                            <th>Manager</th>
                        @endif
                        <th>Statut</th>
                        <th>Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>{{ $order->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $order->client_name }}</div>
                                <div class="text-muted small">{{ $order->client_contact }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $order->product?->name }}</div>
                                <div class="text-muted small">{{ $order->product?->category?->name }}</div>
                            </td>
                            @if (Auth::user()->isAdmin())
                                <td>{{ $order->product?->manager?->name }}</td>
                            @endif
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
                            <td colspan="7" class="text-center text-muted">Aucune commande</td>
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
