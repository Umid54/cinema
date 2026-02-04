@extends('layouts.user-hud')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10">

    <h1 class="text-3xl font-bold text-cyan-300 mb-8 tracking-wide">
        📺 История просмотров
    </h1>

    {{-- LAST WATCHED --}}
    @if($lastWatched)
        <div class="mb-10 p-6 rounded-2xl
                    bg-emerald-500/10 border border-emerald-400/30
                    shadow-[0_0_30px_rgba(52,211,153,0.25)]">

            <div class="text-emerald-300 font-semibold mb-2">
                Последний просмотр
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xl font-bold text-white">
                        {{ $lastWatched->series->title }}
                    </div>

                    <div class="text-sm text-neutral-400 mt-1">
                        Сезон {{ $lastWatched->season }},
                        серия {{ $lastWatched->episode }}
                    </div>
                </div>

                <a href="{{ route('series.watch', [
                        'movie' => $lastWatched->series_id,
                        'season' => $lastWatched->season,
                        'episode' => $lastWatched->episode
                    ]) }}"
                   class="px-6 py-3 rounded-full
                          bg-emerald-500/20 border border-emerald-400/40
                          text-emerald-300 font-semibold
                          shadow-[0_0_25px_rgba(52,211,153,0.6)]">
                    ▶ Продолжить
                </a>
            </div>
        </div>
    @endif

    {{-- HISTORY LIST --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @forelse($history as $item)
            <div class="rounded-xl p-4
                        bg-neutral-900/60 border border-neutral-800
                        hover:border-cyan-400/40 transition">

                <div class="font-semibold text-white">
                    {{ $item->series->title }}
                </div>

                <div class="text-sm text-neutral-400 mt-1">
                    Сезон {{ $item->season }},
                    серия {{ $item->episode }}
                </div>

                <a href="{{ route('series.watch', [
                        'movie' => $item->series_id,
                        'season' => $item->season,
                        'episode' => $item->episode
                    ]) }}"
                   class="inline-block mt-4 text-cyan-300 hover:text-cyan-200">
                    ▶ Продолжить
                </a>
            </div>
        @empty
            <div class="text-neutral-400">
                История просмотров пуста
            </div>
        @endforelse

    </div>

</div>
@endsection
