@extends('layouts.user-hud')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8 space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div class="flex items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-cyan-300 tracking-wide">
                    {{ $series->title }}
                </h1>
                <div class="text-sm text-neutral-400 mt-1">
                    Сезон {{ $season }} · Серия {{ $episode }}
                </div>
            </div>

            {{-- ❤️ FAVORITE (PREMIUM ONLY) --}}
            @auth
                @if($isPremium)
                    <form method="POST"
                          action="{{ route('favorites.toggle', ['movie' => $series->id]) }}">
                        @csrf
                        <button
                            type="submit"
                            class="text-2xl transition
                            {{ auth()->user()->hasFavorited($series)
                                ? 'text-rose-400'
                                : 'text-neutral-400 hover:text-rose-300' }}"
                            title="В избранное"
                        >
                            ♥
                        </button>
                    </form>
                @endif
            @endauth
        </div>

        <a href="{{ route('series.index') }}" class="btn btn-ghost-white">
            ← К сериалам
        </a>
    </div>

    {{-- Player --}}
    <div
        class="relative bg-neutral-900/70 backdrop-blur
               border border-neutral-800 rounded-2xl overflow-hidden"
        data-spotlight
    >
        <div class="aspect-video bg-black">

            <video
                id="player"
                class="plyr w-full h-full {{ $freeLimitExceeded ? 'pointer-events-none blur-sm' : '' }}"
                playsinline
                controls
                preload="metadata"

                data-hls-url="{{ $videoUrl }}"
                data-start-time="{{ $watchProgress->position_seconds ?? 0 }}"
                data-series-id="{{ $series->id }}"
                data-season="{{ $season }}"
                data-episode="{{ $episode }}"
                data-is-premium="{{ $isPremium ? '1' : '0' }}"
            ></video>

        </div>

        {{-- 🔒 FREE LIMIT OVERLAY --}}
        @if($freeLimitExceeded)
            <div
                class="absolute inset-0 z-20 flex items-center justify-center
                       bg-black/80 backdrop-blur-md"
            >
                <div class="text-center max-w-md px-6">
                    <div class="text-2xl font-bold text-amber-400 mb-3">
                        Лимит бесплатного просмотра
                    </div>

                    <div class="text-neutral-300 mb-6">
                        Бесплатно доступна <b>1 серия в сутки</b>.<br>
                        Оформите Premium, чтобы смотреть без ограничений.
                    </div>

                    <a href="{{ route('premium.index') }}"
                       class="btn btn-emerald text-lg px-8">
                        Оформить Premium
                    </a>
                </div>
            </div>
        @endif
    </div>

    {{-- 🎚 QUALITY UI --}}
    <div class="flex items-center gap-3">
        @foreach([360, 480, 720, 1080] as $q)
            @php $allowed = in_array($q, $allowedQualities); @endphp

            <button
                type="button"
                data-quality-btn="{{ $q }}"
                @disabled(! $allowed)
                class="px-4 py-1.5 rounded-full text-sm font-semibold transition
                    {{ $allowed
                        ? 'border border-cyan-400/40 text-cyan-300 bg-cyan-400/10 hover:bg-cyan-400/20'
                        : 'border border-neutral-700 text-neutral-500 bg-neutral-900/60 cursor-not-allowed'
                    }}"
            >
                {{ $q }}p
            </button>
        @endforeach
    </div>

    {{-- Meta --}}
    <div
        class="grid grid-cols-1 md:grid-cols-3 gap-4
               bg-neutral-900/60 backdrop-blur
               border border-neutral-800 rounded-xl p-4"
        data-spotlight
    >
        <div>
            <div class="text-xs text-neutral-500 uppercase">Сериал</div>
            <div class="text-white font-medium">{{ $series->title }}</div>
        </div>
        <div>
            <div class="text-xs text-neutral-500 uppercase">Сезон</div>
            <div class="text-white font-medium">{{ $season }}</div>
        </div>
        <div>
            <div class="text-xs text-neutral-500 uppercase">Серия</div>
            <div class="text-white font-medium">{{ $episode }}</div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const video = document.getElementById('player');
    if (!video) return;

    // ❌ если лимит превышен — плеер не инициализируем и просмотр НЕ пишем
    if (video.classList.contains('pointer-events-none')) {
        return;
    }

    /* =====================================================
       ✅ ФИКСАЦИЯ ФАКТА ПРОСМОТРА (1 РАЗ)
       ===================================================== */
    fetch('/episodes/{{ $episode }}/watch', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    });

    const hlsUrl    = video.dataset.hlsUrl;
    const startAt   = parseInt(video.dataset.startTime || 0, 10);
    const allowed   = @json($allowedQualities);
    const isPremium = video.dataset.isPremium === '1';

    let hls;
    let player;

    // === Safari (native HLS) ===
    if (video.canPlayType('application/vnd.apple.mpegurl')) {

        video.src = hlsUrl;

        player = new Plyr(video, {
            settings: ['quality', 'speed'],
        });

        video.addEventListener('loadedmetadata', () => {
            if (startAt > 0) video.currentTime = startAt;
        });

    // === Chrome / Firefox / Edge ===
    } else if (window.Hls && Hls.isSupported()) {

        hls = new Hls({
            autoStartLoad: true,
            startPosition: startAt > 0 ? startAt : -1,
        });

        hls.loadSource(hlsUrl);
        hls.attachMedia(video);

        hls.on(Hls.Events.MANIFEST_PARSED, () => {

            const qualities = hls.levels
                .map(l => l.height)
                .filter(q => allowed.includes(q));

            player = new Plyr(video, {
                quality: {
                    default: qualities[qualities.length - 1],
                    options: qualities,
                    forced: true,
                    onChange: q => {
                        hls.levels.forEach((level, i) => {
                            if (level.height === q) {
                                hls.currentLevel = i;
                            }
                        });
                    }
                },
                settings: ['quality', 'speed'],
            });
        });
    }

    /* =====================================================
       🔒 КЛИК ПО ЗАБЛОКИРОВАННОМУ КАЧЕСТВУ → CTA
       ===================================================== */
    document.querySelectorAll('[data-quality-btn]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (btn.disabled) {
                window.location.href = '{{ route('premium.index') }}';
            }
        });
    });

    // === Autosave progress (PREMIUM / TRIAL ONLY) ===
    if (isPremium) {
        let lastSent = 0;

        video.addEventListener('timeupdate', () => {
            const current = Math.floor(video.currentTime);

            if (current - lastSent >= 10) {
                lastSent = current;

                fetch('{{ route('series.progress.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        series_id: video.dataset.seriesId,
                        season: video.dataset.season,
                        episode: video.dataset.episode,
                        position_seconds: current
                    })
                });
            }
        });
    }

});
</script>
@endpush
