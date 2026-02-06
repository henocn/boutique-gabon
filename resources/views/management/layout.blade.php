@extends('layouts.app')

@push('styles')
    <link href="{{ asset('assets/css/admin.css') }}" rel="stylesheet">
@endpush

@section('body')
    @include('partials.navbar')
    <main class="container my-4">
        @yield('content')
    </main>
    @include('partials.footer')
@endsection
