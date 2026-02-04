@extends('layouts.user-hud')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10">

    <h1 class="text-3xl font-bold text-cyan-300 mb-8 tracking-wide">
        🎬 Сериалы
    </h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        @forelse ($series as $item)
            <div
                class="relative bg-neutral-900/60 backdrop-blur
                       border border-neutral-800 rounded-xl
                       overflow-hidden group"
                data-spotlight
            >

                {{-- ❤️ FAVORITE (PREMIUM ONLY) --}}
                @auth
                    @if($isPremium)
                        <form method="POST"
                              action="{{ route('favorites.toggle', ['movie' => $item->id]) }}"
                              class="absolute top-3 right-3 z-20">
                            @csrf
                            <button
                                type="submit"
                                class="text-2xl transition
                                {{ auth()->user()->hasFavorited($item)
                                    ? 'text-rose-400'
                                    : 'text-neutral-400 hover:text-rose-300' }}"
                                title="В избранное"
                            >
                                ♥
                            </button>
                        </form>
                    @endif
                @endauth

                {{-- POSTER --}}
                <div class="relative aspect-[2/3] bg-neutral-800 overflow-hidden">

                    @if($item->poster)
                        <img
                            src="{{ $item->poster }}"
                            alt="{{ $item->title }}"
                            class="w-full h-full object-cover
                                   group-hover:scale-105 transition"
                        >
                    @else
                        <div class="w-full h-full flex items-center justify-center
                                    text-neutral-500 text-sm">
                            Нет постера
                        </div>
                    @endif

                    {{-- ▶ ПРОДОЛЖИТЬ (OVERLAY, PREMIUM / TRIAL ONLY) --}}
                    @auth
                        @if($isPremium && $item->watchProgress)
                            <a
                                href="{{ route('series.watch', [
                                    'movie'   => $item->id,
                                    'season'  => $item->watchProgress->season,
                                    'episode' => $item->watchProgress->episode,
                                ]) }}"
                                class="absolute inset-0 z-10
                                       flex items-center justify-center
                                       bg-black/60 backdrop-blur-sm
                                       opacity-0 group-hover:opacity-100
                                       transition"
                            >
                                <div class="px-6 py-3 rounded-full
                                            bg-emerald-500/20
                                            border border-emerald-400/40
                                            text-emerald-300 font-semibold
                                            shadow-[0_0_25px_rgba(52,211,153,0.6)]
                                            text-center">
                                    ▶ Продолжить
                                    <span class="block text-xs opacity-70">
                                        S{{ $item->watchProgress->season }}
                                        · E{{ $item->watchProgress->episode }}
                                    </span>
                                </div>
                            </a>
                        @endif
                    @endauth

                </div>

                {{-- INFO --}}
                <div class="p-4 space-y-1">
                    <div class="font-semibold text-white truncate">
                        {{ $item->title }}
                    </div>

                    <div class="text-xs text-neutral-400">
                        {{ $item->year ?? '—' }}
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="p-4 pt-0 flex gap-2">

                    @auth
                        {{-- 👑 PREMIUM / TRIAL --}}
                        @if($isPremium)
                            @if($item->watchProgress)
                                <a
                                    href="{{ route('series.watch', [
                                        'movie'   => $item->id,
                                        'season'  => $item->watchProgress->season,
                                        'episode' => $item->watchProgress->episode,
                                    ]) }}"
                                    class="btn btn-emerald w-full text-center"
                                >
                                    ▶ Продолжить
                                </a>
                            @else
                                <a
                                    href="{{ route('series.watch', [
                                        'movie'   => $item->id,
                                        'season'  => 1,
                                        'episode' => 1,
                                    ]) }}"
                                    class="btn btn-sky w-full text-center"
                                >
                                    ▶ Смотреть
                                </a>
                            @endif
                        @else
                            {{-- FREE --}}
                            <a
                                href="{{ route('series.watch', [
                                    'movie'   => $item->id,
                                    'season'  => 1,
                                    'episode' => 1,
                                ]) }}"
                                class="btn btn-sky w-full text-center"
                            >
                                ▶ Смотреть
                            </a>
                        @endif
                    @else
                        {{-- GUEST --}}
                        <a
                            href="{{ route('login') }}"
                            class="btn btn-sky w-full text-center"
                        >
                            🔒 Войти
                        </a>
                    @endauth

                    {{-- ADMIN --}}
                    @if($isAdmin && Route::has('admin.series.edit'))
                        <a
                            href="{{ route('admin.series.edit', $item->id) }}"
                            class="btn btn-ghost-white px-3"
                            title="Редактировать"
                        >
                            ✏️
                        </a>
                    @endif

                </div>
            </div>
        @empty
            <div class="col-span-full text-center text-neutral-500 py-20">
                Сериалы не найдены
            </div>
        @endforelse

    </div>
</div>
@endsection
