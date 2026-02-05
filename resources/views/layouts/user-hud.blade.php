<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Личный кабинет')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/plyr@3.7.8/dist/plyr.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- 🔐 AUTH FLAGS FOR JS --}}
    <script>
        window.AUTH = {
            logged: @json(auth()->check()),
            premium: @json(auth()->check() && auth()->user()?->is_premium),
        };
    </script>

    <style>
        body {
            background:
                radial-gradient(1200px 600px at 10% -10%, rgba(34,211,238,.30), transparent 45%),
                radial-gradient(900px 500px at 90% 10%, rgba(52,211,153,.28), transparent 45%),
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

        .hud-card {
            background: rgba(2,6,23,.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(148,163,184,.25);
            border-radius: 1rem;
            position: relative;
        }

        /* ❤️ HEART STATES */
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

{{-- HEADER --}}
<header class="hud-glass hud-glow px-10 py-6 flex justify-between items-center">

    <a href="{{ url('/') }}"
       class="text-cyan-400 text-xl font-bold tracking-widest hover:text-cyan-300 transition">
        MYTORRENTS
    </a>

    <nav class="flex items-center gap-6 text-sm">
        <a href="{{ route('movies.index') }}"
           class="text-slate-300 hover:text-cyan-300 transition">Фильмы</a>

        <a href="{{ route('series.index') }}"
           class="text-slate-300 hover:text-cyan-300 transition">Сериалы</a>

        @auth
            <a href="{{ route('favorites.index') }}"
               class="relative text-rose-400 hover:text-rose-300 transition flex items-center gap-1">
                ❤️ Избранное
                <span id="favoritesCounter"
                      class="ml-2 px-2 min-w-[22px] h-[22px]
                             inline-flex items-center justify-center
                             rounded-full text-xs
                             bg-rose-500/25 text-rose-300">
                    {{ $favoritesCount ?? 0 }}
                </span>
            </a>

            <a href="{{ route('account.index') }}"
               class="text-emerald-400 hover:text-emerald-300 transition">
                👤 Личный кабинет
            </a>
        @endauth
    </nav>

    <div class="flex items-center gap-6 text-sm">
        @auth
            <span class="text-slate-400">{{ auth()->user()->email }}</span>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-rose-400 hover:text-rose-300 transition">
                    ⏻ Выйти
                </button>
            </form>
        @endauth

        @guest
            <a href="{{ route('login') }}"
               class="text-cyan-400 hover:text-cyan-300 transition">
                Войти
            </a>
        @endguest
    </div>
</header>

{{-- FLASH --}}
@if(session('flash'))
    @php
        $type = session('flash.type');
        $colors = [
            'success' => 'border-emerald-400/60 text-emerald-300 bg-emerald-400/10',
            'info'    => 'border-cyan-400/60 text-cyan-300 bg-cyan-400/10',
            'error'   => 'border-rose-400/60 text-rose-300 bg-rose-400/10',
        ];
    @endphp

    <div class="max-w-3xl mx-auto mt-6 px-6 py-4 rounded-xl
                border {{ $colors[$type] ?? $colors['info'] }}">
        {{ session('flash.message') }}
    </div>
@endif

<main class="px-10 py-12 max-w-7xl mx-auto">
    @yield('content')
</main>

<footer class="text-center text-xs text-slate-500 py-10">
    © {{ date('Y') }} MyTorrents · Пользовательский кабинет
</footer>

{{-- ================= HUD MODALS ================= --}}

{{-- LOGIN --}}
<div id="loginModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur">
    <div class="hud-card max-w-md w-full p-6">
        <h2 class="text-xl font-bold text-cyan-400 mb-4">🔒 Требуется вход</h2>
        <p class="text-neutral-300 mb-6">
            Чтобы смотреть видео, войдите или зарегистрируйтесь.
        </p>

        <div class="flex gap-4">
            <a href="{{ route('login') }}" class="btn btn-cyan w-full text-center">
                Войти
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline w-full text-center">
                Регистрация
            </a>
        </div>

        <button onclick="closeHudModal('loginModal')"
                class="absolute top-3 right-3 text-neutral-400 hover:text-white">✕</button>
    </div>
</div>

{{-- PREMIUM --}}
<div id="premiumModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/80 backdrop-blur">
    <div class="hud-card max-w-md w-full p-6">
        <h2 class="text-xl font-bold text-yellow-400 mb-4">⭐ Premium доступ</h2>
        <p class="text-neutral-300 mb-6">
            Этот фильм доступен только для Premium-пользователей.
        </p>

        <a href="{{ route('premium.index') }}"
           class="btn btn-yellow w-full text-center">
            Оформить Premium
        </a>

        <button onclick="closeHudModal('premiumModal')"
                class="absolute top-3 right-3 text-neutral-400 hover:text-white">✕</button>
    </div>
</div>

{{-- HUD HELPERS --}}
<script>
function openHudModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('hidden');
    el.classList.add('flex');
}

function closeHudModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('hidden');
    el.classList.remove('flex');
}
</script>

{{-- ❤️ FAVORITES AJAX --}}
<script>
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.favorite-btn');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();

    const movieId = btn.dataset.movieId;
    if (!movieId) return;

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

        if (data.status === 'removed') {
            const card = btn.closest('[data-favorite-card]');
            if (card) {
                card.style.opacity = '0';
                setTimeout(() => card.remove(), 200);
            }
        }

        const counter = document.getElementById('favoritesCounter');
        if (counter && typeof data.count !== 'undefined') {
            counter.textContent = data.count;
            counter.classList.add('animate-pulse');
            setTimeout(() => counter.classList.remove('animate-pulse'), 400);
        }

    } catch (err) {
        console.error(err);
        btn.classList.toggle('is-active');
        alert('Ошибка соединения');
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/plyr@3.7.8/dist/plyr.polyfilled.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.15/dist/hls.min.js"></script>
<script src="{{ asset('js/gallery.js') }}"></script>

@stack('scripts')
</body>
</html>
