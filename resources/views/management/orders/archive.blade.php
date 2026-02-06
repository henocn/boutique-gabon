@extends('management.layout')

@section('title', 'Archives')

@push('styles')
	<link href="{{ asset('assets/css/orders.css') }}" rel="stylesheet">
@endpush

@section('content')
<h4 class="mb-3"><i class='bx bx-archive me-2'></i>Archives des commandes</h4>
@if ($orders->isEmpty())
	<div class="alert alert-info">Aucune commande archivee.</div>
@else
	<div class="table-container table-responsive">
		<table class="table align-middle">
			<thead>
				<tr>
					<th>ID</th>
					<th>Client</th>
					<th>Produit</th>
					<th>Total</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($orders as $order)
					<tr>
						<td>#{{ $order->id }}</td>
						<td>{{ $order->client_name }}</td>
						<td>{{ $order->product?->name }}</td>
						<td>{{ number_format($order->total_price, 0, ',', ' ') }} FCFA</td>
						<td>{{ $order->updated_at?->format('d/m/Y H:i') }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@endif
@endsection
