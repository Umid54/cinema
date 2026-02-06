<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>MyTorrents</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background:
                radial-gradient(1200px 600px at 10% -10%, rgba(34,211,238,.35), transparent 45%),
                radial-gradient(900px 400px at 90% 10%, rgba(52,211,153,.30), transparent 45%),
                #020617;
            color: #e5e7eb;
        }

        .hud-glass {
            background: rgba(2, 6, 23, 0.92);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(148,163,184,.25);
        }

        .hud-glow {
            box-shadow:
                0 0 0 1px rgba(34,211,238,.25),
                0 0 40px rgba(34,211,238,.45);
        }

        .nav-link { color: #cbd5f5; transition: color .2s; }
        .nav-link:hover { color: #22d3ee; }
        .nav-link.active {
            color: #22d3ee;
            text-shadow: 0 0 12px rgba(34,211,238,.7);
        }

        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { scrollbar-width: none; }

        .favorite-btn {
            font-size: 20px;
            color: #94a3b8;
            border: 1px solid rgba(148,163,184,.35);
            transition: all .2s ease;
        }
        .favorite-btn.is-active {
            color: #f87171;
            border-color: rgba(248,113,113,.7);
            box-shadow: 0 0 14px rgba(248,113,113,.6);
        }
    </style>
</head>

<body class="min-h-screen">

{{-- HEADER --}}
<header class="hud-glass hud-glow">
    <div class="max-w-7xl mx-auto px-6 py-5 flex items-center justify-between">

        <a href="{{ route('home') }}"
           class="text-cyan-400 text-xl font-bold tracking-widest hover:text-cyan-300">
            MYTORRENTS
        </a>

        <nav class="flex gap-6 text-sm">
            <a href="{{ route('movies.index') }}"
               class="nav-link {{ request()->is('movies*') ? 'active' : '' }}">🎬 Фильмы</a>
            <a href="{{ route('series.index') }}"
               class="nav-link {{ request()->is('series*') ? 'active' : '' }}">📺 Сериалы</a>
            <a href="{{ route('cartoons.index') }}"
               class="nav-link {{ request()->is('cartoons') ? 'active' : '' }}">🧸 Мультфильмы</a>
            <a href="{{ route('documentary.index') }}"
               class="nav-link {{ request()->is('documentary') ? 'active' : '' }}">📚 Документальные</a>
            <a href="{{ route('anime.index') }}"
               class="nav-link {{ request()->is('anime') ? 'active' : '' }}">🌸 Аниме</a>

            @auth
                <a href="{{ route('favorites.index') }}"
                   class="text-rose-400 hover:text-rose-300">❤️ Избранное ({{ $favoritesCount }})</a>
            @endauth
        </nav>

        <div class="flex gap-4">
            @auth
                <a href="{{ route('account.profile') }}"
                   class="px-4 py-2 rounded-xl border border-emerald-400/60 text-emerald-300">
                    Кабинет
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="px-4 py-2 rounded-xl border border-rose-400/60 text-rose-300">
                        Выйти
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   class="px-4 py-2 rounded-xl border border-cyan-400/60 text-cyan-300">
                    Войти
                </a>
            @endauth
        </div>
    </div>
</header>

{{-- HERO --}}
<section class="max-w-7xl mx-auto px-6 py-20">
    <div class="hud-glass hud-glow rounded-3xl p-10">
        <h1 class="text-3xl font-bold text-white mb-4">
            Онлайн-кинотеатр MYTORRENTS
        </h1>
        <p class="text-slate-300 max-w-2xl">
            Фильмы, сериалы, аниме и документальные проекты.
            Новинки и популярные релизы — в одном месте.
        </p>
    </div>
</section>

{{-- NEW RELEASES --}}
<main class="max-w-7xl mx-auto px-6">

@if($latestMovies->count())
<section class="mb-24">
    <div class="flex justify-between mb-6">
        <h2 class="text-xl font-semibold">🔥 Новые релизы</h2>
        <a href="{{ route('movies.index') }}" class="text-cyan-400">Все →</a>
    </div>

    <div class="flex gap-6 overflow-x-auto pb-6 scrollbar-hide">
        @foreach($latestMovies as $movie)
            <a href="{{ route('movies.watch', $movie) }}"
               class="min-w-[220px] rounded-2xl overflow-hidden hover:-translate-y-2 transition">
                <img src="{{ $movie->poster_url }}"
                     class="w-full h-[320px] object-cover">
            </a>
        @endforeach
    </div>
</section>
@endif

</main>

<footer class="text-center text-xs text-slate-500 py-12">
    © {{ date('Y') }} MyTorrents · Мы не храним контент · 18+
</footer>

</body>
</html>
