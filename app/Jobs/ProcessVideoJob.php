<?php

namespace App\Jobs;

use App\Models\Movie;
use App\Models\Video;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;

class ProcessVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 0;
    public int $tries = 1;

    protected Movie $movie;
    protected string $sourcePath;

    public function __construct(Movie $movie, string $sourcePath)
    {
        $this->movie = $movie;
        $this->sourcePath = $sourcePath;
    }

    public function handle(): void
    {
        $movieId = $this->movie->id;

        $baseDir        = "videos/movies/{$movieId}";
        $masterRelative = "{$baseDir}/master.m3u8";
        $masterAbsolute = Storage::disk('public')->path($masterRelative);

        Log::info('[HLS] Job started', [
            'movie_id' => $movieId,
            'source'   => $this->sourcePath,
        ]);

        if (!file_exists($this->sourcePath)) {
            $this->failJob('Source file not found');
            return;
        }

        if (Storage::disk('public')->exists($masterRelative)) {
            $this->syncModels($masterRelative);
            return;
        }

        foreach (['1080p', '720p', '480p'] as $res) {
            Storage::disk('public')->makeDirectory("{$baseDir}/{$res}");
        }

        try {
            $cmd = [
                'ffmpeg',
                '-y',
                '-i', $this->sourcePath,

                '-filter_complex',
                '[0:v]split=3[v1080][v720][v480];' .

                '[v1080]scale=w=1920:h=1080:force_original_aspect_ratio=decrease,' .
                'pad=1920:1080:(ow-iw)/2:(oh-ih)/2[v1080out];' .

                '[v720]scale=w=1280:h=720:force_original_aspect_ratio=decrease,' .
                'pad=1280:720:(ow-iw)/2:(oh-ih)/2[v720out];' .

                '[v480]scale=w=854:h=480:force_original_aspect_ratio=decrease,' .
                'pad=854:480:(ow-iw)/2:(oh-ih)/2[v480out]',

                '-map', '[v1080out]', '-map', '0:a',
                '-map', '[v720out]',  '-map', '0:a',
                '-map', '[v480out]',  '-map', '0:a',

                '-c:v', 'libx264',
                '-preset', 'slow',
                '-crf', '20',
                '-profile:v', 'main',
                '-g', '48',
                '-keyint_min', '48',
                '-sc_threshold', '0',

                '-c:a', 'aac',
                '-ar', '48000',

                '-b:v:0', '5000k',
                '-maxrate:v:0', '5350k',
                '-bufsize:v:0', '7500k',

                '-b:v:1', '2800k',
                '-maxrate:v:1', '2996k',
                '-bufsize:v:1', '4200k',

                '-b:v:2', '1400k',
                '-maxrate:v:2', '1498k',
                '-bufsize:v:2', '2100k',

                '-f', 'hls',
                '-hls_time', '6',
                '-hls_playlist_type', 'vod',
                '-hls_flags', 'independent_segments',
                '-hls_segment_type', 'mpegts',

                '-var_stream_map', 'v:0,a:0 v:1,a:1 v:2,a:2',
                '-master_pl_name', 'master.m3u8',

                '-hls_segment_filename',
                Storage::disk('public')->path("{$baseDir}/%v/segment_%03d.ts"),

                Storage::disk('public')->path("{$baseDir}/%v/index.m3u8"),
            ];

            $process = new Process($cmd);
            $process->setTimeout(null);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new Exception($process->getErrorOutput());
            }

            if (!file_exists($masterAbsolute)) {
                throw new Exception('master.m3u8 was not generated');
            }

            $this->syncModels($masterRelative);

            Log::info('[HLS] Job finished successfully', [
                'movie_id' => $movieId,
            ]);

        } catch (Exception $e) {
            $this->failJob($e->getMessage());
            throw $e;
        }
    }

    protected function syncModels(string $masterRelative): void
    {
        Video::updateOrCreate(
            ['movie_id' => $this->movie->id],
            [
                'type'       => 'hls',
                'path'       => $masterRelative,
                'resolution' => json_encode(['1080p', '720p', '480p']),
                'status'     => 'ready',
            ]
        );

        $this->movie->update([
            'status'   => 'ready',
            'hls_path' => $masterRelative,
        ]);
    }

    protected function failJob(string $reason): void
    {
        Log::error('[HLS] Job failed', [
            'movie_id' => $this->movie->id,
            'error'    => $reason,
        ]);

        $this->movie->update(['status' => 'error']);
    }
}
