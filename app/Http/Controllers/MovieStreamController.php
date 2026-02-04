<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MovieStreamController extends Controller
{
    /**
     * 🎬 Stream HLS (master.m3u8 + seg_*.ts)
     */
    public function stream(Movie $movie, string $file)
    {
        // =====================================================
        // 🧠 ФАКТ:
        // HLS лежит в storage/app/public/streams
        // nginx alias → /protected-streams/
        // =====================================================

        $relativePath = "movies/{$movie->id}/{$file}";

        // =====================================================
        // 🟢 TS СЕГМЕНТЫ — ВСЕГДА ДОСТУПНЫ
        // =====================================================
        if (str_ends_with($file, '.ts')) {
            return response('', 200, [
                'X-Accel-Redirect' => "/protected-streams/{$relativePath}",
                'Content-Type'    => 'video/mp2t',
                'Cache-Control'   => 'no-store',
            ]);
        }

        // =====================================================
        // 🔐 MASTER.m3u8 — ТОЛЬКО ДЛЯ PREMIUM
        // =====================================================
        if (!auth()->check() || !auth()->user()->is_premium) {
            abort(403);
        }

        return response('', 200, [
            'X-Accel-Redirect' => "/protected-streams/{$relativePath}",
            'Content-Type'    => 'application/vnd.apple.mpegurl',
            'Cache-Control'   => 'no-store',
        ]);
    }
}
