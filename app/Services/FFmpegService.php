<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;

class FFmpegService
{
    /**
     * Конвертация видео в HLS 720p (MVP)
     *
     * @throws \Throwable
     */
    public function convertToHls720(string $inputPath, int $movieId): string
    {
        if (!file_exists($inputPath)) {
            throw new \RuntimeException("Input file not found: {$inputPath}");
        }

        $baseDir = "videos/movies/{$movieId}";
        $publicBase = storage_path("app/public/{$baseDir}");
        $outDir = "{$publicBase}/720p";

        if (!is_dir($outDir)) {
            mkdir($outDir, 0755, true);
        }

        $indexPath  = "{$outDir}/index.m3u8";
        $masterPath = "{$publicBase}/master.m3u8";

        $cmd = [
            'ffmpeg',
            '-y',
            '-i', $inputPath,

            // Видео
            '-vf', 'scale=-2:720',
            '-c:v', 'libx264',
            '-profile:v', 'main',
            '-preset', 'veryfast',
            '-b:v', '2800k',
            '-maxrate', '3000k',
            '-bufsize', '4200k',

            // Аудио
            '-c:a', 'aac',
            '-ac', '2',
            '-b:a', '128k',

            // HLS
            '-f', 'hls',
            '-hls_time', '6',
            '-hls_playlist_type', 'vod',
            '-hls_flags', 'independent_segments',
            '-hls_segment_filename', "{$outDir}/segment_%03d.ts",

            $indexPath
        ];

        Process::timeout(0)->run($cmd)->throw();

        // master.m3u8 (один поток — MVP)
        file_put_contents($masterPath, implode("\n", [
            '#EXTM3U',
            '#EXT-X-VERSION:3',
            '#EXT-X-STREAM-INF:BANDWIDTH=2800000,RESOLUTION=1280x720',
            '720p/index.m3u8',
        ]));

        return "{$baseDir}/master.m3u8";
    }

    /**
     * Генерация preview.mp4 (hover preview)
     * 6 секунд, без звука, безопасный seek
     *
     * @throws \Throwable
     */
    public function generatePreview(string $inputPath, int $movieId): string
{
    $baseDir = "videos/movies/{$movieId}";
    $publicBase = storage_path("app/public/{$baseDir}");
    $hlsIndex = "{$publicBase}/720p/index.m3u8";
    $previewPath = "{$publicBase}/preview.mp4";

    if (!file_exists($hlsIndex)) {
        throw new \RuntimeException("HLS index not found: {$hlsIndex}");
    }

    $cmd = [
        'ffmpeg',
        '-y',

        // безопасный seek
        '-ss', '10',
        '-i', $hlsIndex,

        // 6 секунд
        '-t', '6',

        // лёгкое видео
        '-vf', 'scale=426:240,fps=24',
        '-c:v', 'libx264',
        '-preset', 'veryfast',
        '-crf', '28',

        // без звука
        '-an',
        '-movflags', '+faststart',

        $previewPath
    ];

    \Illuminate\Support\Facades\Process::timeout(0)
        ->run($cmd)
        ->throw();

    return "{$baseDir}/preview.mp4";
}

}
