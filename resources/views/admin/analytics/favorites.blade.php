@extends('layouts.user-hud')

@section('title', 'Топ избранного')

@section('content')

<h1 class="text-3xl font-bold text-emerald-400 mb-10 tracking-widest">
    📊 ТОП ИЗБРАННОГО
</h1>

@if($items->isEmpty())
    <div class="hud-glass hud-glow p-8 rounded-2xl text-center text-slate-400">
        Данных пока нет
    </div>
@else
    <div class="hud-glass hud-glow rounded-2xl overflow-hidden">

        <table class="w-full text-sm">
            <thead class="bg-neutral-900/80 text-slate-400">
                <tr>
                    <th class="px-6 py-4 text-left">#</th>
                    <th class="px-6 py-4 text-left">Тип</th>
                    <th class="px-6 py-4 text-left">Название</th>
                    <th class="px-6 py-4 text-right">❤️ Добавлений</th>
                </tr>
            </thead>
            <tbody>

                @foreach($items as $i => $row)
                    <tr class="border-t border-neutral-800 hover:bg-neutral-900/40">
                        <td class="px-6 py-4 text-slate-500">
                            {{ $i + 1 }}
                        </td>

                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs
                                {{ $row->type === 'Movie'
                                    ? 'bg-cyan-500/10 text-cyan-300'
                                    : 'bg-emerald-500/10 text-emerald-300' }}">
                                {{ $row->type === 'Movie' ? 'Фильм' : 'Сериал' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-slate-200">
                            {{ $row->item->title }}
                        </td>

                        <td class="px-6 py-4 text-right text-rose-400 font-semibold">
                            {{ $row->total }}
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>

    </div>
@endif

@endsection
