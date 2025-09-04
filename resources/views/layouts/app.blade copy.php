<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <!-- Scripts -->
        <style>
            .preloader-hidden {
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
            }
        </style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans antialiased">
        {{-- AWAL PRELOADER --}}
        <div id="page-preloader" class="fixed inset-0 z-[100] flex items-center justify-center bg-white">
            <div class="relative flex items-center justify-center w-24 h-24">
                <svg class="absolute w-16 h-16 text-dishub-blue-800" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div
                    class="absolute w-full h-full border-4 border-dishub-yellow-300 border-t-transparent rounded-full animate-spin">
                </div>
            </div>
        </div>
        {{-- AKHIR PRELOADER --}}
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
            {{-- === TAMBAHKAN POP-UP MEDIA PLAYER DI SINI === --}}
            <div x-data="{ isMediaPlayerOpen: false, mediaUrl: '', mediaType: 'image' }" x-show="isMediaPlayerOpen" x-transition
                @open-media-viewer.window="
                mediaUrl = $event.detail.url;
                mediaType = $event.detail.type;
                isMediaPlayerOpen = true;
             "
                @keydown.escape.window="isMediaPlayerOpen = false"
                class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 p-4"
                style="display: none;">

                <div class="relative w-full max-w-3xl" @click.away="isMediaPlayerOpen = false">
                    <button @click="isMediaPlayerOpen = false"
                        class="absolute -top-10 right-0 text-white hover:text-gray-300 text-4xl font-bold">&times;</button>
                    <template x-if="isMediaPlayerOpen">
                        <div class="bg-black">
                            <template x-if="mediaType === 'video'">
                                <video :src="mediaUrl" class="w-full h-auto max-h-[80vh]" controls autoplay
                                    playsinline></video>
                            </template>
                            <template x-if="mediaType === 'image'">
                                <img :src="mediaUrl" class="w-full h-auto max-h-[80vh] object-contain">
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </body>
    @if (session('success') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                @if (session('success'))
                    Toast.fire({
                        icon: 'success',
                        title: '{{ session('success') }}'
                    });
                @endif

                @if (session('error'))
                    Toast.fire({
                        icon: 'error',
                        title: '{{ session('error') }}'
                    });
                @endif
            });
        </script>
    @endif
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    @stack('scripts')
    <script>
        // Logika untuk menyembunyikan preloader setelah halaman siap
        window.addEventListener('load', function() {
            const preloader = document.getElementById('page-preloader');
            if (preloader) {
                preloader.classList.add('preloader-hidden');
                // Hapus elemen dari DOM setelah animasi selesai
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 500);
            }
        });
    </script>

</html>
