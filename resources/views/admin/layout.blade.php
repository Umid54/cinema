<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin HUD')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background:
                radial-gradient(1200px 600px at 10% -10%, rgba(34,211,238,.25), transparent 45%),
                radial-gradient(900px 500px at 90% 10%, rgba(52,211,153,.22), transparent 45%),
                #020617;
            background-attachment: fixed;
        }

        .hud-glass {
            background: rgba(2, 6, 23, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(148,163,184,.18);
        }

        .hud-glow {
            box-shadow:
                0 0 0 1px rgba(34,211,238,.25),
                0 0 35px rgba(34,211,238,.35);
        }

        .hud-glow-emerald {
            box-shadow:
                0 0 0 1px rgba(52,211,153,.25),
                0 0 35px rgba(52,211,153,.35);
        }

        .hud-grid::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
            z-index: 0;
        }

        .hud-text    { color: #e5e7eb; }
        .hud-muted   { color: #94a3b8; }
        .hud-cyan    { color: #22d3ee; }
        .hud-emerald { color: #34d399; }
        .hud-rose    { color: #f87171; }

        .nav-item {
            display: block;
            padding: 12px 16px;
            border-radius: 14px;
            transition: .2s;
        }

        .nav-active {
            box-shadow:
                0 0 0 1px rgba(34,211,238,.35),
                0 0 25px rgba(34,211,238,.45);
            color: #22d3ee;
        }
		
		/* =========================
   ADMIN FORM – DARK HUD FIX
   ========================= */

.admin-form input,
.admin-form textarea,
.admin-form select {
    background-color: rgba(2, 6, 23, 0.92) !important;
    color: #e5e7eb !important;
    border: 1px solid rgba(148, 163, 184, 0.35) !important;
}

.admin-form input::placeholder,
.admin-form textarea::placeholder {
    color: #94a3b8 !important;
}

.admin-form input:focus,
.admin-form textarea:focus,
.admin-form select:focus {
    outline: none;
    border-color: #22d3ee !important;
    box-shadow:
        0 0 0 1px rgba(34, 211, 238, 0.45),
        0 0 20px rgba(34

		
    </style>
</head>

<body class="hud-grid hud-text">

<div class="relative z-10 flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="w-72 p-6 hud-glass hud-glow flex flex-col">

        <div class="mb-10">
            <div class="hud-cyan text-xl font-bold tracking-widest">
                ADMIN HUD
            </div>
            <div class="hud-muted text-xs mt-1">
                {{ config('app.name') }}
            </div>
        </div>

        <nav class="space-y-3 text-sm">

            <a href="{{ route('admin.dashboard') }}"
               class="nav-item hud-glass {{ request()->routeIs('admin.dashboard') ? 'nav-active' : '' }}">
                📊 Dashboard
            </a>

            <a href="{{ route('admin.movies.index') }}"
               class="nav-item hud-glass {{ request()->routeIs('admin.movies.*') ? 'nav-active' : '' }}">
                🎬 Контент
            </a>

            <a href="{{ route('admin.movies.create') }}"
               class="nav-item hud-glass">
                ➕ Добавить
            </a>

        </nav>

        <div class="mt-auto pt-10 hud-muted text-xs">
            HUD SYSTEM · ONLINE
        </div>
    </aside>

    {{-- MAIN --}}
    <main class="flex-1 p-10 space-y-8">

        {{-- TOP BAR --}}
        <div class="hud-glass hud-glow flex justify-between items-center px-6 py-4 rounded-2xl">
            <div>
                <div class="hud-muted text-xs uppercase tracking-widest">
                    Section
                </div>
                <div class="text-lg font-semibold hud-text">
                    @yield('header', 'Dashboard')
                </div>
            </div>

            <div class="flex items-center gap-6 text-sm">
                <span class="hud-muted">
                    {{ auth()->user()->email }}
                </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="hud-rose hover:text-rose-300 transition">
                        ⏻ Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- CONTENT --}}
        <section class="hud-glass hud-glow p-8 rounded-2xl hud-text">
            @yield('content')
        </section>

    </main>
</div>

</body>
</html>
