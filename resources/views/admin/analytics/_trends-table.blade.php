@if($items->isEmpty())
    <div class="hud-glass hud-glow p-6 rounded-xl text-slate-400 mb-8">
        Нет растущих позиций
    </div>
@else
    <div class="hud-glass hud-glow rounded-2xl overflow-hidden mb-8">

        <table class="w-full text-sm">
            <thead class="bg-neutral-900/80 text-slate-400">
                <tr>
                    <th class="px-6 py-4 text-left">Тип</th>
                    <th class="px-6 py-4 text-left">Название</th>
                    <th class="px-6 py-4 text-right">Было</th>
                    <th class="px-6 py-4 text-right">Стало</th>
                    <th class="px-6 py-4 text-right text-emerald-400">Δ</th>
                </tr>
            </thead>
            <tbody>

                @foreach($items as $row)
                    <tr class="border-t border-neutral-800 hover:bg-neutral-900/40">

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

                        <td class="px-6 py-4 text-right text-slate-400">
                            {{ $row->before }}
                        </td>

                        <td class="px-6 py-4 text-right text-slate-200">
                            {{ $row->now }}
                        </td>

                        <td class="px-6 py-4 text-right font-semibold text-emerald-400">
                            +{{ $row->delta }}
                        </td>

                    </tr>
                @endforeach

            </tbody>
        </table>

    </div>
@endif
