<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('movies', function (Blueprint $table) {

            // путь к загруженному исходному видео (tmp)
            if (!Schema::hasColumn('movies', 'video_path')) {
                $table->string('video_path')->nullable()->after('id');
            }

            // путь к HLS master.m3u8
            if (!Schema::hasColumn('movies', 'hls_path')) {
                $table->string('hls_path')->nullable()->after('video_path');
            }

            // статус обработки видео
            if (!Schema::hasColumn('movies', 'status')) {
                $table->enum('status', [
                    'draft',
                    'uploading',
                    'processing',
                    'ready',
                    'error',
                ])->default('draft')->after('hls_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {

            if (Schema::hasColumn('movies', 'video_path')) {
                $table->dropColumn('video_path');
            }

            if (Schema::hasColumn('movies', 'hls_path')) {
                $table->dropColumn('hls_path');
            }

            if (Schema::hasColumn('movies', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
