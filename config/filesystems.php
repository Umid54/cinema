<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Используем local по умолчанию (private storage)
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    */

    'disks' => [

        /**
         * 🔒 PRIVATE
         * uploads, source.mp4, raw-файлы
         * доступ ТОЛЬКО через backend
         */
        'local' => [
            'driver'     => 'local',
            'root'       => storage_path('app/private'),
            'visibility' => 'private',
            'throw'      => false,
            'report'     => false,
        ],

        /**
         * 🌍 PUBLIC
         * постеры, скриншоты, preview
         */
        'public' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public'),
            'url'        => rtrim(env('APP_URL', 'http://localhost'), '/') . '/storage',
            'visibility' => 'public',
            'throw'      => false,
            'report'     => false,
        ],

        /**
         * 🎬 STREAMS (HLS)
         * master.m3u8 → Laravel
         * .ts → nginx (X-Accel-Redirect)
         *
         * ФИЗИЧЕСКИЙ ПУТЬ:
         * storage/app/streams/series/{id}/s{n}/e{n}/
         */
        'streams' => [
            'driver'     => 'local',
            'root'       => storage_path('app/streams'),
            'visibility' => 'private',
            'throw'      => false,
            'report'     => false,
        ],

        /**
         * ☁️ S3 (опционально, на будущее)
         */
        's3' => [
            'driver'                  => 's3',
            'key'                     => env('AWS_ACCESS_KEY_ID'),
            'secret'                  => env('AWS_SECRET_ACCESS_KEY'),
            'region'                  => env('AWS_DEFAULT_REGION'),
            'bucket'                  => env('AWS_BUCKET'),
            'url'                     => env('AWS_URL'),
            'endpoint'                => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw'                   => false,
            'report'                  => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
