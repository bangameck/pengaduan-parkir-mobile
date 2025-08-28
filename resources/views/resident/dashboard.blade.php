@extends('layouts.mobile')

@section('title', 'Beranda')

@section('skeleton')
    <x-skeletons.dashboard-resident />
@endsection
@section('content')

    <header class="sticky top-0 z-20">
        <x-resident-header />
    </header>
    {{-- Bagian Header dengan Nama & Foto Profil --}}

    {{-- Kartu Statistik Laporan Milik User --}}
    <div class="p-4 sm:p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-3">Ringkasan Laporan Anda</h2>
        <div class="grid grid-cols-2 gap-4">
            {{-- Total Laporan --}}
            <div class="flex items-start p-4 bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ count: 0 }"
                x-init="let to = {{ $stats['total'] }};
                let i = 0;
                let interval = setInterval(() => {
                    i += Math.ceil(to / 100) || 1;
                    if (i >= to) {
                        i = to;
                        clearInterval(interval);
                    }
                    count = i;
                }, 25);">
                <div class="p-2 bg-blue-100 rounded-full mr-3 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800" x-text="count"></p>
                    <p class="text-sm text-gray-500">Total Laporan</p>
                </div>
            </div>

            {{-- Laporan Diproses --}}
            <div class="flex items-start p-4 bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ count: 0 }"
                x-init="let to = {{ $stats['processed'] }};
                let i = 0;
                let interval = setInterval(() => {
                    i += Math.ceil(to / 100) || 1;
                    if (i >= to) {
                        i = to;
                        clearInterval(interval);
                    }
                    count = i;
                }, 25);">
                <div class="p-2 bg-indigo-100 rounded-full mr-3 text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h5M7 7l1.586-1.586a2 2 0 012.828 0l2 2V16m-6 3h12a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800" x-text="count"></p>
                    <p class="text-sm text-gray-500">Diproses</p>
                </div>
            </div>

            {{-- Laporan Selesai --}}
            <div class="flex items-start p-4 bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ count: 0 }"
                x-init="let to = {{ $stats['completed'] }};
                let i = 0;
                let interval = setInterval(() => {
                    i += Math.ceil(to / 100) || 1;
                    if (i >= to) {
                        i = to;
                        clearInterval(interval);
                    }
                    count = i;
                }, 25);">
                <div class="p-2 bg-green-100 rounded-full mr-3 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800" x-text="count"></p>
                    <p class="text-sm text-gray-500">Selesai</p>
                </div>
            </div>

            {{-- Laporan Ditolak --}}
            <div class="flex items-start p-4 bg-white rounded-lg shadow-sm border border-gray-200" x-data="{ count: 0 }"
                x-init="let to = {{ $stats['rejected'] }};
                let i = 0;
                let interval = setInterval(() => {
                    i += Math.ceil(to / 100) || 1;
                    if (i >= to) {
                        i = to;
                        clearInterval(interval);
                    }
                    count = i;
                }, 25);">
                <div class="p-2 bg-red-100 rounded-full mr-3 text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-800" x-text="count"></p>
                    <p class="text-sm text-gray-500">Ditolak</p>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="p-4 sm:p-6">
        <a href="{{ route('laporan.create') }}"
            class="mt-8 inline-block bg-blue-600 text-white text-lg font-bold py-3 px-8 rounded-lg hover:bg-blue-700 transition-transform hover:scale-105">
            Buat Laporan Sekarang
        </a>
    </div> --}}

    {{-- Daftar Laporan Terakhir --}}
    <div class="p-4 sm:p-6">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-lg font-semibold text-gray-700">Laporan Terakhir Anda</h2>
            <a href="{{ route('laporan.saya') }}" class="text-sm font-medium text-blue-600 hover:underline">Lihat Semua</a>
        </div>
        <div class="space-y-3">
            @forelse ($reports as $report)
                <a href="{{ route('laporan.show', $report) }}" class="block">
                    <div
                        class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 transition-shadow duration-200 hover:shadow-md">
                        <div class="flex items-start">
                            @php $firstMedia = $report->images->first(); @endphp

                            @if ($firstMedia)
                                {{-- Jika media adalah video & punya thumbnail, tampilkan thumbnail --}}
                                @if ($firstMedia->file_type == 'video' && $firstMedia->thumbnail_path)
                                    <div class="relative w-16 h-16 flex-shrink-0 mr-4">
                                        <img src="{{ Storage::url($firstMedia->thumbnail_path) }}" alt="Thumbnail Laporan"
                                            class="w-16 h-16 object-cover rounded-md">
                                        {{-- Ikon Play di atas thumbnail --}}
                                        <div
                                            class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-25 rounded-md">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                    {{-- Jika media adalah gambar --}}
                                @elseif($firstMedia->file_type == 'image')
                                    <img src="{{ Storage::url($firstMedia->file_path) }}" alt="Foto Laporan"
                                        class="w-16 h-16 object-cover rounded-md mr-4 flex-shrink-0">
                                @endif
                            @else
                                {{-- Fallback jika tidak ada media --}}
                                <div class="w-16 h-16 bg-gray-100 rounded-md mr-4 ...">...</div>
                            @endif

                            <div class="flex-1">
                                <div class="flex justify-between items-center mb-1">
                                    <p class="font-semibold text-gray-800 break-words leading-tight">{{ $report->title }}
                                    </p>
                                    {{-- Badge Status --}}
                                    @if ($report->status == 'pending')
                                        <span
                                            class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-yellow-100 text-yellow-800 ml-2">Menunggu</span>
                                    @elseif (in_array($report->status, ['verified', 'in_progress']))
                                        <span
                                            class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 ml-2">Diproses</span>
                                    @elseif ($report->status == 'completed')
                                        <span
                                            class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-green-100 text-green-800 ml-2">Selesai</span>
                                    @elseif ($report->status == 'rejected')
                                        <span
                                            class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-red-100 text-red-800 ml-2">Ditolak</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Dibuat {{ $report->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="p-6 text-center bg-white rounded-lg shadow-sm border border-gray-200">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">Belum Ada Laporan</h3>
                    <p class="mt-1 text-sm text-gray-500">Mulai laporkan pelanggaran parkir di sekitar Anda.</p>
                    <div class="mt-6">
                        <a href="{{ route('laporan.create') }}"
                            class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path
                                    d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Buat Laporan Baru
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
