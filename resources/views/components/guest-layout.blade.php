<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Cinema') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-black text-slate-200">

    {{-- background --}}
    <div class="fixed inset-0 z-0 bg-gradient-to-br from-black via-neutral-900 to-black"></div>

    {{-- glow --}}
    <div class="fixed inset-0 z-0 pointer-events-none">
        <div class="absolute top-1/3 left-1/2 -translate-x-1/2
                    w-[600px] h-[600px]
                    bg-cyan-500/10 blur-[140px] rounded-full">
        </div>
    </div>

    {{-- CONTENT --}}
    <div class="relative z-10 min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            {{ $slot }}
        </div>
    </div>

</body>
</html>
