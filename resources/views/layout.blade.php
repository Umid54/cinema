<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'MYTORRENTS' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen
             bg-gradient-to-b from-[#050b1a] via-[#060d22] to-[#020713]
             text-slate-200 overflow-x-hidden">

{{-- Header --}}
<header class="bg-black/90 backdrop-blur border-b border-cyan-400/20 text-white">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">

        {{-- Logo --}}
        <a href="{{ url('/') }}"
           class="text-xl font-bold tracking-wide text-cyan-300 hover:text-cyan-200 transition">
            🎬 MYTORRENTS
        </a>

        {{-- Navigation --}}
        <div class="flex items-center gap-6 text-sm">

            <nav class="flex gap-4">
                <a href="{{ route('movies.index') }}"
                   class="hover:text-cyan-300 transition">
                    Каталог
                </a>

                @auth
                    @if(method_exists(auth()->user(), 'hasRole') && auth()->user()->hasRole('admin'))
                        <a href="{{ route('admin.movies.index') }}"
                           class="hover:text-cyan-300 transition">
                            Админка
                        </a>
                    @endif
                @endauth
            </nav>

            {{-- Premium / FREE --}}
            @auth
                @if(auth()->user()->is_premium && auth()->user()->premium_until)
                    <span class="px-3 py-1 rounded-full
                                 bg-gradient-to-r from-amber-400 to-yellow-500
                                 text-black text-xs font-semibold">
                        👑 PREMIUM
                    </span>
                @else
                    <a href="{{ route('premium.index') }}"
                       class="px-4 py-1.5 rounded-lg
                              bg-gradient-to-r from-cyan-400 to-emerald-400
                              text-black text-xs font-semibold
                              hover:shadow-[0_0_20px_rgba(34,211,238,0.6)]
                              transition">
                        Оформить Premium
                    </a>
                @endif
            @endauth

        </div>
    </div>
</header>

{{-- Content --}}
<main class="py-10 bg-transparent">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="bg-black/80 border-t border-cyan-400/10 text-slate-500 text-sm">
    <div class="max-w-7xl mx-auto px-4 py-6 text-center">
        © {{ date('Y') }} MYTORRENTS. Все права защищены.
    </div>
</footer>

{{-- Player JS --}}
<script>
function loadPlayer(html) {
    const player = document.getElementById('player');
    if (player) {
        player.innerHTML = html;
    }
}

function showSeason(id) {
    document.querySelectorAll('.season-block').forEach(el => {
        el.style.display = el.dataset.season == id ? 'block' : 'none';
    });
}
</script>

</body>
</html>
