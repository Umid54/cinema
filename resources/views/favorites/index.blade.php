@extends('layouts.user-hud')

@section('title', 'Избранное')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10">

    <h1 class="text-3xl font-bold text-rose-400 mb-8 tracking-widest">
        ❤️ ИЗБРАННОЕ
    </h1>

    @if($favorites->isEmpty())
        <div class="hud-glass hud-glow p-10 rounded-2xl text-center text-slate-400">
            В избранном пока ничего нет
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            @foreach($favorites as $favorite)
                @php($item = $favorite->favoritable)

                {{-- ================= MOVIE ================= --}}
                @if($item instanceof \App\Models\Movie)
                    <div
                        data-favorite-card
                        class="hud-glass hud-glow rounded-2xl overflow-hidden flex flex-col relative
                               transition-all duration-200">

                        {{-- ❤️ REMOVE (AJAX) --}}
                        <button
                            class="favorite-btn absolute top-3 right-3 z-10
                                   w-10 h-10 rounded-full
                                   bg-black/60 backdrop-blur
                                   flex items-center justify-center
                                   border border-rose-400/40
                                   text-rose-500
                                   hover:text-rose-400
                                   hover:shadow-[0_0_15px_rgba(248,113,113,.6)]
                                   transition"
                            data-movie-id="{{ $item->id }}"
                            title="Убрать из избранного">
                            ♥
                        </button>

                        {{-- POSTER --}}
                        <div class="h-56 bg-slate-800">
                            <img
                                src="{{ $item->poster_url }}"
                                class="w-full h-full object-cover"
                                alt="{{ $item->title }}">
                        </div>

                        <div class="p-5 flex flex-col flex-1">
                            <div class="text-lg font-semibold text-slate-200 mb-2">
                                {{ $item->title }}
                            </div>

                            <a href="{{ route('movies.watch', $item) }}"
                               class="mt-auto btn btn-sky text-center">
                                ▶ Смотреть
                            </a>
                        </div>
                    </div>
                @endif

                {{-- ================= SERIES (пока без AJAX) ================= --}}
                @if($item instanceof \App\Models\Series)
                    <div
                        class="hud-glass hud-glow rounded-2xl overflow-hidden flex flex-col relative opacity-80">

                        <div class="h-56 bg-slate-800">
                            <img
                                src="{{ $item->poster_url }}"
                                class="w-full h-full object-cover"
                                alt="{{ $item->title }}">
                        </div>

                        <div class="p-5 flex flex-col flex-1">
                            <div class="text-lg font-semibold text-slate-200 mb-2">
                                {{ $item->title }}
                            </div>

                            <a href="{{ route('series.watch', [
                                'series' => $item->id,
                                'season' => 1,
                                'episode' => 1
                            ]) }}"
                               class="mt-auto btn btn-emerald text-center">
                                ▶ Смотреть
                            </a>
                        </div>
                    </div>
                @endif

            @endforeach

        </div>
    @endif

</div>
@endsection
