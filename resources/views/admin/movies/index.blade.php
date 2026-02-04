@extends('admin.layout')

@section('title', 'Контент')
@section('header', 'Фильмы и сериалы')

@section('content')

{{-- HEADER ACTIONS --}}
<div class="flex justify-between items-center mb-8">
    <div class="text-sm hud-muted">
        Всего: {{ $movies->total() }}
    </div>

    <a href="{{ route('admin.movies.create') }}"
       class="btn btn-emerald">
        ➕ Добавить контент
    </a>
</div>

{{-- GRID --}}
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-6">

@forelse($movies as $movie)

    <div class="hud-glass hud-glow rounded-2xl overflow-hidden group">

        {{-- POSTER --}}
        <div class="relative aspect-[2/3] bg-neutral-900 overflow-hidden">

            @if($movie->poster_url)
                <img src="{{ $movie->poster_url }}"
                     alt="{{ $movie->title }}"
                     class="absolute inset-0 w-full h-full object-cover
                            transition duration-300 group-hover:scale-105">
            @else
                <div class="absolute inset-0 flex items-center justify-center hud-muted text-xs">
                    NO POSTER
                </div>
            @endif

            {{-- TYPE BADGE --}}
            <div class="absolute top-3 left-3 px-3 py-1 rounded-full text-xs
                        {{ $movie->is_series
                            ? 'bg-cyan-500/20 text-cyan-300'
                            : 'bg-emerald-500/20 text-emerald-300' }}">
                {{ $movie->is_series ? 'СЕРИАЛ' : 'ФИЛЬМ' }}
            </div>

            {{-- STATUS --}}
            <div class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs
                        {{ $movie->status === 'published'
                            ? 'bg-emerald-500/20 text-emerald-300'
                            : 'bg-amber-500/20 text-amber-300' }}">
                {{ strtoupper($movie->status) }}
            </div>
        </div>

        {{-- BODY --}}
        <div class="p-4 space-y-1">

            <div class="font-semibold text-sm hud-text truncate">
                {{ $movie->title }}
            </div>

            @if($movie->year)
                <div class="hud-muted text-xs">
                    {{ $movie->year }}
                </div>
            @endif

        </div>

        {{-- ACTIONS --}}
        <div class="flex border-t border-neutral-800 text-xs">

            <a href="{{ route('admin.movies.edit', $movie) }}"
               class="flex-1 text-center py-3 hud-text hover:bg-neutral-800/60 transition">
                ✏ Редактировать
            </a>

            <form method="POST"
                  action="{{ route('admin.movies.destroy', $movie) }}"
                  onsubmit="return confirm('Удалить контент?')"
                  class="flex-1">
                @csrf
                @method('DELETE')
                <button class="w-full py-3 hud-rose hover:bg-neutral-800/60 transition">
                    🗑 Удалить
                </button>
            </form>

        </div>

    </div>

@empty
    <div class="col-span-full text-center hud-muted py-20">
        Контент не найден
    </div>
@endforelse

</div>

{{-- PAGINATION --}}
@if($movies->hasPages())
    <div class="mt-10 hud-text">
        {{ $movies->links() }}
    </div>
@endif

@endsection
