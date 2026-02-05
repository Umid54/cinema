@extends('layouts.user-hud')

@section('title', $movie->title)

@section('content')

<div class="max-w-6xl mx-auto px-4 sm:px-6">

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

        {{-- PLAYER + INFO --}}
        <div class="w-full mb-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                {{-- PLAYER --}}
                <div class="lg:col-span-2">
                    <div class="relative w-full overflow-hidden rounded-2xl border border-cyan-500/20 bg-black/40 hud-glow shadow-[0_0_60px_rgba(34,211,238,0.12)]"
                         style="aspect-ratio: 16 / 9; max-width: 900px;">

                        <video
                            id="player"
                            playsinline
                            controls
                            preload="metadata"
                            class="absolute inset-0 w-full h-full object-contain"
                        ></video>

                        {{-- 🔒 OVERLAY --}}
                        @if(!auth()->check() || !auth()->user()?->is_premium)
                            <div class="absolute inset-0 z-40 flex items-center justify-center bg-black/60 backdrop-blur-sm cursor-pointer"
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

                {{-- INFO PANEL --}}
                <div class="hud-glass rounded-2xl p-6 border border-white/5">

                    <div class="flex items-center justify-between mb-4">
                        <div class="text-cyan-300 font-semibold text-lg tracking-wide">
                            Информация
                        </div>

                        <div class="flex items-center gap-3 text-sm">
                            @if($movie->kp_rating)
                                <span class="px-3 py-1 rounded-xl bg-cyan-400/10 text-cyan-200 border border-cyan-400/20">
                                    КП: <b class="text-white">{{ $movie->kp_rating }}</b>
                                </span>
                            @endif

                            @if($movie->imdb_rating)
                                <span class="px-3 py-1 rounded-xl bg-emerald-400/10 text-emerald-200 border border-emerald-400/20">
                                    IMDb: <b class="text-white">{{ $movie->imdb_rating }}</b>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3 text-sm text-slate-300">

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Страна:</span>
                            <span class="text-right">{{ $movie->country ?? '—' }}</span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Жанр:</span>
                            <span class="text-right">{{ $movie->genre ?? '—' }}</span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Год выпуска:</span>
                            <span class="text-right">{{ $movie->year ?? '—' }}</span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Продолжительность:</span>
                            <span class="text-right">{{ $movie->duration ? $movie->duration.' мин' : '—' }}</span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Перевод:</span>
                            <span class="text-right">{{ $movie->translation ?? '—' }}</span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Субтитры:</span>
                            <span class="text-right">{{ $movie->subtitles ?? '—' }}</span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Оригинальная аудиодорожка:</span>
                            <span class="text-right">{{ $movie->original_audio ?? '—' }}</span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Режиссер:</span>
                            <span class="text-right">{{ $movie->director ?? '—' }}</span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">В ролях:</span>
                            <span class="text-right leading-relaxed">
                                {{ $movie->cast_list ?? '—' }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-slate-500">Качество видео:</span>
                            <span class="text-right">{{ $movie->quality ?? '—' }}</span>
                        </div>

                    </div>

                </div>

            </div>
        </div>

    @elseif($movie->status === 'processing')

        <div class="hud-glass rounded-2xl mb-8 min-h-[260px]
                    flex items-center justify-center
                    text-cyan-300 text-lg">
            🎬 Видео конвертируется…
        </div>

    @elseif($movie->status === 'draft')

        <div class="hud-glass rounded-2xl mb-8 min-h-[260px]
                    flex items-center justify-center
                    text-slate-400 text-lg">
            ⏳ Видео ещё не опубликовано
        </div>

    @else

        <div class="hud-glass rounded-2xl mb-8 min-h-[260px]
                    flex items-center justify-center
                    text-rose-400 text-lg">
            ⛔ Видео недоступно
        </div>

    @endif

    {{-- DESCRIPTION --}}
    @if($movie->description)
        <div class="w-full mt-10">
            <div class="hud-glass p-6 rounded-2xl text-slate-300 leading-relaxed">
                {{ $movie->description }}
            </div>
        </div>
    @endif

  {{-- SCREENSHOTS (СТРОГО ПОД ОПИСАНИЕМ) --}}
@if($movie->screenshots && $movie->screenshots->count())
    <section class="movie-shell w-full mt-12 mb-16">

        {{-- HEADER --}}
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-xl
                        bg-cyan-400/10 text-cyan-300
                        flex items-center justify-center
                        shadow-[0_0_20px_rgba(34,211,238,.4)]">
                📸
            </div>

            <h2 class="text-2xl font-bold text-cyan-300 tracking-wide">
                Кадры из фильма
            </h2>
        </div>

        {{-- GRID --}}
        <div class="screenshots-grid grid gap-6
                    grid-cols-2
                    sm:grid-cols-3
                    md:grid-cols-4
                    lg:grid-cols-5
                    justify-items-center">

            @foreach($movie->screenshots as $i => $shot)
                <button
                    type="button"
                    class="screenshot-thumb
                           cursor-zoom-in group relative
                           overflow-hidden rounded-2xl
                           border border-neutral-800 bg-black
                           hover:border-cyan-400/60
                           hover:shadow-[0_0_30px_rgba(34,211,238,.35)]
                           transition-all duration-300"
                    data-index="{{ $i }}"
                    data-src="{{ asset('storage/'.$shot->path) }}"
                >
                    {{-- IMAGE FRAME (КОНТРОЛЬ РАЗМЕРА) --}}
                    <div class="screenshot-frame bg-black/30 aspect-video">
                        <img
                            src="{{ asset('storage/'.$shot->path) }}"
                            class="w-full h-full object-cover
                                   transition-transform duration-500
                                   group-hover:scale-105"
                            draggable="false"
                            loading="lazy"
                            alt="Screenshot {{ $i + 1 }}">
                    </div>

                    {{-- HOVER --}}
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
    </section>
@endif




{{-- ===== GALLERY MODAL (MOBILE FIX) ===== --}}
<div id="galleryModal"
     class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/80 backdrop-blur-sm opacity-0 transition-opacity duration-300 px-4">

    <div class="relative w-full max-w-5xl mx-auto">

        <button id="galleryClose"
                class="absolute -top-12 right-0 text-white/80 hover:text-cyan-300 transition text-3xl">
            ✕
        </button>

        <button id="galleryPrev"
                class="absolute left-2 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white px-4 py-2 rounded-xl border border-white/10 transition">
            ‹
        </button>

        <button id="galleryNext"
                class="absolute right-2 top-1/2 -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white px-4 py-2 rounded-xl border border-white/10 transition">
            ›
        </button>

        <div class="w-full overflow-hidden rounded-2xl border border-white/10 bg-black/40 shadow-[0_0_60px_rgba(34,211,238,0.12)]">
            <img id="galleryImage"
                 class="w-full max-h-[80vh] object-contain opacity-0 scale-95 transition-all duration-300 cursor-pointer select-none"
                 src=""
                 alt="Screenshot">
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.8"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('player');
    if (!video) return;

    const source = @json(route('movies.stream', [
        'movie' => $movie->id,
        'file'  => 'master.m3u8'
    ]));

    if (window.Hls && Hls.isSupported()) {
        const hls = new Hls();
        hls.loadSource(source);
        hls.attachMedia(video);
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = source;
    }
});
</script>

<script src="{{ asset('js/gallery.js') }}"></script>
@endpush
