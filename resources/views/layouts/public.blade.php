<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app_name', 'Aplikasi Pengaduan Parkir Pekanbaru') }} - Ruang Kendali</title>
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

    <body class="bg-gray-50">

        @php
            $notificationCount = $notificationCount ?? 0;
            $notifications = $notifications ?? collect();
        @endphp

        {{-- Header Publik --}}
        <header
            class="sticky top-0 z-50 backdrop-blur-lg bg-gradient-to-r from-blue-800/80 via-blue-900/80 to-blue-800/80 shadow-lg border-b border-white/10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">

                    {{-- Kiri: Logo --}}
                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" class="flex items-center gap-3">
                            <img src="{{ asset('logo-parkir.png') }}" alt="Logo ParkirPKU"
                                class="h-10 sm:h-12 drop-shadow-md">
                            <span class="font-bold text-white tracking-wide text-lg">
                                SiParkir<span class="text-yellow-400">Kita</span>
                            </span>
                        </a>
                    </div>

                    {{-- Kanan --}}
                    <div class="flex items-center gap-4">

                        @auth
                            {{-- Notifikasi Lonceng --}}
                            <div class="relative" x-data="{ dropdownOpen: false }">
                                <button @click="dropdownOpen = !dropdownOpen" class="relative focus:outline-none">
                                    <svg class="w-7 h-7 text-white hover:text-yellow-300 transition" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                                                                       c0-3.07-1.64-5.64-4.5-6.32V4a1.5 1.5 0 00-3 0v.68C7.64 5.36 6 7.92 6 11v3.159
                                                                       c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>

                                    {{-- Badge count --}}
                                    @if (Auth::user()->role->name === 'resident' && $notificationCount > 0)
                                        <span
                                            class="absolute -top-1 -right-1 flex items-center justify-center
                                        w-5 h-5 text-xs font-bold text-white
                                        bg-red-600 rounded-full shadow-md animate-bounce">
                                            {{ $notificationCount }}
                                        </span>
                                    @endif
                                </button>

                                {{-- Dropdown Notifikasi --}}
                                <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2"
                                    class="absolute right-0 mt-3 w-80 rounded-xl shadow-xl overflow-hidden
                                   bg-white/95 backdrop-blur-lg border border-gray-200 z-50">

                                    <div class="p-4 border-b">
                                        <h4 class="font-semibold text-gray-800">Notifikasi</h4>
                                        @if ($notificationCount > 0)
                                            <p class="text-sm text-gray-600">Anda memiliki {{ $notificationCount }}
                                                notifikasi laporan.</p>
                                        @else
                                            <p class="text-sm text-gray-600">Tidak ada notifikasi baru.</p>
                                        @endif
                                    </div>

                                    <div class="max-h-64 overflow-y-auto">
                                        @forelse ($notifications as $notification)
                                            <a href="{{ route('resident.reports.show', $notification->id) }}"
                                                class="flex items-start px-4 py-3 text-sm text-gray-600 hover:bg-gray-100 -mx-2 border-b">

                                                {{-- Icon status --}}
                                                @if ($notification->status === 'verified')
                                                    <span class="w-2.5 h-2.5 mt-2 rounded-full bg-green-500"></span>
                                                @elseif ($notification->status === 'completed')
                                                    <span class="w-2.5 h-2.5 mt-2 rounded-full bg-blue-500"></span>
                                                @elseif ($notification->status === 'rejected')
                                                    <span class="w-2.5 h-2.5 mt-2 rounded-full bg-red-500"></span>
                                                @endif

                                                <div class="ml-3">
                                                    <p class="font-semibold text-gray-700">
                                                        Laporan #{{ $notification->report_code }}
                                                    </p>

                                                    <p class="text-xs">
                                                        @if ($notification->status === 'verified')
                                                            <span class="text-green-600">✅ Diverifikasi petugas</span>
                                                        @elseif ($notification->status === 'completed')
                                                            <span class="text-blue-600">🎉 Selesai ditangani</span>
                                                        @elseif ($notification->status === 'rejected')
                                                            <span class="text-red-600">❌ Ditolak</span>
                                                        @endif
                                                    </p>

                                                    <p class="text-xs text-gray-500">
                                                        {{ $notification->updated_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="px-4 py-8 text-center">
                                                <p class="text-sm text-gray-500">Belum ada notifikasi terbaru.</p>
                                            </div>
                                        @endforelse
                                    </div>

                                    <a href="{{ route('laporan.saya') }}"
                                        class="block w-full px-4 py-3 font-semibold text-center text-sm text-dishub-blue-800 bg-gray-50 hover:bg-gray-100">
                                        Lihat Semua Laporan
                                    </a>
                                </div>
                            </div>

                            {{-- Avatar + Dropdown --}}
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="relative flex items-center focus:outline-none">
                                    <img class="w-11 h-11 rounded-full border-2 border-white/30 shadow-md"
                                        src="{{ Auth::user()->image ? Storage::url(Auth::user()->image) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=1E3A8A&color=fff' }}"
                                        alt="Profile Picture">
                                </button>

                                {{-- Dropdown Profil --}}
                                <div x-show="open" @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2"
                                    class="absolute right-0 mt-3 w-56 rounded-xl shadow-xl overflow-hidden
                                   bg-white/95 backdrop-blur-lg border border-gray-200 z-50">

                                    <div class="px-4 py-3 border-b">
                                        <p class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</p>
                                        <p class="text-xs text-gray-500">{{ '@' . Auth::user()->username }}</p>
                                        <p class="text-xs text-gray-500">+{{ Auth::user()->phone_number }}</p>
                                    </div>

                                    <a href="{{ route('profile.edit') }}"
                                        class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                                        ✏️ Edit Profil
                                    </a>

                                    <button type="button" data-menu="logout"
                                        class="w-full text-left js-logout-btn block px-4 py-3 text-sm text-red-600 hover:bg-red-100">
                                        🚪 Logout
                                    </button>
                                </div>
                            </div>
                        @else
                            {{-- Kalau belum login --}}
                            <a href="{{ route('login') }}"
                                class="text-sm font-semibold text-white hover:text-yellow-300 transition">
                                Login
                            </a>
                            <a href="{{ route('register') }}"
                                class="inline-block bg-yellow-400 text-blue-900 text-sm font-bold py-2 px-4 rounded-xl
                               hover:bg-yellow-500 transition shadow-md">
                                Register
                            </a>
                        @endauth

                    </div>
                </div>
            </div>
        </header>



        <main>
            @yield('content')
        </main>
        <br>
        <br>
        <br>
        {{-- Footer Publik --}}
        @auth
            <nav
                class="fixed bottom-4 left-1/2 -translate-x-1/2
           w-[95%] max-w-md bg-blue-600/70 backdrop-blur-md
           shadow-lg rounded-2xl border border-white/20 z-40">

                <div class="relative flex items-center justify-around h-16 text-white" id="dock-nav">

                    {{-- Menu Publik --}}
                    <a href="{{ route('home') }}" data-menu="home"
                        class="nav-item flex flex-col items-center text-center p-2 sm:p-3 transition-all duration-200
           {{ request()->routeIs('home') ? 'text-yellow-300' : 'hover:text-yellow-200' }}">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2h8a2 2 0 002-2v-1a2 2 0 012-2h1.945M7.881 4.109A9 9 0 1112 3a9 9 0 014.119 1.109L12 12 7.881 4.109z">
                            </path>
                        </svg>
                        <span class="text-xs">Publik</span>
                    </a>

                    {{-- Menu Dashboard --}}
                    <a href="{{ route('dashboard') }}" data-menu="dashboard"
                        class="nav-item flex flex-col items-center text-center p-2 sm:p-3 transition-all duration-200
           {{ request()->routeIs('dashboard') ? 'text-yellow-300' : 'hover:text-yellow-200' }}">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                            </path>
                        </svg>
                        <span class="text-xs">Home</span>
                    </a>

                    {{-- Tombol Aksi Utama --}}
                    <div class="relative">
                        <a href="{{ route('laporan.create') }}"
                            class="relative -mt-10 flex items-center justify-center w-16 h-16
                       bg-yellow-400 text-blue-900 rounded-full shadow-lg
                       border-4 border-white/30 hover:bg-yellow-500
                       transition-transform hover:scale-110">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </a>
                    </div>

                    {{-- Menu Laporan --}}
                    <a href="{{ route('laporan.saya') }}" data-menu="laporan"
                        class="nav-item flex flex-col items-center text-center p-2 sm:p-3 transition-all duration-200
           {{ request()->routeIs('laporan.*') ? 'text-yellow-300' : 'hover:text-yellow-200' }}">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        <span class="text-xs">Laporan</span>
                    </a>

                    {{-- Logout --}}
                    <button type="button" data-menu="logout"
                        class="nav-item js-logout-btn flex flex-col items-center text-center
                   text-gray-200 hover:text-red-400 p-2 sm:p-3 transition-all duration-200">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span class="text-xs">Logout</span>
                    </button>

                    {{-- Sliding Indicator --}}
                    <span id="nav-indicator"
                        class="absolute bottom-1 w-6 h-1 rounded-full bg-yellow-300 transition-all duration-300"></span>
                </div>
            </nav>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>

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
        @endauth
        @guest
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
            <footer class="fixed bottom-0 left-0 right-0 z-50
           bg-dishub-blue-800 text-white shadow-lg">

                <!-- Garis Gradient Tipis -->
                <div
                    class="absolute top-0 left-0 w-full h-1
                bg-gradient-to-r from-dishub-yellow-400 via-dishub-blue-400 to-dishub-yellow-300">
                </div>

                <div class="container mx-auto py-4 px-4 text-center text-sm relative">
                    <!-- Judul Aplikasi -->
                    <p class="font-semibold drop-shadow">
                        &copy; {{ date('Y') }} Aplikasi Pengaduan Perparkiran Kota Pekanbaru
                    </p>

                    <!-- Tim IT -->
                    <p class="text-white font-medium tracking-wide mt-1">
                        Tim IT <span class="text-dishub-yellow-200 font-bold">UPT Perparkiran</span>
                    </p>

                    <!-- Load Time -->
                    <p id="loadTime" class="text-xs text-dishub-yellow-100 mt-1"></p>
                </div>
            </footer>

            @if (config('popup_enabled') == '1' && config('popup_image'))
                <div x-data="{
                    showModal: false,
                    init() {
                        // Cek sessionStorage. Jika belum pernah lihat DI SESI INI, tampilkan modal.
                        if (!sessionStorage.getItem('popupWasShown')) {
                            // Beri jeda 2 detik sebelum menampilkan modal agar tidak terlalu tiba-tiba
                            setTimeout(() => { this.showModal = true; }, 2000);
                        }
                    },
                    closeModal() {
                        this.showModal = false;
                        // Tandai bahwa user sudah pernah lihat DI SESI INI, agar tidak muncul lagi saat refresh.
                        sessionStorage.setItem('popupWasShown', 'true');
                    }
                }" x-init="init()" x-show="showModal"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60 p-4" x-cloak>

                    <div @click.away="closeModal()"
                        class="relative bg-white rounded-lg shadow-xl max-w-lg w-full transform transition-all"
                        x-show="showModal" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                        <!-- Tombol Tutup (X) -->
                        <button @click="closeModal()"
                            class="absolute -top-3 -right-3 bg-white rounded-full p-1 text-gray-500 hover:text-gray-800 z-10 shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <!-- Gambar Banner -->
                        <img src="{{ Storage::url(config('popup_image')) }}" alt="{{ config('popup_title') }}"
                            class="w-full h-auto object-cover rounded-t-lg">

                        <!-- Konten Teks -->
                        <div class="p-6 text-center">
                            <h3 class="text-xl font-bold text-gray-800">{{ config('popup_title', 'Informasi Penting') }}
                            </h3>
                            <p class="text-gray-600 mt-2">{{ config('popup_text', '') }}</p>
                            @if (config('popup_button_text') && config('popup_button_url'))
                                <a href="{{ config('popup_button_url') }}" target="_blank"
                                    class="mt-6 inline-block bg-dishub-blue-800 text-white font-bold py-3 px-8 rounded-lg hover:bg-dishub-blue-900 transition-colors shadow-md hover:shadow-lg">
                                    {{ config('popup_button_text') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

        @endguest
        <script>
            window.addEventListener('load', function() {
                const skeleton = document.getElementById('skeleton-loader');
                const content = document.getElementById('main-content');
                if (skeleton && content) {
                    skeleton.classList.add('hidden');
                    content.classList.remove('hidden');
                }
            });

            document.addEventListener("DOMContentLoaded", () => {
                const indicator = document.getElementById("nav-indicator");
                const items = document.querySelectorAll(".nav-item");

                function moveIndicator(el) {
                    const rect = el.getBoundingClientRect();
                    const parentRect = el.parentElement.getBoundingClientRect();
                    indicator.style.left = (rect.left - parentRect.left + rect.width / 2 - indicator.offsetWidth / 2) +
                        "px";
                }

                // Set posisi awal sesuai route aktif
                const active = document.querySelector(".nav-item.text-yellow-300");
                if (active) moveIndicator(active);

                // Update saat klik
                items.forEach(item => {
                    item.addEventListener("click", () => moveIndicator(item));
                });
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

        <style>
            /* Fade-in animation */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in {
                animation: fadeIn 1s ease-out;
            }
        </style>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
        @livewireScripts
        @stack('scripts')
    </body>

</html>
