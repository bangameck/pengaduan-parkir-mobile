<?php

return [
    'install-button' => true,

    'manifest'       => [
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
            // --- Ikon dengan purpose 'any' (ikon biasa) ---
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
            [
                'src'     => '/icons/android-launchericon-144-144.png',
                'sizes'   => '144x144',
                'type'    => 'image/png',
                'purpose' => 'any',
            ],
            [
                'src'     => '/icons/android-launchericon-192-192.png',
                'sizes'   => '192x192',
                'type'    => 'image/png',
                'purpose' => 'any',
            ],

            // --- Ikon dengan purpose 'maskable' (untuk Android) ---
            [
                'src'     => '/icons/android-launchericon-512-512.png',
                'sizes'   => '512x512',
                'type'    => 'image/png',
                'purpose' => 'maskable', // <-- Hanya 'maskable'
            ],
        ],

        // ## TAMBAHAN: Menambahkan screenshots ##
        'screenshots'      => [
            [
                'src'         => '/images/mobile-1.png',
                'sizes'       => '1080x1920',
                'type'        => 'image/png',
                'form_factor' => 'narrow', // Untuk mobile
                'label'       => 'Tampilan Laporan Publik di Mobile',
            ],
            [
                'src'         => '/images/desktop-1.png',
                'sizes'       => '1920x1080',
                'type'        => 'image/png',
                'form_factor' => 'wide', // Untuk desktop
                'label'       => 'Tampilan Laporan di Desktop',
            ],
        ],

        'shortcuts'        => [
            [
                'name'        => 'Home',
                'short_name'  => 'Lapor',
                'description' => 'Aplikasii Pengaduan Perparkiran',
                'url'         => '/',
                'icons'       => [
                    // ## PERBAIKAN: Menambahkan ikon 96x96 ##
                    [
                        "src"     => "/icons/android-launchericon-96-96.png",
                        "sizes"   => "96x96",
                        "purpose" => "any",
                    ],
                ],
            ],
        ],
    ],

    'debug'          => env('APP_DEBUG', false),
    'livewire-app'   => true,
];
