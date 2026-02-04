<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        /**
         * =========================
         * ТОП ИЗБРАННОГО (ALL TIME)
         * =========================
         */
        $topFavoritesRaw = DB::table('favorites')
            ->where('favoritable_type', Movie::class)
            ->select('favoritable_id', DB::raw('COUNT(*) as total'))
            ->groupBy('favoritable_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $movies = Movie::whereIn(
            'id',
            $topFavoritesRaw->pluck('favoritable_id')
        )->get()->keyBy('id');

        $topFavorites = $topFavoritesRaw->map(function ($row) use ($movies) {
            return (object)[
                'item'  => $movies[$row->favoritable_id] ?? null,
                'total' => $row->total,
            ];
        })->filter(fn ($row) => $row->item !== null);

        /**
         * =========================
         * TRENDS (ПОКА ЗАГЛУШКИ)
         * =========================
         * ВАЖНО: Blade требует эти переменные
         */
        $trends7  = collect();
        $trends30 = collect();

        return view('admin.dashboard', [
            'topFavorites' => $topFavorites,
            'trends7'      => $trends7,
            'trends30'     => $trends30,
        ]);
    }
}
