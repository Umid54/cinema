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

        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { scrollbar-width: none; }

        .nav-link {
            color: #cbd5f5;
            transition: color .2s;
        }
        .nav-link:hover {
            color: #22d3ee;
        }

        /* ===== FAVORITE HEART ===== */
        .favorite-btn {
            font-size: 20px;
            color: #94a3b8;
            border: 1px solid rgba(148,163,184,.35);
            transition: all .2s ease;
        }

        .favorite-btn:hover {
            color: #fb7185;
            border-color: rgba(248,113,113,.6);
        }

        .favorite-btn.is-active {
            color: #f87171;
            border-color: rgba(248,113,113,.7);
            box-shadow:
                0 0 14px rgba(248,113,113,.6),
                inset 0 0 6px rgba(248,113,113,.35);
        }
    </style>
</head>

<body class="min-h-screen">

<header class="hud-glass hud-glow px-10 py-5 flex items-center justify-between gap-10">

    <a href="{{ url('/') }}"
       class="text-cyan-400 text-xl font-bold tracking-widest hover:text-cyan-300 transition">
        MYTORRENTS
    </a>

    <nav class="flex items-center gap-6 text-sm">
        <a href="{{ route('movies.index') }}" class="nav-link">🎬 Фильмы</a>
        <a href="{{ route('series.index') }}" class="nav-link">📺 Сериалы</a>
        <a href="{{ route('movies.index', ['category' => 'cartoon']) }}" class="nav-link">🧸 Мультфильмы</a>
        <a href="{{ route('movies.index', ['category' => 'documentary']) }}" class="nav-link">📚 Документальные</a>
        <a href="{{ route('movies.index', ['category' => 'anime']) }}" class="nav-link">🌸 Аниме</a>

        @auth
        <a href="{{ route('favorites.index') }}"
           class="relative text-rose-400 hover:text-rose-300 transition flex items-center gap-1">
            ❤️ Избранное
            <span id="favoritesCounter"
                  class="ml-2 px-2.5 h-[22px] min-w-[22px]
                         inline-flex items-center justify-center
                         rounded-full text-xs font-semibold
                         bg-rose-500/25 text-rose-300">
                {{ $favoritesCount }}
            </span>
        </a>
        @endauth
    </nav>

    <div class="flex gap-4 items-center">
        @auth
            <a href="{{ route('account.profile') }}"
               class="px-4 py-2 rounded-xl border border-emerald-400/60 text-emerald-300">
                Личный кабинет
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
</header>

<main class="px-10 py-16 max-w-7xl mx-auto">

@if($latestMovies->count())
<section class="mb-24">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-slate-100">
            🔥 Новые релизы
        </h2>

        <a href="{{ route('movies.index') }}"
           class="text-sm text-cyan-400 hover:underline">
            Все →
        </a>
    </div>

    <div class="flex gap-6 overflow-x-auto pb-6 scrollbar-hide">
        @foreach($latestMovies as $movie)
            <div class="relative min-w-[220px] max-w-[220px] group">

                @auth
                <button
                    class="favorite-btn absolute top-3 right-3 z-10
                           w-9 h-9 rounded-full
                           bg-black/60 backdrop-blur
                           flex items-center justify-center
                           {{ ($movie->is_favorited ?? false) ? 'is-active' : '' }}"
                    data-movie-id="{{ $movie->id }}">
                    ♥
                </button>
                @endauth

                <a href="{{ route('movies.watch', $movie) }}"
                   class="block rounded-[22px] overflow-hidden group-hover:-translate-y-2 transition">

                    <div class="relative h-[320px] bg-black">
                        <img src="{{ $movie->poster_url }}"
                             class="absolute inset-0 w-full h-full object-cover">
                    </div>

                </a>
            </div>
        @endforeach
    </div>
</section>
@endif

</main>

<footer class="text-center text-xs text-slate-500 py-12">
    © {{ date('Y') }} MyTorrents · Мы не храним контент · 18+
</footer>

<script>
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.favorite-btn');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();

    const movieId = btn.dataset.movieId;
    const token = document.querySelector('meta[name="csrf-token"]').content;

    btn.classList.toggle('is-active');

    try {
        const res = await fetch(`/favorites/movie/${movieId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
        });

        const data = await res.json();

        if (!res.ok) {
            btn.classList.toggle('is-active');
            alert(data.message || 'Ошибка');
            return;
        }

        const counter = document.getElementById('favoritesCounter');
        if (counter) {
            counter.textContent = data.count;
            counter.classList.add('animate-pulse');
            setTimeout(() => counter.classList.remove('animate-pulse'), 400);
        }

    } catch (err) {
        btn.classList.toggle('is-active');
        alert('Ошибка соединения');
    }
});
</script>

</body>
</html>
