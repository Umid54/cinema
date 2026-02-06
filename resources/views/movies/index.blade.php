@extends('layouts.user-hud')

@section('title', $title ?? 'Фильмы')

@section('content')

{{-- BREADCRUMBS --}}
<div class="text-sm text-slate-400 mb-6">
    <a href="{{ route('home') }}" class="hover:text-cyan-300">Главная</a>
    <span class="mx-2">/</span>
    <a href="{{ route('movies.index') }}" class="hover:text-cyan-300">Фильмы</a>

    @isset($title)
        <span class="mx-2">/</span>
        <span class="text-slate-300">{{ $title }}</span>
    @endisset
</div>

<h1 class="text-4xl font-bold text-cyan-400 mb-12 tracking-widest">
    {{ mb_strtoupper($title ?? 'Фильмы') }}
</h1>

{{-- GUEST NOTICE --}}
@guest
    <div class="hud-glass hud-glow p-8 rounded-2xl mb-12 text-center max-w-2xl mx-auto">
        <p class="text-slate-300 mb-6">
            Для просмотра фильмов необходимо войти в аккаунт
        </p>
        <a href="{{ route('login') }}"
           class="inline-block px-8 py-3 rounded-xl
                  border border-cyan-400/60 text-cyan-300
                  hover:shadow-[0_0_30px_rgba(34,211,238,.6)] transition">
            Войти
        </a>
    </div>
@endguest

{{-- MOVIES GRID --}}
<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-8">

@forelse($movies as $movie)

@php
    $video = $movie->video ?? null;

    $canPreview =
        auth()->check() &&
        $video &&
        $video->type === 'mp4' &&
        $video->path;

    $isReady = $movie->status === 'ready';
    $isProcessing = $movie->status === 'processing';
    $isBlocked = in_array($movie->status, ['draft', 'blocked']);
@endphp

<div class="group relative block {{ $isReady ? '' : 'pointer-events-none opacity-70' }}">

    @if($isReady)
        <a href="{{ route('movies.watch', $movie) }}" class="absolute inset-0 z-10"></a>
    @endif

    <div class="relative rounded-[24px] overflow-hidden bg-black
                border border-cyan-400/20 transition duration-300
                group-hover:-translate-y-2
                group-hover:shadow-[0_0_45px_rgba(34,211,238,.45)]">

        {{-- FAVORITE --}}
        @auth
            <form method="POST"
                  action="{{ route('favorites.toggle', $movie) }}"
                  class="absolute top-3 right-3 z-30"
                  onclick="event.stopPropagation()">
                @csrf
                <button
                    class="text-lg transition
                    {{ auth()->user()->hasFavorited($movie)
                        ? 'text-rose-400'
                        : 'text-white/60 hover:text-rose-300' }}">
                    ♥
                </button>
            </form>
        @endauth

        {{-- RATINGS --}}
        <div class="absolute top-3 left-3 z-30 flex flex-col gap-1">
            @if($movie->rating_kp)
                <span class="px-2 py-0.5 rounded-md bg-black/60 backdrop-blur
                             text-[11px] font-semibold text-emerald-400">
                    КП {{ $movie->rating_kp }}
                </span>
            @endif

            @if($movie->rating_imdb)
                <span class="px-2 py-0.5 rounded-md bg-black/60 backdrop-blur
                             text-[11px] font-semibold text-amber-300">
                    IMDb {{ $movie->rating_imdb }}
                </span>
            @endif
        </div>

        {{-- MEDIA --}}
        <div class="relative w-full aspect-[2/3]">

            <div class="absolute inset-0 skeleton"></div>

            <img
                src="{{ $movie->poster_url }}"
                alt="{{ $movie->title }}"
                loading="lazy"
                onload="this.previousElementSibling.remove()"
                class="absolute inset-0 w-full h-full object-cover
                       transition-opacity duration-300
                       {{ $canPreview && $isReady ? 'group-hover:opacity-0' : '' }}">

            @if($canPreview && $isReady)
                <video
                    class="absolute inset-0 w-full h-full object-cover
                           opacity-0 group-hover:opacity-100
                           transition-opacity duration-300"
                    muted loop playsinline preload="none"
                    onmouseenter="this.play()"
                    onmouseleave="this.pause(); this.currentTime = 0;">
                    <source src="{{ asset($video->path) }}" type="video/mp4">
                </video>
            @endif

            <div class="absolute inset-0 bg-black/40"></div>

            @if($isProcessing)
                <div class="absolute inset-0 flex items-center justify-center z-20">
                    <span class="px-4 py-2 rounded-xl bg-black/70 text-cyan-300 text-sm">
                        🎬 Конвертация…
                    </span>
                </div>
            @elseif($isBlocked)
                <div class="absolute inset-0 flex items-center justify-center z-20">
                    <span class="px-4 py-2 rounded-xl bg-black/70 text-rose-400 text-sm">
                        ⛔ Недоступно
                    </span>
                </div>
            @endif

            @if($isReady)
                <div class="absolute inset-0 flex items-center justify-center
                            pointer-events-none opacity-0 group-hover:opacity-100 transition z-20">
                    <div class="w-16 h-16 rounded-full bg-cyan-400/10 backdrop-blur
                                border border-cyan-400/60 flex items-center justify-center
                                shadow-[0_0_25px_rgba(34,211,238,.6)]">
                        <svg class="w-8 h-8 text-cyan-300 ml-1"
                             viewBox="0 0 24 24" fill="currentColor">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>
            @endif

            <div class="absolute bottom-3 right-3 text-[11px]
                        px-2 py-0.5 rounded-md bg-black/60 text-slate-300 z-30">
                18+
            </div>

        </div>
    </div>
</div>

@empty
    <div class="col-span-full text-center text-slate-400 text-lg">
        Фильмы не найдены
    </div>
@endforelse

</div>

@if(method_exists($movies, 'links'))
    <div class="mt-16">
        {{ $movies->links() }}
    </div>
@endif

@endsection
