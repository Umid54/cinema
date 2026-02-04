<?php

namespace App\Http\Controllers;

use App\Services\HlsMasterFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class SeriesStreamController extends Controller
{
    public function handle(
        Request $request,
        int $series,
        int $season,
        int $episode,
        string $file
    ): Response {

        // 🔒 защита
        if (
            str_contains($file, '..') ||
            str_starts_with($file, '/') ||
            str_contains($file, '\\')
        ) {
            abort(404);
        }

        // ✅ РЕАЛЬНЫЙ путь в streams
        $path = "series/{$series}/s{$season}/e{$episode}/{$file}";

        // ✅ ПРОВЕРКА В НУЖНОМ DISK
        if (!Storage::disk('streams')->exists($path)) {
            abort(404);
        }

        // ============================
        // 🎚 m3u8 — отдаём Laravel
        // ============================
        if (str_ends_with($file, '.m3u8')) {
            $content = Storage::disk('streams')->get($path);

            if ($file === 'master.m3u8') {
                $content = HlsMasterFilterService::filter(
                    $content,
                    $request->user()
                );
            }

            return response($content, 200, [
                'Content-Type' => 'application/vnd.apple.mpegurl',
                'Cache-Control' => 'no-store',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        // ============================
        // 🚀 TS — через X-Accel
        // ============================
        return response()
            ->noContent()
            ->header(
                'X-Accel-Redirect',
                "/protected-streams/{$path}"
            )
            ->header('Content-Type', 'video/mp2t')
            ->header('Cache-Control', 'no-store')
            ->header('X-Content-Type-Options', 'nosniff');
    }
}
