<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Топ избранного за всё время
     */
    public function favorites(): View
    {
        $top = DB::table('favorites')
            ->select(
                'favoritable_type',
                'favoritable_id',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('favoritable_type', 'favoritable_id')
            ->orderByDesc('total')
            ->limit(20)
            ->get();

        $items = $top->map(function ($row) {
            $model = $row->favoritable_type::find($row->favoritable_id);

            if (!$model) {
                return null;
            }

            return (object) [
                'type'  => class_basename($row->favoritable_type), // Movie / Series
                'item'  => $model,
                'total' => (int) $row->total,
            ];
        })->filter();

        return view('admin.analytics.favorites', [
            'items' => $items,
        ]);
    }

    /**
     * 📈 Тренды избранного (7 / 30 дней)
     */
    public function trends(): View
    {
        return view('admin.analytics.trends', [
            'trends7'  => $this->buildTrends(7),
            'trends30' => $this->buildTrends(30),
        ]);
    }

    /**
     * Универсальный расчёт трендов
     */
    protected function buildTrends(int $days)
    {
        $nowStart     = Carbon::now()->subDays($days);
        $beforeStart = Carbon::now()->subDays($days * 2);
        $beforeEnd   = Carbon::now()->subDays($days);

        $rows = DB::table('favorites')
            ->select(
                'favoritable_type',
                'favoritable_id',
                DB::raw("
                    SUM(
                        CASE
                            WHEN created_at >= '{$nowStart}'
                            THEN 1 ELSE 0
                        END
                    ) as current_count
                "),
                DB::raw("
                    SUM(
                        CASE
                            WHEN created_at BETWEEN '{$beforeStart}' AND '{$beforeEnd}'
                            THEN 1 ELSE 0
                        END
                    ) as previous_count
                ")
            )
            ->groupBy('favoritable_type', 'favoritable_id')
            ->havingRaw('current_count > previous_count')
            ->orderByRaw('(current_count - previous_count) DESC')
            ->limit(20)
            ->get();

        return $rows->map(function ($row) {
            $model = $row->favoritable_type::find($row->favoritable_id);

            if (!$model) {
                return null;
            }

            return (object) [
                'type'   => class_basename($row->favoritable_type), // Movie / Series
                'item'   => $model,
                'now'    => (int) $row->current_count,
                'before' => (int) $row->previous_count,
                'delta'  => (int) ($row->current_count - $row->previous_count),
            ];
        })->filter();
    }
}
