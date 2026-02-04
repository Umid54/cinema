@extends('layouts.user-hud')

@section('title', $movie->title)

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/plyr@3.7.8/dist/plyr.css">

<style>
/* 🔥 FORCE PLYR CONTROLS (HUD FIX) */
.plyr {
    --plyr-color-main: #22d3ee;
}
.plyr__controls {
    display: flex !important;
    opacity: 1 !important;
    pointer-events: auto !important;
    z-index: 30;
}
</style>
@endpush

@section('content')

<div class="max-w-6xl mx-auto">

    {{-- TITLE --}}
    <h1 class="text-3xl font-bold text-cyan-400 mb-6 tracking-widest">
        {{ $movie->title }}
    </h1>

    {{-- META --}}
    <div class="flex flex-wrap items-center gap-4 text-sm text-slate-400 mb-6">
        @if($movie->year)
            <span>{{ $movie->year }}</span>
        @endif
        @if($movie->rating)
            <span class="text-emerald-400">★ {{ $movie->rating }}</span>
        @endif
        @if($movie->duration)
            <span>{{ $movie->duration }} мин</span>
        @endif
    </div>

    {{-- PLAYER / ACCESS --}}
    @if(!$canWatch)

        <div class="hud-glass rounded-2xl p-10 mb-10 text-center">
            <div class="text-3xl mb-4">🔒</div>
            <div class="text-xl text-cyan-300 font-semibold mb-2">
                Просмотр доступен по подписке
            </div>
            <div class="text-slate-400 mb-6">
                Оформите Premium, чтобы смотреть фильмы без ограничений
            </div>

            <a href="{{ route('premium.index') }}"
               class="btn btn-sky px-8 py-3 text-lg">
                Получить Premium
            </a>
        </div>

    @elseif($movie->status === 'ready' && $movie->hls_path)

  <div class="player-container mb-10">
    <div class="hud-glass hud-glow player-shell rounded-2xl overflow-hidden">
        <video
            id="player"
            playsinline
            controls
            crossorigin
        ></video>
    </div>
</div>





                {{-- 🔒 OVERLAY ТОЛЬКО ЕСЛИ НЕТ ДОСТУПА --}}
                @if(!auth()->check() || !auth()->user()?->is_premium)
                    <div class="absolute inset-0 z-40
                                flex items-center justify-center
                                bg-black/60 backdrop-blur-sm
                                cursor-pointer"
                         onclick="openHudModal('{{ auth()->check() ? 'premiumModal' : 'loginModal' }}')">
                        <div class="text-center">
                            <div class="text-5xl mb-4">🔒</div>
                            <div class="text-lg text-cyan-300 font-semibold">
                                Доступ ограничен
                            </div>
                            <div class="text-sm text-slate-400 mt-2">
                                {{ auth()->check() ? 'Требуется Premium' : 'Войдите для просмотра' }}
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>

    @elseif($movie->status === 'processing')

        <div class="hud-glass rounded-2xl mb-8 min-h-[300px]
                    flex items-center justify-center
                    text-cyan-300 text-lg">
            🎬 Видео конвертируется…
        </div>

    @elseif($movie->status === 'draft')

        <div class="hud-glass rounded-2xl mb-8 min-h-[300px]
                    flex items-center justify-center
                    text-slate-400 text-lg">
            ⏳ Видео ещё не опубликовано
        </div>

    @else

        <div class="hud-glass rounded-2xl mb-8 min-h-[300px]
                    flex items-center justify-center
                    text-rose-400 text-lg">
            ⛔ Видео недоступно
        </div>

    @endif

    {{-- DESCRIPTION --}}
    @if($movie->description)
        <div class="hud-glass p-6 rounded-2xl mb-12 text-slate-300 leading-relaxed">
            {{ $movie->description }}
        </div>
    @endif

    {{-- SCREENSHOTS — СТРОГО ПОСЛЕ ОПИСАНИЯ --}}
    @if($movie->screenshots->count())
        <div class="mt-10">

            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl
                            bg-cyan-400/10 text-cyan-300
                            flex items-center justify-center
                            shadow-[0_0_20px_rgba(34,211,238,.4)]">
                    📸
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-cyan-300 tracking-wide">
                        Кадры из фильма
                    </h2>
                    <p class="text-sm text-slate-400">
                       {{-- Атмосфера и визуальный стиль--}}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                @foreach($movie->screenshots as $i => $shot)
                    <button
                        type="button"
                        class="screenshot-thumb cursor-zoom-in group relative
                               w-full overflow-hidden rounded-2xl
                               border border-neutral-800 bg-black
                               hover:border-cyan-400/60
                               hover:shadow-[0_0_30px_rgba(34,211,238,.35)]
                               transition-all duration-300"
                        data-index="{{ $i }}"
                        data-src="{{ asset('storage/'.$shot->path) }}"
                    >
                        <div class="relative w-full" style="padding-top:56.25%;">
                            <img
                                src="{{ asset('storage/'.$shot->path) }}"
                                class="absolute inset-0 w-full h-full object-cover
                                       transition-transform duration-500
                                       group-hover:scale-105"
                                draggable="false"
                                alt="Screenshot {{ $i + 1 }}">
                        </div>

                        <div class="absolute inset-0
                                    bg-black/0 group-hover:bg-black/40
                                    flex items-center justify-center
                                    opacity-0 group-hover:opacity-100
                                    transition">
                            <span class="text-white text-sm tracking-wide">
                                Смотреть
                            </span>
                        </div>
                    </button>
                @endforeach
            </div>

        </div>
    @endif

</div>
{{-- ===== GALLERY MODAL ===== --}}
<div id="galleryModal"
     class="fixed inset-0 z-50 hidden
            bg-black/80 backdrop-blur-md
            flex items-center justify-center">

    <button id="galleryClose"
            class="absolute top-6 right-6
                   hud-glass rounded-full w-12 h-12
                   flex items-center justify-center
                   text-white text-3xl
                   hover:text-cyan-300 transition">
        &times;
    </button>

    <button id="galleryPrev"
            class="absolute left-6
                   hud-glass rounded-full w-14 h-14
                   flex items-center justify-center
                   text-white text-4xl
                   hover:text-cyan-300 transition">
        &#10094;
    </button>

    <img id="galleryImage"
         class="max-h-[90vh] max-w-[90vw]
                rounded-2xl hud-glow select-none">

    <button id="galleryNext"
            class="absolute right-6
                   hud-glass rounded-full w-14 h-14
                   flex items-center justify-center
                   text-white text-4xl
                   hover:text-cyan-300 transition">
        &#10095;
    </button>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.8"></script>
<script src="https://cdn.jsdelivr.net/npm/plyr@3.7.8/dist/plyr.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('player');
    if (!video) return;

    const source = @json(route('movies.stream', [
        'movie' => $movie->id,
        'file'  => 'master.m3u8'
    ]));

    new Plyr(video, {
        controls: [
            'play-large',
            'play',
            'progress',
            'current-time',
            'mute',
            'volume',
            'settings',
            'fullscreen'
        ],
    });

    if (Hls.isSupported()) {
        const hls = new Hls();
        hls.loadSource(source);
        hls.attachMedia(video);
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = source;
    }
});
</script>
@endpush
