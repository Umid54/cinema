@extends('admin.layout')

@section('title', 'Admin HUD')
@section('header', 'Control Center')

@section('content')

{{-- STATUS ROW --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">

    <div class="hud-glass hud-glow p-6 rounded-2xl">
        <div class="text-xs text-slate-400 mb-2">STATUS</div>
        <div class="text-emerald-400 font-semibold tracking-wide">
            SYSTEM ONLINE
        </div>
    </div>

    <div class="hud-glass hud-glow-emerald p-6 rounded-2xl">
        <div class="text-xs text-slate-400 mb-2">ROLE</div>
        <div class="text-cyan-300 text-xl font-bold">
            ADMIN
        </div>
    </div>

    <div class="hud-glass hud-glow p-6 rounded-2xl">
        <div class="text-xs text-slate-400 mb-2">ACCESS</div>
        <div class="text-slate-300">
            FULL CONTROL
        </div>
    </div>

</div>

{{-- ANALYTICS --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- TOP FAVORITES --}}
    <div class="lg:col-span-1 hud-glass hud-glow p-6 rounded-2xl">
        <div class="text-xs text-slate-400 mb-4">
            📊 ТОП ИЗБРАННОГО (ALL-TIME)
        </div>

        @forelse($topFavorites as $row)
            <div class="flex justify-between items-center py-2 border-b border-neutral-800 last:border-0">
                <div class="truncate text-slate-200">
                    {{ $row->item->title }}
                </div>
                <div class="text-rose-400 font-semibold ml-4">
                    {{ $row->total }}
                </div>
            </div>
        @empty
            <div class="text-slate-500 text-sm">
                Нет данных
            </div>
        @endforelse
    </div>

    {{-- TRENDS 7 DAYS --}}
    <div class="hud-glass hud-glow p-6 rounded-2xl">
        <div class="text-xs text-slate-400 mb-4">
            📈 РОСТ ЗА 7 ДНЕЙ
        </div>

        @forelse($trends7 as $row)
            <div class="flex justify-between items-center py-2 border-b border-neutral-800 last:border-0">
                <div class="truncate text-slate-200">
                    {{ $row->item->title }}
                </div>
                <div class="text-emerald-400 font-semibold ml-4">
                    +{{ $row->delta }}
                </div>
            </div>
        @empty
            <div class="text-slate-500 text-sm">
                Нет роста
            </div>
        @endforelse
    </div>

    {{-- TRENDS 30 DAYS --}}
    <div class="hud-glass hud-glow p-6 rounded-2xl">
        <div class="text-xs text-slate-400 mb-4">
            📊 РОСТ ЗА 30 ДНЕЙ
        </div>

        @forelse($trends30 as $row)
            <div class="flex justify-between items-center py-2 border-b border-neutral-800 last:border-0">
                <div class="truncate text-slate-200">
                    {{ $row->item->title }}
                </div>
                <div class="text-sky-400 font-semibold ml-4">
                    +{{ $row->delta }}
                </div>
            </div>
        @empty
            <div class="text-slate-500 text-sm">
                Нет роста
            </div>
        @endforelse
    </div>

</div>

@endsection
