@extends('layouts.user-hud')

@section('title', $title ?? 'Сериалы')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10">

    {{-- BREADCRUMBS --}}
    <div class="text-sm text-neutral-400 mb-6">
        <a href="{{ route('home') }}" class="hover:text-cyan-300">Главная</a>
        <span class="mx-2">/</span>
        <span class="text-neutral-300">Сериалы</span>

        @isset($title)
            <span class="mx-2">/</span>
            <span class="text-neutral-300">{{ $title }}</span>
        @endisset
    </div>

    {{-- TITLE --}}
    <h1 class="text-3xl font-bold text-cyan-300 mb-8 tracking-wide">
        {{ mb_strtoupper($title ?? 'Сериалы') }}
    </h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        @forelse ($series as $item)

            @php
                $user = auth()->user();
                $isPremium = $user && ($user->is_premium_active || $user->is_trial);
                $isAdmin = $user && method_exists($user, 'isAdmin') && $user->isAdmin();
            @endphp

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
                              class="absolute top-3 right-3 z-20"
                              onclick="event.stopPropagation()">
                            @csrf
                            <button
                                type="submit"
                                class="text-2xl transition
                                {{ $item->is_favorited
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

                    @if($item->poster_url)
                        <img
                            src="{{ $item->poster_url }}"
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

                    {{-- ▶ ПРОДОЛЖИТЬ (PREMIUM / TRIAL ONLY) --}}
                    @auth
                        @if($isPremium && $item->watchProgress)
                            <a
                                href="{{ route('series.watch', [
                                    'series'  => $item->id,
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
                                            shadow-[0_0_25px_rgba(52,211,153,.6)]
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
                        @if($isPremium)
                            @if($item->watchProgress)
                                <a
                                    href="{{ route('series.watch', [
                                        'series'  => $item->id,
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
                                        'series'  => $item->id,
                                        'season'  => 1,
                                        'episode' => 1,
                                    ]) }}"
                                    class="btn btn-sky w-full text-center"
                                >
                                    ▶ Смотреть
                                </a>
                            @endif
                        @else
                            <a
                                href="{{ route('series.watch', [
                                    'series'  => $item->id,
                                    'season'  => 1,
                                    'episode' => 1,
                                ]) }}"
                                class="btn btn-sky w-full text-center"
                            >
                                ▶ Смотреть
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}"
                           class="btn btn-sky w-full text-center">
                            🔒 Войти
                        </a>
                    @endauth

                </div>
            </div>

        @empty
            <div class="col-span-full text-center text-neutral-500 py-20">
                Сериалы не найдены
            </div>
        @endforelse

    </div>

    {{-- PAGINATION --}}
    @if(method_exists($series, 'links'))
        <div class="mt-16">
            {{ $series->links() }}
        </div>
    @endif

</div>
@endsection
