<?php

namespace App\Jobs;

use App\Models\Movie;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class ConvertMovieToHls implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 0;

    public function __construct(
        public int $movieId
    ) {}

    public function handle(): void
    {
        $movie = Movie::findOrFail($this->movieId);

        // 1️⃣ processing
        $movie->update([
            'status' => 'processing',
            'hls_path' => null,
        ]);

        $source = storage_path("app/private/uploads/movies/{$movie->id}/source.mp4");
        $outputDir = storage_path("app/public/streams/movies/{$movie->id}");
        $master = $outputDir . '/master.m3u8';

        try {

            // 2️⃣ source check
            if (!File::exists($source)) {
                throw new Exception("Source file not found: {$source}");
            }

            // 3️⃣ output dir
            File::ensureDirectoryExists($outputDir);

            // 4️⃣ ffmpeg (stable, single-bitrate)
            $process = new Process([
                'ffmpeg',
                '-y',
                '-i', $source,
                '-map', '0:v',
                '-map', '0:a?',
                '-c:v', 'libx264',
                '-preset', 'veryfast',
                '-crf', '23',
                '-c:a', 'aac',
                '-ar', '48000',
                '-hls_time', '6',
                '-hls_playlist_type', 'vod',
                '-hls_segment_filename', $outputDir . '/seg_%03d.ts',
                $master,
            ]);

            $process->setTimeout(null);
            $process->run();

            if (!$process->isSuccessful()) {
                throw new Exception(
                    $process->getErrorOutput() ?: 'ffmpeg failed'
                );
            }

            // 5️⃣ success criteria
            if (!File::exists($master)) {
                throw new Exception('master.m3u8 not created');
            }

            // 6️⃣ READY
            $movie->update([
                'status'   => 'ready',
                'hls_path' => "streams/movies/{$movie->id}/master.m3u8",
            ]);

        } catch (\Throwable $e) {

            Log::error('[HLS CONVERT FAILED]', [
                'movie_id' => $movie->id,
                'error'    => $e->getMessage(),
            ]);

            // 7️⃣ BLOCKED
            $movie->update([
                'status' => 'blocked',
            ]);

            throw $e;
        }
    }
}
