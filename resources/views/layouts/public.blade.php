<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'ParkirPKU') }} - Pengaduan Parkir Pekanbaru</title>
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

        {{-- Header Publik --}}
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div>
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('logo-parkir.png') }}" alt="Logo ParkirPKU" class="h-10">
                        </a>
                    </div>
                    <div>
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="text-sm font-semibold text-gray-600 hover:text-gray-900">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-sm font-semibold text-gray-600 hover:text-gray-900 mr-4">Log in</a>
                            <a href="{{ route('register') }}"
                                class="inline-block bg-blue-600 text-white text-sm font-bold py-2 px-4 rounded-lg hover:bg-blue-700">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        {{-- Footer Publik --}}
        <footer class="bg-white border-t mt-12">
            <div class="container mx-auto py-6 px-4 text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} Aplikasi Pengaduan Perparkiran Kota Pekanbaru.
            </div>
        </footer>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        @livewireScripts
    </body>

</html>
