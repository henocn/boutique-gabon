{{-- Push Notifications Registration --}}
@if(auth()->check() && auth()->user()->role === 'admin')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="vapid-public-key" data-vapid-public-key="{{ config('services.vapid.public_key') }}">
    <script src="{{ asset('js/push-notifications.js') }}"></script>
@endif
