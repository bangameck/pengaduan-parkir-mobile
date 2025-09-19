<?php

return [
    'base_domain' => env('APP_URL', 'http://localhost'),
    'prefix'      => env('PWA_PREFIX', '/'),

    'splash'      => [
        'background_color' => '#0058e1',
        'theme_color'      => '#0058e1',
        // Menggunakan ikon utama untuk semua ukuran splash screen
        '640x1136'         => '/icons/android-launchericon-512-512.png',
        '750x1334'         => '/icons/android-launchericon-512-512.png',
        '828x1792'         => '/icons/android-launchericon-512-512.png',
        '1125x2436'        => '/icons/android-launchericon-512-512.png',
        '1242x2208'        => '/icons/android-launchericon-512-512.png',
        '1242x2688'        => '/icons/android-launchericon-512-512.png',
        '1536x2048'        => '/icons/android-launchericon-512-512.png',
        '1668x2224'        => '/icons/android-launchericon-512-512.png',
        '1668x2388'        => '/icons/android-launchericon-512-512.png',
        '2048x2732'        => '/icons/android-launchericon-512-512.png',
    ],

    'icons'       => [
        '72x72'   => [
            'path'    => '/icons/android-launchericon-72-72.png',
            'purpose' => 'any',
        ],
        '96x96'   => [
            'path'    => '/icons/android-launchericon-96-96.png',
            'purpose' => 'any',
        ],
        '128x128' => [
            'path'    => '/icons/android-launchericon-128-128.png',
            'purpose' => 'any',
        ],
        '144x144' => [
            'path'    => '/icons/android-launchericon-144-144.png',
            'purpose' => 'any',
        ],
        '152x152' => [
            'path'    => '/icons/android-launchericon-152-152.png',
            'purpose' => 'any',
        ],
        '192x192' => [
            'path'    => '/icons/android-launchericon-192-192.png',
            'purpose' => 'any',
        ],
        '384x384' => [
            'path'    => '/icons/android-launchericon-384-384.png',
            'purpose' => 'any',
        ],
        '512x512' => [
            'path'    => '/icons/android-launchericon-512-512.png',
            'purpose' => 'any',
        ],
    ],

    'manifest'    => [
        'name'             => 'SiParkirKita',
        'short_name'       => 'SiParkir',
        'start_url'        => '/',
        'background_color' => '#0058e1',
        'theme_color'      => '#0058e1',
        'display'          => 'standalone',
        'orientation'      => 'portrait',
        'status_bar'       => 'black',
        'scope'            => '.',
        'shortcuts'        => [
            [
                'name'  => 'Home',
                'url'   => '/',
                'icons' => [
                    "src"     => "/icons/android-launchericon-72-72.png",
                    "purpose" => "any",
                ],
            ],
        ],
        'custom'           => [],
    ],
];
