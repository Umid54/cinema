<?php

namespace App\Http\Controllers;

use App\Models\Series;
use App\Services\WatchProgressService;
use Illuminate\Http\Request;

class SeriesWatchController extends Controller
{
    public function watch(
        Request $request,
        Series $series,
        int $season,
        int $episode,
        WatchProgressService $progress
    ) {
        $user = auth()->user();

        // 👑 Premium или Trial (для UX: resume, overlay)
        $isPremium = $user && ($user->is_premium_active || $user->is_trial);

        // 🎞 Разрешённые качества (ТОЛЬКО UI, единый источник — User)
        $allowedQualities = $user
            ? $user->allowedQualities()
            : [360];

        // 🎥 HLS master playlist
        // ❗ ТОЛЬКО через stream-route
        $videoUrl = route('series.stream', [
            'movie'   => $series->id,   // ⚠️ имя параметра как в routes
            'season'  => $season,
            'episode' => $episode,
            'file'    => 'master.m3u8',
        ]);

        return view('series.watch', [
            'series'  => $series,
            'season'  => $season,
            'episode' => $episode,

            // 🎬 Плеер
            'videoUrl' => $videoUrl,

            // ▶️ Resume playback (ТОЛЬКО Premium / Trial)
            'watchProgress' => $isPremium
                ? $progress->getForSeries($series->id)
                : null,

            // 👑 Quality / UX
            'allowedQualities' => $allowedQualities,
            'isPremium'        => $isPremium,

            // 🔒 FREE limit overlay
            'freeLimitExceeded' => (bool) $request->get('free_limit_exceeded', false),
        ]);
    }
}
