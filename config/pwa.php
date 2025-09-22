<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Would you like the install button to appear on all pages?
      Set true/false
    |--------------------------------------------------------------------------
    */

    'install-button' => true,

    /*
    |--------------------------------------------------------------------------
    | PWA Manifest Configuration
    |--------------------------------------------------------------------------
    |  php artisan erag:update-manifest
    */

    'manifest'       => [
        // --- DATA DARI KONFIGURASI LAMA ANDA ---
        'name'             => 'SiParkirKita',
        'short_name'       => 'SiParkir',
        'start_url'        => '/',
        'background_color' => '#0058e1',
        'theme_color'      => '#0058e1',
        'display'          => 'standalone',
        'orientation'      => 'portrait',
        'status_bar'       => 'black',
        'scope'            => '.',
        'description'      => 'Aplikasi Manajemen Parkir SiParkirKita',

        'icons'            => [
            // --- PATH IKON YANG SUDAH DISESUAIKAN ---
            [
                'src'     => '/icons/android-launchericon-72-72.png',
                'sizes'   => '72x72',
                'type'    => 'image/png',
                'purpose' => 'any',
            ],
            [
                'src'     => '/icons/android-launchericon-96-96.png',
                'sizes'   => '96x96',
                'type'    => 'image/png',
                'purpose' => 'any',
            ],
            // [
            //     'src'     => '/icons/android-launchericon-128-128.png',
            //     'sizes'   => '128x128',
            //     'type'    => 'image/png',
            //     'purpose' => 'any',
            // ],
            [
                'src'     => '/icons/android-launchericon-144-144.png',
                'sizes'   => '144x144',
                'type'    => 'image/png',
                'purpose' => 'any',
            ],
            // [
            //     'src'     => '/icons/android-launchericon-152-152.png',
            //     'sizes'   => '152x152',
            //     'type'    => 'image/png',
            //     'purpose' => 'any',
            // ],
            [
                'src'     => '/icons/android-launchericon-192-192.png',
                'sizes'   => '192x192',
                'type'    => 'image/png',
                'purpose' => 'any',
            ],
            // [
            //     'src'     => '/icons/android-launchericon-384-384.png',
            //     'sizes'   => '384x384',
            //     'type'    => 'image/png',
            //     'purpose' => 'any',
            // ],
            [
                'src'     => '/icons/android-launchericon-512-512.png',
                'sizes'   => '512x512',
                'type'    => 'image/png',
                'purpose' => 'any',
            ],
        ],

        'shortcuts'        => [
            [
                'name'  => 'Home',
                'url'   => '/',
                'icons' => [
                    [
                        "src"     => "/icons/android-launchericon-72-72.png",
                        "sizes"   => "72x72",
                        "purpose" => "any",
                    ],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Configuration
    |--------------------------------------------------------------------------
    | Toggles the application's debug mode based on the environment variable
    */

    'debug'          => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Livewire Integration
    |--------------------------------------------------------------------------
    | Set to true if you're using Livewire in your application to enable
    | Livewire-specific PWA optimizations or features.
    */

    'livewire-app'   => true,
];
