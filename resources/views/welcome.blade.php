<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>MyTorrents</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

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

        /* ===== SKELETON ===== */
        .skeleton {
            background: linear-gradient(
                100deg,
                rgba(255,255,255,0.06) 40%,
                rgba(255,255,255,0.14) 50%,
                rgba(255,255,255,0.06) 60%
            );
            background-size: 200% 100%;
            animation: skeleton 1.4s ease infinite;
        }

        @keyframes skeleton {
            to { background-position-x: -200%; }
        }
    </style>
</head>

<body class="min-h-screen">

{{-- HEADER --}}
<header class="hud-glass hud-glow px-10 py-6 flex justify-between items-center">
    <div class="text-cyan-400 text-xl font-bold tracking-widest">
        MYTORRENTS
    </div>

    <div class="flex gap-4 items-center">
        @auth
            <a href="{{ route('dashboard') }}"
               class="px-5 py-2 rounded-xl border border-emerald-400/60 text-emerald-300
                      hover:shadow-[0_0_25px_rgba(52,211,153,.6)] transition">
                Кабинет
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    class="px-5 py-2 rounded-xl border border-rose-400/60 text-rose-300
                           hover:shadow-[0_0_25px_rgba(248,113,113,.6)] transition">
                    Выйти
                </button>
            </form>
        @else
            <a href="{{ route('login') }}"
               class="px-5 py-2 rounded-xl border border-cyan-400/60 text-cyan-300
                      hover:shadow-[0_0_25px_rgba(34,211,238,.6)] transition">
                Войти
            </a>

            @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="px-5 py-2 rounded-xl border border-emerald-400/60 text-emerald-300
                          hover:shadow-[0_0_25px_rgba(52,211,153,.6)] transition">
                    Регистрация
                </a>
            @endif
        @endauth
    </div>
</header>

{{-- MAIN --}}
<main class="px-10 py-16 max-w-7xl mx-auto">

    <h1 class="text-3xl font-bold text-center text-cyan-400 mb-12 tracking-widest">
        КАТЕГОРИИ КОНТЕНТА
    </h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

        @foreach ([
            'movies' => ['Фильмы', '🎬'],
            'series' => ['Сериалы', '📺'],
            'anime'  => ['Аниме', '✨'],
            'cartoon'=> ['Мультфильмы', '🐭'],
        ] as $key => [$title, $icon])

            <div class="hud-glass hud-glow p-8 rounded-2xl text-center transition">

                <div class="text-5xl mb-4">{{ $icon }}</div>

                <div class="text-lg font-semibold text-slate-200">
                    {{ $title }}
                </div>

                <div class="text-sm text-slate-400 mt-2 mb-6">
                    Смотреть онлайн
                </div>

                @auth
                    <a href="/{{ $key }}"
                       class="inline-block px-4 py-2 rounded-lg
                              border border-cyan-400/50 text-cyan-300
                              hover:shadow-[0_0_20px_rgba(34,211,238,.5)] transition">
                        Открыть
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-block px-4 py-2 rounded-lg
                              border border-slate-500/40 text-slate-300
                              hover:border-cyan-400/60 hover:text-cyan-300 transition">
                        Войти для просмотра
                    </a>
                @endauth

            </div>

        @endforeach

    </div>

    {{-- GUEST NOTICE --}}
    @guest
        <div class="mt-16 text-center hud-glass hud-glow p-8 rounded-2xl max-w-3xl mx-auto">
            <p class="text-slate-300 mb-6">
                Для просмотра фильмов и сериалов необходимо войти в аккаунт
            </p>

            <a href="{{ route('login') }}"
               class="px-6 py-3 rounded-xl border border-cyan-400/60 text-cyan-300
                      hover:shadow-[0_0_30px_rgba(34,211,238,.6)] transition">
                Войти
            </a>
        </div>
    @endguest

</main>

{{-- FOOTER --}}
<footer class="text-center text-xs text-slate-500 py-10">
    © {{ date('Y') }} MyTorrents · Мы не храним контент · 18+
</footer>

</body>
</html>
