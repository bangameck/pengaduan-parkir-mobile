@extends('layouts.public')

@section('content')
    {{-- Bagian Hero --}}
    <div class="bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Lapor, Pantau, dan Tertibkan Parkir</h1>
            <p class="mt-4 text-lg text-gray-600 max-w-2xl mx-auto">Bersama wujudkan Pekanbaru yang lebih tertib dan nyaman.
                Laporkan pelanggaran parkir liar di sekitar Anda dengan mudah.</p>
            <a href="{{ route('register') }}"
                class="mt-8 inline-block bg-blue-600 text-white text-lg font-bold py-3 px-8 rounded-lg hover:bg-blue-700 transition-transform hover:scale-105">
                Buat Laporan Sekarang
            </a>
        </div>
    </div>

    {{-- Bagian Statistik dengan Animasi Angka --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Statistik Laporan</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            {{-- Fungsi x-init akan menjalankan animasi saat komponen dimuat --}}
            <a href="{{ route('laporan.publik') }}"
                class="block p-4 bg-white rounded-lg shadow text-center hover:bg-gray-50 transition-colors"
                x-data="{ count: 0 }" x-init="let to = {{ $stats['total'] }};
                let i = 0;
                let interval = setInterval(() => {
                    i += Math.ceil(to / 100);
                    if (i >= to) {
                        i = to;
                        clearInterval(interval);
                    }
                    count = i;
                }, 20);">
                <p class="text-2xl font-bold" x-text="count"></p>
                <p class="text-sm text-gray-500">Total Laporan</p>
            </a>
            <a href="{{ route('laporan.publik', 'in_progress') }}"
                class="block p-4 bg-white rounded-lg shadow text-center hover:bg-gray-50 transition-colors"
                x-data="{ count: 0 }" x-init="let to = {{ $stats['in_progress'] + $stats['verified'] }};
                let i = 0;
                let interval = setInterval(() => {
                    i += Math.ceil(to / 100) || 1;
                    if (i >= to) {
                        i = to;
                        clearInterval(interval);
                    }
                    count = i;
                }, 20);">
                <p class="text-2xl font-bold text-blue-600" x-text="count"></p>
                <p class="text-sm text-gray-500">Diproses</p>
            </a>
            <a href="{{ route('laporan.publik', 'completed') }}"
                class="block p-4 bg-white rounded-lg shadow text-center hover:bg-gray-50 transition-colors"
                x-data="{ count: 0 }" x-init="let to = {{ $stats['completed'] }};
                let i = 0;
                let interval = setInterval(() => {
                    i += Math.ceil(to / 100) || 1;
                    if (i >= to) {
                        i = to;
                        clearInterval(interval);
                    }
                    count = i;
                }, 20);">
                <p class="text-2xl font-bold text-green-600" x-text="count"></p>
                <p class="text-sm text-gray-500">Selesai</p>
            </a>
            <a href="{{ route('laporan.publik', 'pending') }}"
                class="block p-4 bg-white rounded-lg shadow text-center hover:bg-gray-50 transition-colors"
                x-data="{ count: 0 }" x-init="let to = {{ $stats['pending'] }};
                let i = 0;
                let interval = setInterval(() => {
                    i += Math.ceil(to / 100) || 1;
                    if (i >= to) {
                        i = to;
                        clearInterval(interval);
                    }
                    count = i;
                }, 20);">
                <p class="text-2xl font-bold text-yellow-600" x-text="count"></p>
                <p class="text-sm text-gray-500">Menunggu</p>
            </a>
            <a href="{{ route('laporan.publik', 'rejected') }}"
                class="block col-span-2 md:col-span-1 lg:col-span-1 p-4 bg-white rounded-lg shadow text-center hover:bg-gray-50 transition-colors"
                x-data="{ count: 0 }" x-init="let to = {{ $stats['rejected'] }};
                let i = 0;
                let interval = setInterval(() => {
                    i += Math.ceil(to / 100) || 1;
                    if (i >= to) {
                        i = to;
                        clearInterval(interval);
                    }
                    count = i;
                }, 20);">
                <p class="text-2xl font-bold text-red-600" x-text="count"></p>
                <p class="text-sm text-gray-500">Ditolak</p>
            </a>
        </div>
    </div>

    {{-- Bagian Daftar Laporan Selesai --}}
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-16">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Transparansi Kinerja Petugas</h2>
        <livewire:public-report-list />
    </div>
@endsection
