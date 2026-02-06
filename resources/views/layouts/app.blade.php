<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cinema') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-neutral-950 text-neutral-200">

<div class="min-h-screen flex flex-col">

    {{-- HEADER / NAVIGATION --}}
    @include('layouts.navigation')

    {{-- OPTIONAL PAGE HEADER --}}
    @isset($header)
        <header class="border-b border-neutral-800 bg-neutral-900/60 backdrop-blur">
            <div class="max-w-7xl mx-auto px-6 py-4">
                {{ $header }}
            </div>
        </header>
    @endisset

    {{-- PAGE CONTENT --}}
    <main class="flex-1">
        <div class="max-w-7xl mx-auto px-6 py-8">
            {{ $slot }}
        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="border-t border-neutral-800 bg-neutral-950">
        <div class="max-w-7xl mx-auto px-6 py-6 text-sm text-neutral-500 flex flex-col md:flex-row gap-4 justify-between">
            <div>
                © {{ date('Y') }} {{ config('app.name', 'Cinema') }}
            </div>
            <div class="flex gap-4">
                <a href="#" class="hover:text-neutral-300">О сайте</a>
                <a href="#" class="hover:text-neutral-300">Правообладателям</a>
                <a href="#" class="hover:text-neutral-300">Контакты</a>
            </div>
        </div>
    </footer>

</div>

</body>
</html>
