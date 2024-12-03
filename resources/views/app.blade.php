<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="/icons/{{ app()->environment() }}.png">

        <title inertia>{{ config('app.name', 'App') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
        <script src="https://js.stripe.com/v3/"></script>
        <!-- Scripts -->
        @routes
        <script>window.translations = {!! json_encode(request()->get('_translations'), true)  !!};</script>
        <script>window.features = {!! json_encode(\Laravel\Pennant\Feature::all()) !!};</script>
        <script async src="https://maps.googleapis.com/maps/api/js?libraries=places&language=fr&key={{ config('services.google_api_key') }}"></script>
        @vite(['resources/js/app.js'])
        <script src="https://kit.fontawesome.com/dc3be731aa.js" crossorigin="anonymous"></script>
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
