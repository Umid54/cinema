<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\View\View;

class SeriesController extends Controller
{
    /**
     * Каталог сериалов
     */
    public function index(): View
    {
        $user = auth()->user();

        $series = Movie::query()
            ->where('is_series', true)
            ->where('status', 'published')
            ->when(
                $user && ($user->is_premium_active || $user->is_trial),
                fn ($q) => $q->with(['watchProgress' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }])
            )
            ->latest()
            ->paginate(24);

        return view('series.index', [
            'series' => $series,
        ]);
    }
}
