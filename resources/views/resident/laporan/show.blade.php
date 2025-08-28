@extends('layouts.mobile')

@section('title', 'Detail Laporan')

@section('skeleton')
    <x-skeletons.detail-laporan />
@endsection

@section('content')
    {{-- Header Halaman --}}
    <x-resident-header />
    <x-page-header>Laporan #{{ $report->report_code }}</x-page-header>

    {{-- Konten Detail Laporan --}}
    <div class="p-4 sm:p-6 space-y-6">

        {{-- Galeri Dokumentasi Laporan Awal --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h3 class="text-md font-semibold text-gray-800 mb-3">Dokumentasi Laporan Awal</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                @forelse($report->images as $media)
                    <a href="{{ route('media.stream', ['path' => $media->file_path]) }}"
                        class="relative block aspect-square group js-media-viewer-trigger"
                        data-media-type="{{ $media->file_type }}">

                        <img src="{{ $media->file_type == 'video' ? ($media->thumbnail_path ? Storage::url($media->thumbnail_path) : 'https://via.placeholder.com/300') : Storage::url($media->file_path) }}"
                            alt="Media Laporan"
                            class="w-full h-full object-cover rounded-md transition-transform group-hover:scale-105">

                        @if ($media->file_type == 'video')
                            <div
                                class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30 rounded-md transition-opacity group-hover:bg-opacity-20">
                                <svg class="w-10 h-10 text-white opacity-80" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z">
                                    </path>
                                </svg>
                            </div>
                        @endif
                    </a>
                @empty
                    <p class="text-sm text-gray-500 col-span-full">Tidak ada dokumentasi yang dilampirkan.</p>
                @endforelse
            </div>
        </div>

        {{-- Kartu Detail Utama --}}
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-lg font-bold text-gray-800">Detail Laporan</h2>
                @if ($report->status == 'pending')
                    <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-yellow-100 text-yellow-800">Menunggu
                        Verifikasi</span>
                @elseif (in_array($report->status, ['verified', 'in_progress']))
                    <span
                        class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 capitalize">{{ Str::replace('_', ' ', $report->status) }}</span>
                @elseif ($report->status == 'completed')
                    <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-green-100 text-green-800">Selesai</span>
                @elseif ($report->status == 'rejected')
                    <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-red-100 text-red-800">Ditolak</span>
                @endif
            </div>
            <dl class="space-y-3">
                <div>
                    <dt class="text-xs font-medium text-gray-500">Judul Laporan</dt>
                    <dd class="text-sm font-semibold text-gray-800">{{ $report->title }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500">Waktu Laporan</dt>
                    <dd class="text-sm text-gray-800">{{ $report->created_at->format('d F Y, H:i') }}
                        ({{ $report->created_at->diffForHumans() }})</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500">Lokasi</dt>
                    <dd class="text-sm text-gray-800">{{ $report->location_address }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-medium text-gray-500">Deskripsi</dt>
                    <dd class="text-sm text-gray-800 whitespace-pre-wrap">{{ $report->description }}</dd>
                </div>
            </dl>
        </div>

        {{-- Kartu Tindak Lanjut (Tampil Jika Sudah Selesai) --}}
        @if ($report->status == 'completed' && $report->followUp)
            <div class="p-4 bg-white rounded-lg shadow-sm border-2 border-green-500 space-y-4">
                <h2 class="text-lg font-bold text-green-600">✅ Laporan Selesai Ditindaklanjuti</h2>
                <dl class="space-y-4">
                    @if ($report->followUp->officer)
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Petugas Lapangan</dt>
                            <dd class="text-sm font-semibold text-gray-800">{{ $report->followUp->officer->name }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Catatan Petugas</dt>
                        <dd class="text-sm text-gray-800 whitespace-pre-wrap">{{ $report->followUp->notes }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Titik Koordinat Penyelesaian</dt>
                        <dd class="mt-1">
                            <div id="map" class="w-full h-48 rounded-lg shadow-inner border"></div>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 mb-2">Dokumentasi Penyelesaian</dt>
                        <dd class="grid grid-cols-2 gap-2">
                            @forelse($report->followUp->media as $media)
                                <a href="{{ route('media.stream', ['path' => $media->file_path]) }}"
                                    class="relative block aspect-square group js-media-viewer-trigger"
                                    data-media-type="{{ $media->file_type }}">
                                    <img src="{{ $media->file_type == 'video' ? ($media->thumbnail_path ? Storage::url($media->thumbnail_path) : 'https://via.placeholder.com/300') : Storage::url($media->file_path) }}"
                                        alt="Media Bukti"
                                        class="w-full h-full object-cover rounded-md transition-transform group-hover:scale-105">
                                    @if ($media->file_type == 'video')
                                        <div
                                            class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30 rounded-md transition-opacity group-hover:bg-opacity-20">
                                            <svg class="w-10 h-10 text-white opacity-80" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path
                                                    d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </a>
                            @empty
                                <p class="text-sm text-gray-500 col-span-2">Tidak ada media dokumentasi.</p>
                            @endforelse
                        </dd>
                    </div>
                </dl>
            </div>
        @endif

        {{-- Kartu Riwayat Laporan (Timeline Dinamis) --}}
        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Riwayat Laporan</h2>
            <ol class="relative border-l border-gray-200">
                @foreach ($report->statusHistories->sortBy('created_at') as $history)
                    <li class="mb-6 ml-4">
                        <div class="absolute w-3 h-3 bg-blue-500 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                        <time
                            class="mb-1 text-sm font-normal leading-none text-gray-400">{{ $history->created_at->isoFormat('dddd, D MMMM YYYY - HH:mm') }}</time>
                        <h3 class="text-md font-semibold text-gray-900">Status:
                            {{ Str::ucfirst(str_replace('_', ' ', $history->status)) }}</h3>
                        <p class="text-sm font-normal text-gray-500">{{ $history->notes }}</p>
                    </li>
                @endforeach
                {{-- <li class="ml-4">
                    <div class="absolute w-3 h-3 bg-gray-300 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                    <time
                        class="mb-1 text-sm font-normal leading-none text-gray-400">{{ $report->created_at->isoFormat('dddd, D MMMM YYYY - HH:mm') }}</time>
                    <h3 class="text-md font-semibold text-gray-900">Laporan Dibuat</h3>
                    <p class="text-sm font-normal text-gray-500">Laporan berhasil dikirim dan menunggu verifikasi.</p>
                </li> --}}
            </ol>
        </div>
    </div>
@endsection

@push('scripts')
    @if ($report->status == 'completed' && $report->followUp)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mapElement = document.getElementById('map');
                if (mapElement) {
                    try {
                        const lat = {{ $report->followUp->proof_latitude }};
                        const lon = {{ $report->followUp->proof_longitude }};
                        const map = L.map(mapElement).setView([lat, lon], 16);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);
                        L.marker([lat, lon]).addTo(map).bindPopup('Lokasi tindak lanjut petugas.');
                    } catch (e) {
                        console.error('Gagal menginisialisasi peta Leaflet:', e);
                        mapElement.innerHTML =
                            '<div class="p-4 text-center text-red-500 bg-red-50">Gagal memuat peta.</div>';
                    }
                }
            });
        </script>
    @endif
@endpush
