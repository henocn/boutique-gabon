@extends('layouts.app')

@push('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="{{ asset('assets/css/front-product.css') }}" rel="stylesheet">
@endpush

@section('body')
    @include('partials.front-navbar')
    <main class="flex-grow-1">
        @yield('content')
    </main>
    @include('partials.front-footer')
@endsection
