<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app_name', 'Aplikasi Pengaduan Parkir Pekanbaru') }} - Ruang Kendali</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- SweetAlert2 via CDN (jika Anda menggunakan metode ini) --}}
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

         <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

    </head>

    <body class="font-sans antialiased bg-gray-100">
        <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-200">

            {{-- ====================================================================== --}}
            {{-- == PERBAIKAN 1: Letakkan Overlay di sini & tambahkan style display none == --}}
            {{-- ====================================================================== --}}
            <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
                class="fixed inset-0 z-20 bg-black bg-opacity-50 transition-opacity lg:hidden" style="display: none;">
            </div>

            <aside
                class="fixed inset-y-0 left-0 z-30 w-64 overflow-y-auto transition duration-300 transform -translate-x-full bg-dishub-blue-800 lg:translate-x-0 lg:static lg:inset-0"
                :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }">

                {{-- Header Sidebar --}}
                <div class="flex items-start justify-between mt-8 px-4">
                    {{-- Logo dan Nama Aplikasi di Sidebar --}}
                    <div class="flex flex-col items-center flex-1">
                        <a href="{{ route('dashboard') }}">
                            <img src="{{ asset('logo-parkir.png') }}" alt="Logo ParkirPKU" class="w-16 h-16 mx-auto">
                        </a>
                        <div class="text-center text-white mt-2">
                            <h1 class="text-lg font-bold">PENGADUAN PARKIR</h1>
                            <p class="text-xs text-dishub-yellow-300">UPT Perparkiran Kota Pekanbaru</p>
                        </div>
                    </div>

                    {{-- ========================================================== --}}
                    {{-- == PERBAIKAN 2: Letakkan Tombol Tutup di dalam header sidebar == --}}
                    {{-- ========================================================== --}}
                    <button @click="sidebarOpen = false" class="text-gray-400 hover:text-white lg:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                @include('layouts.navigation')

            </aside>

            <div class="flex-1 flex flex-col overflow-hidden">
                {{-- Header Utama (tidak diubah) --}}
                <header
                    class="flex items-center justify-between px-6 py-4 bg-white border-b-2 border-dishub-yellow-300">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                        <div class="relative mx-4 lg:mx-0">
                            <h1 class="text-lg font-semibold text-dishub-blue-800">Ruang Kendali Kantor</h1>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div x-data="{ dropdownOpen: false }" class="relative mx-4">
                            {{-- Tombol Lonceng --}}
                            <button @click="dropdownOpen = !dropdownOpen"
                                class="relative text-gray-600 focus:outline-none">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M15 17H20L18.5951 15.5951C18.2141 15.2141 18 14.6973 18 14.1585V11C18 8.38757 16.3304 6.16509 14 5.34142V5C14 3.89543 13.1046 3 12 3C10.8954 3 10 3.89543 10 5V5.34142C7.66962 6.16509 6 8.38757 6 11V14.1585C6 14.6973 5.78595 15.2141 5.40493 15.5951L4 17H9M12 21C12.5523 21 13 20.5523 13 20V19H11V20C11 20.5523 11.4477 21 12 21Z"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </svg>

                                {{-- Badge Notifikasi --}}
                                @if (isset($notificationCount) && $notificationCount > 0)
                                    <span
                                        class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                                        {{ $notificationCount }}
                                    </span>
                                @endif
                            </button>

                            {{-- Latar belakang gelap saat dropdown terbuka --}}
                            <div x-show="dropdownOpen" @click="dropdownOpen = false"
                                class="fixed inset-0 z-10 w-full h-full" style="display: none;"></div>

                            {{-- Panel Dropdown --}}
                            <div x-show="dropdownOpen" x-transition
                                class="absolute right-0 z-20 w-80 mt-2 overflow-hidden bg-white rounded-lg shadow-xl"
                                style="display: none;">

                                <div class="p-4 border-b">
                                    <h4 class="font-semibold text-gray-800">Notifikasi</h4>
                                    @if ($notificationCount > 0)
                                        <p class="text-sm text-gray-600">Anda memiliki {{ $notificationCount }} laporan
                                            baru.</p>
                                    @else
                                        <p class="text-sm text-gray-600">Tidak ada notifikasi baru.</p>
                                    @endif
                                </div>

                                {{-- DAFTAR NOTIFIKASI DINAMIS DENGAN @forelse --}}
                                <div class="max-h-64 overflow-y-auto">
                                    @forelse ($notifications as $notification)
                                        <a href="#" {{-- Nantinya bisa ke route('admin.laporan.show', $notification->id) --}}
                                            class="flex items-center px-4 py-3 text-sm text-gray-600 hover:bg-gray-100 -mx-2 border-b">
                                            <div class="ml-3">
                                                <p class="font-semibold text-gray-700">Laporan
                                                    #{{ $notification->report_code }}
                                                    perlu ditindaklanjuti.</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center">
                                            <p class="text-sm text-gray-500">Semua laporan sudah tertangani! 🎉</p>
                                        </div>
                                    @endforelse
                                </div>
                                {{-- AKHIR DAFTAR DINAMIS --}}

                                {{-- LINK KE ROUTE YANG BENAR --}}
                                <a href="{{ route('admin.laporan.index') }}"
                                    class="block w-full px-4 py-3 font-semibold text-center text-sm text-dishub-blue-800 bg-gray-50 hover:bg-gray-100">
                                    Lihat Semua Laporan
                                </a>
                            </div>
                        </div>

                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = !dropdownOpen"
                                class="relative block h-8 w-8 overflow-hidden rounded-full shadow focus:outline-none">
                                <img class="object-cover w-full h-full"
                                    src="{{ Auth::user()->image ? Storage::url(Auth::user()->image) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=d9ecff&color=0058e1' }}"
                                    alt="Your avatar">
                            </button>

                            <div x-show="dropdownOpen" @click="dropdownOpen = false"
                                class="fixed inset-0 z-10 w-full h-full" style="display: none;"></div>

                            <div x-show="dropdownOpen"
                                class="absolute right-0 z-10 w-48 mt-2 overflow-hidden bg-white rounded-md shadow-xl"
                                style="display: none;">
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-dishub-blue-600 hover:text-white">Profile</a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-dishub-blue-600 hover:text-white">
                                        {{ __('Log Out') }}
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100">
                    <div class="container mx-auto px-6 py-8">
                        @if (isset($header))
                            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                                {{ $header }}
                            </div>
                        @endif
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
    @livewireScripts
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    @stack('scripts')

</html>
