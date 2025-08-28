<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'ParkirPKU') }} - @yield('title')</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap"
            rel="stylesheet">

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            body {
                font-family: 'Work Sans', sans-serif;
            }
        </style>
    </head>

    {{-- Inisialisasi Alpine.js di tag <body> untuk mengontrol state modal --}}

    <body class="bg-gray-50">

        <main class="pb-24">
            {{-- Kontainer untuk Skeleton Loader --}}
            <div id="skeleton-loader">
                @yield('skeleton')
            </div>
            {{-- Kontainer untuk Konten Asli (Tersembunyi) --}}
            <div id="main-content" class="hidden">
                @yield('content')
            </div>
        </main>

        <nav class="fixed bottom-0 left-0 right-0 w-full bg-white border-t z-40">
            <div class="flex items-center justify-around max-w-md mx-auto h-16">

                {{-- Menu Kiri --}}
                <div class="flex justify-around w-full">
                    {{-- Tombol Publik (Homepage) --}}
                    <a href="{{ route('home') }}"
                        class="flex flex-col items-center text-center p-2 sm:p-3 transition-colors duration-200 {{ request()->routeIs('home') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2h8a2 2 0 002-2v-1a2 2 0 012-2h1.945M7.881 4.109A9 9 0 1112 3a9 9 0 014.119 1.109L12 12 7.881 4.109z">
                            </path>
                        </svg>
                        <span class="text-xs">Publik</span>
                    </a>

                    {{-- Tombol Home (Dashboard Resident) --}}
                    <a href="{{ route('dashboard') }}"
                        class="flex flex-col items-center text-center p-2 sm:p-3 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                            </path>
                        </svg>
                        <span class="text-xs">Home</span>
                    </a>
                </div>

                {{-- Tombol Aksi Utama (Buat Laporan) --}}
                <div class="relative">
                    <a href="{{ route('laporan.create') }}"
                        class="relative -mt-8 flex items-center justify-center w-16 h-16 bg-blue-600 text-white rounded-full shadow-lg border-4 border-white hover:bg-blue-700 transition-transform hover:scale-110">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </a>
                </div>

                {{-- Menu Kanan --}}
                <div class="flex justify-around w-full">
                    {{-- Tombol Laporan Saya --}}
                    <a href="{{ route('laporan.saya') }}"
                        class="flex flex-col items-center text-center p-2 sm:p-3 transition-colors duration-200 {{ request()->routeIs('laporan.*') ? 'text-blue-600' : 'text-gray-500 hover:text-blue-600' }}">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        <span class="text-xs">Laporan</span>
                    </a>

                    <button type="button"
                        class="js-logout-btn w-full flex flex-col items-center text-center text-gray-500 hover:text-red-600 p-2 sm:p-3">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span class="text-xs">Logout</span>
                    </button>
                </div>
            </div>
        </nav>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>

        {{-- Pop-up Media Player (Gambar & Video) --}}
        <div x-data="{ isMediaPlayerOpen: false, mediaUrl: '', mediaType: 'image' }"
            @open-media-viewer.window="
            mediaUrl = $event.detail.url;
            mediaType = $event.detail.type;
            isMediaPlayerOpen = true;
         "
            x-show="isMediaPlayerOpen" x-transition @keydown.escape.window="isMediaPlayerOpen = false"
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

        {{-- Scripts --}}
        <script>
            window.addEventListener('load', function() {
                const skeleton = document.getElementById('skeleton-loader');
                const content = document.getElementById('main-content');
                if (skeleton && content) {
                    skeleton.classList.add('hidden');
                    content.classList.remove('hidden');
                }
            });

            document.addEventListener('DOMContentLoaded', function() {
                @if (session('success'))
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
                    Toast.fire({
                        icon: 'success',
                        title: '{{ session('success') }}'
                    });
                @endif
            });
        </script>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        @livewireScripts
        @stack('scripts')
    </body>

</html>
