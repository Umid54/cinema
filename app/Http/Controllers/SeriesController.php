<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class SeriesController extends Controller
{
    /**
     * 📺 Каталог сериалов
     *
     * Видимы:
     * - published (метаданные готовы)
     * - ready (HLS готов)
     */
    public function index(): View
    {
        $user = Auth::user();

        $series = Movie::query()
            ->where('is_series', true)
            ->whereIn('status', ['published', Movie::STATUS_READY])
            ->when(
                $user && ($user->is_premium_active || $user->is_trial),
                fn ($q) => $q->with([
                    'watchProgress' => function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    }
                ])
            )
            ->latest()
            ->paginate(24);

        return view('series.index', [
            'series' => $series,
            'title'  => 'Сериалы',
        ]);
    }
}
