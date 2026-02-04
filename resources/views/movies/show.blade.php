@extends('layouts.user-hud')

@section('title', $movie->title)

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- TOP INFO --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-12">

        {{-- POSTER --}}
        <div class="hud-glass hud-glow rounded-2xl overflow-hidden relative">

            {{-- ❤️ FAVORITE --}}
            @auth
                <form method="POST"
                      action="{{ route('favorites.toggle', ['movie' => $movie->id]) }}"
                      class="absolute top-4 right-4 z-10">
                    @csrf
                    <button
                        type="submit"
                        class="text-2xl transition
                        {{ auth()->user()->hasFavorited($movie)
                            ? 'text-rose-400'
                            : 'text-slate-400 hover:text-rose-300' }}">
                        ♥
                    </button>
                </form>
            @endauth

            <img
                src="{{ $movie->poster_url }}"
                alt="{{ $movie->title }}"
                class="w-full h-full object-cover select-none pointer-events-none"
                draggable="false">
        </div>

        {{-- TITLE --}}
        <div class="md:col-span-2">
            <h1 class="text-3xl font-bold text-cyan-400 tracking-widest mb-3">
                {{ $movie->title }}
            </h1>

            <div class="text-slate-400 text-sm mb-6">
                {{ $movie->year }}
                @if($movie->duration) • {{ gmdate('H:i:s', $movie->duration * 60) }} @endif
                • ⭐ {{ $movie->rating ?? '—' }}
            </div>
        </div>
    </div>

    {{-- INFO --}}
    <div class="hud-glass hud-glow rounded-2xl p-6 mb-12 text-sm text-slate-300">
        <div class="space-y-3">

            @if($movie->countries->count())
                <div><span class="text-slate-400">Страна:</span> {{ $movie->countries->pluck('name')->join(', ') }}</div>
            @endif

            @if($movie->genres->count())
                <div><span class="text-slate-400">Жанр:</span> {{ $movie->genres->pluck('name')->join(', ') }}</div>
            @endif

            <div><span class="text-slate-400">Год выпуска:</span> {{ $movie->year }}</div>

            @if($movie->duration)
                <div><span class="text-slate-400">Продолжительность:</span> {{ gmdate('H:i:s', $movie->duration * 60) }}</div>
            @endif
        </div>

        @if($movie->description)
            <div class="mt-6 pt-4 border-t border-slate-700/50 leading-relaxed">
                <span class="text-slate-400 block mb-2">Описание:</span>
                {{ $movie->description }}
            </div>
        @endif
    </div>

    {{-- SCREENSHOTS --}}
    @if($movie->screenshots->count())
        <div class="mb-12">
            <h2 class="text-xl text-cyan-400 tracking-widest mb-4">Скриншоты</h2>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($movie->screenshots as $i => $shot)
                    <div
                        class="rounded-xl overflow-hidden cursor-pointer hover:opacity-80 transition"
                        onclick="openGallery({{ $i }})">

                        <img
                            src="{{ asset('storage/'.$shot->path) }}"
                            class="w-full h-full object-cover select-none pointer-events-none"
                            draggable="false">
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- PLAYER --}}
    @if($movie->status === 'ready' && $movie->hls_path)

        <div class="hud-glass hud-glow rounded-2xl overflow-hidden mb-12">
            <div class="aspect-video bg-black">
                <video id="hls-player"
                       class="w-full h-full"
                       controls
                       playsinline></video>
            </div>
        </div>

    @elseif($movie->status === 'processing')

        <div class="hud-glass rounded-2xl p-8 text-center text-cyan-300">
            🎬 Видео конвертируется…
        </div>

    @elseif($movie->status === 'draft')

        <div class="hud-glass rounded-2xl p-8 text-center text-slate-400">
            ⏳ Видео ещё не опубликовано
        </div>

    @else {{-- blocked --}}

        <div class="hud-glass rounded-2xl p-8 text-center text-rose-400">
            ⛔ Видео недоступно
        </div>

    @endif

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.8"></script>

@if($movie->status === 'ready' && $movie->hls_path)
<script>
document.addEventListener('DOMContentLoaded', () => {

    const video = document.getElementById('hls-player');
    const src = "{{ asset('storage/'.$movie->hls_path) }}";

    if (Hls.isSupported()) {
        const hls = new Hls({
            lowLatencyMode: false,
        });
        hls.loadSource(src);
        hls.attachMedia(video);
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = src;
    }

});
</script>
@endif
@endpush
