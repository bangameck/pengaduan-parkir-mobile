@extends('layouts.public')

@section('content')
    {{-- Bagian Hero dengan Video Latar Belakang --}}
    <div class="relative bg-dishub-blue-900 text-white overflow-hidden">
        <video autoplay loop muted playsinline class="absolute z-0 w-auto min-w-full min-h-full max-w-none opacity-20">
            <source src="{{ asset('aksi-parkir.mp4') }}" type="video/mp4">
            Browser Anda tidak mendukung tag video.
        </video>
        <div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight">
                <span class="block">Lapor, Pantau, dan Wujudkan</span>
                <span class="block text-dishub-yellow-400 mt-2">Parkir Tertib di Pekanbaru</span>
            </h1>
            <p class="mt-6 text-lg text-blue-200 max-w-2xl mx-auto">
                Aplikasi ini adalah jembatan antara Anda dan kami. Laporkan pelanggaran parkir, dan biarkan petugas kami
                yang bertindak.
            </p>
            <div class="mt-8 flex justify-center gap-4">
                <a href="{{ route('login') }}"
                    class="inline-block bg-dishub-yellow-400 text-dishub-blue-900 text-lg font-bold py-3 px-8 rounded-lg hover:bg-white transition-transform hover:scale-105 shadow-lg">
                    Buat Laporan
                </a>
            </div>
        </div>
    </div>

    {{-- Bagian Statistik --}}
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
            <a href="{{ route('laporan.publik', 'verified') }}"
                class="block p-4 bg-white rounded-lg shadow text-center hover:bg-gray-50 transition-colors"
                x-data="{ count: 0 }" x-init="let to = {{ $stats['verified'] }};
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

    {{-- Carousel Gambar Tindak Lanjut dengan Flowbite --}}
    @if ($carouselImages->isNotEmpty())
        <div class="bg-white py-16">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-800">Galeri Aksi Petugas di Lapangan</h2>
                    <p class="text-gray-500 mt-2">Dokumentasi nyata dari laporan yang telah selesai ditindaklanjuti.</p>
                </div>

                {{-- ======================================================= --}}
                {{-- == AWAL FLOWBITE CAROUSEL == --}}
                {{-- ======================================================= --}}
                <div id="gallery" class="relative w-full" data-carousel="slide">
                    <div class="relative h-56 overflow-hidden rounded-lg sm:h-64 xl:h-80 2xl:h-96">
                        @foreach ($carouselImages as $image)
                            <div class="hidden duration-700 ease-in-out" data-carousel-item>
                                <img src="{{ Storage::url($image->file_path) }}"
                                    class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2"
                                    alt="Foto Tindak Lanjut Petugas">
                            </div>
                        @endforeach
                    </div>
                    <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
                        @foreach ($carouselImages as $index => $image)
                            <button type="button" class="w-3 h-3 rounded-full"
                                aria-current="{{ $loop->first ? 'true' : 'false' }}"
                                aria-label="Slide {{ $index + 1 }}"
                                data-carousel-slide-to="{{ $index }}"></button>
                        @endforeach
                    </div>
                    <button type="button"
                        class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                        data-carousel-prev>
                        <span
                            class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white group-focus:outline-none">
                            <svg class="w-4 h-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 1 1 5l4 4" />
                            </svg>
                            <span class="sr-only">Previous</span>
                        </span>
                    </button>
                    <button type="button"
                        class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none"
                        data-carousel-next>
                        <span
                            class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 group-hover:bg-white/50 group-focus:ring-4 group-focus:ring-white group-focus:outline-none">
                            <svg class="w-4 h-4 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m1 9 4-4-4-4" />
                            </svg>
                            <span class="sr-only">Next</span>
                        </span>
                    </button>
                </div>
                {{-- ======================================================= --}}
                {{-- == AKHIR FLOWBITE CAROUSEL == --}}
                {{-- ======================================================= --}}

            </div>
        </div>
    @endif

    {{-- Bagian Daftar Laporan Publik (Livewire) --}}
    <div class="bg-gray-50 py-16">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Transparansi Kinerja Petugas</h2>
            <livewire:public-report-list />
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const loadTime = (performance.now() / 1000).toFixed(2);
            document.getElementById("loadTime").textContent =
                `⚡ Halaman diload dalam ${loadTime} detik`;
        });
    </script>
@endpush
