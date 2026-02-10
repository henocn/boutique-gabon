<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.shop_name', config('app.name')) }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-light">
        <div class="min-vh-100 d-flex align-items-center justify-content-center py-5">
            <div class="card card-soft p-4" style="max-width: 440px; width: 100%;">
                <div class="text-center mb-3">
                    <a class="text-decoration-none fw-bold text-dark" href="/">{{ config('app.shop_name', config('app.name')) }}</a>
                </div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
