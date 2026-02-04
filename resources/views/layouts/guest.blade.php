<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Cinema'))</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen text-slate-200 bg-[#05070c] overflow-x-hidden">

    {{-- GRID / NOISE BACKGROUND --}}
    <div class="fixed inset-0 z-0
                bg-[radial-gradient(circle_at_1px_1px,rgba(255,255,255,0.04)_1px,transparent_0)]
                bg-[size:24px_24px]">
    </div>

    {{-- MAIN GRADIENT --}}
    <div class="fixed inset-0 z-0
                bg-gradient-to-br from-black via-[#070b14] to-black">
    </div>

    {{-- NEON GLOWS --}}
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute -top-40 left-1/2 -translate-x-1/2
                    w-[900px] h-[900px] rounded-full
                    bg-cyan-500/10 blur-[180px]">
        </div>

        <div class="absolute bottom-0 right-1/4
                    w-[600px] h-[600px] rounded-full
                    bg-emerald-500/10 blur-[160px]">
        </div>
    </div>

    {{-- PAGE WRAPPER --}}
    <div class="relative z-10 min-h-screen flex items-center justify-center px-4">

        {{-- CONTENT SLOT --}}
        <div class="w-full max-w-md">
            @yield('content')
        </div>

    </div>

    {{-- FOOTER --}}
    <footer class="relative z-10 mt-12 text-center text-xs text-slate-500">
        © {{ date('Y') }} {{ config('app.name', 'Cinema') }} · All rights reserved
    </footer>

</body>
</html>
