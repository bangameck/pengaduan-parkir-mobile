@extends('layouts.public')

@section('title', 'Detail Laporan')

@section('skeleton')
    <x-skeletons.detail-laporan />
@endsection

@section('content')
    {{-- Header Halaman --}}
    {{-- <x-resident-header /> --}}
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
                    <dt class="text-xs font-medium text-gray-500">Pelapor</dt>
                    <dd class="text-sm font-medium text-gray-800"><b>{{ $report->report_name }}</b> via
                        {{ ucfirst($report->source) }}</dd>
                </div>
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

        <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-200" x-data="socialShare()">
            <h3 class="text-md font-semibold text-gray-800 mb-4">Bagikan Laporan Ini</h3>
            <div class="flex flex-wrap items-center gap-4">

                {{-- Tombol WhatsApp --}}
                <a :href="`https://api.whatsapp.com/send?text=${shareText}`" target="_blank"
                    class="social-share-button bg-green-50 text-green-600 shadow-green-100 hover:bg-green-100">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.894 11.892-1.99-.001-3.956-.539-5.688-1.588l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.886-.001 2.269.655 4.318 1.804 6.043l-1.205 4.428 4.555-1.196z" />
                    </svg>
                </a>

                {{-- Tombol Facebook --}}
                <a :href="`https://www.facebook.com/sharer/sharer.php?u=${pageUrl}`" target="_blank"
                    class="social-share-button bg-blue-50 text-blue-800 shadow-blue-100 hover:bg-blue-100">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v2.385z" />
                    </svg>
                </a>

                {{-- Tombol Twitter/X --}}
                <a :href="`https://twitter.com/intent/tweet?url=${pageUrl}&text=${shareText}`" target="_blank"
                    class="social-share-button bg-gray-800 text-white shadow-gray-400/50 hover:bg-black">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                    </svg>
                </a>

                {{-- Tombol Telegram --}}
                <a :href="`https://t.me/share/url?url=${pageUrl}&text=${shareText}`" target="_blank"
                    class="social-share-button bg-sky-50 text-sky-600 shadow-sky-100 hover:bg-sky-100">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-2.25 14.542c-.41.21-1.23.63-1.84.92-.61.29-1.05.4-1.38.4-.56 0-.8-.21-.8-.85 0-.67.24-1.4.74-2.1l2.4-2.73 1.48-1.68c.2-.23.4-.46.4-.66s-.23-.33-.5-.33c-.23 0-.6 0-1.1.17l-5.17 1.7c-.5.17-.8.34-1.2.5-.4.17-.8.34-.8.75s.4.58.9.58c.4 0 .7-.13 1.1-.3l4.2-2.8-1.9 4.1c-.5 1.1-.9 2.2-1.3 3.3-.4 1.1-.8 1.6-1.3 1.6-.5 0-.8-.25-1.2-.75s-.8-1.08-1.2-1.7c-.4-.62-.8-1-1.2-1-.4 0-.6.17-.6.5s.4.67.9.92c.5.25 1.1.58 1.6.92s1.1.67 1.6.67c.9 0 1.6-.42 2.1-1.25.5-.83 1-1.9 1.4-3.2l.6-1.8.8 1c.5.58 1.1 1.17 1.6 1.75s1.1.92 1.6.92c.6 0 1.1-.25 1.4-.75.3-.5.4-1.1.4-1.8s-.1-1.3-.4-1.9c-.3-.6-.8-1.1-1.4-1.5-.6-.4-1.3-.7-2.1-.9-.8-.2-1.7-.3-2.6-.3-.9 0-1.7.1-2.4.4-.7.3-1.3.6-1.7 1-.4.4-.7.8-.8 1.3s0 .9.2 1.2z" />
                    </svg>
                </a>

                {{-- Tombol Salin Link --}}
                <button @click="copyLink"
                    class="social-share-button bg-gray-50 text-gray-600 shadow-gray-200 hover:bg-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </button>

                <span x-show="showFeedback" x-transition:enter.duration.300ms x-transition:leave.duration.500ms
                    class="text-sm font-semibold text-green-600 self-center">
                    Tautan berhasil disalin!
                </span>
            </div>
        </div>

        {{-- Kartu Tindak Lanjut (Tampil Jika Sudah Selesai) --}}
        @if ($report->status == 'completed' && $report->followUp)
            <div class="p-4 bg-white rounded-lg shadow-sm border-2 border-green-500 space-y-4">
                <h2 class="text-lg font-bold text-green-600">✅ Laporan Selesai Ditindaklanjuti</h2>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-xs font-medium text-gray-500">Ditindaklanjuti oleh Tim Petugas:</dt>
                        <dd class="mt-2 flex flex-wrap gap-4">
                            @forelse($report->followUp->officers as $officer)
                                <div class="flex items-center gap-2">
                                    <img class="h-8 w-8 rounded-full object-cover"
                                        src="{{ $officer->image ? Storage::url($officer->image) : 'https://ui-avatars.com/api/?name=' . urlencode($officer->name) . '&background=EBF4FF&color=76A9FA' }}"
                                        alt="{{ $officer->name }}">
                                    <span class="text-sm font-semibold text-gray-800">{{ $officer->name }}</span>
                                </div>
                            @empty
                                <span class="text-sm font-semibold text-gray-800">-</span>
                            @endforelse
                        </dd>
                    </div>

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

                {{-- ================================================================ --}}
                {{-- == PERBAIKAN 3: Mengurutkan Riwayat dari yang Terbaru (Desc) == --}}
                {{-- ================================================================ --}}
                @foreach ($report->statusHistories->sortByDesc('created_at') as $history)
                    <li class="mb-8 ml-4">
                        <div class="absolute w-3 h-3 bg-blue-500 rounded-full mt-1.5 -left-1.5 border border-white"></div>
                        <time
                            class="mb-1 text-sm font-normal leading-none text-gray-400">{{ $history->created_at->isoFormat('dddd, D MMMM YYYY - HH:mm') }}</time>
                        <h3 class="text-md font-semibold text-gray-900 capitalize">Status:
                            {{ str_replace('_', ' ', $history->status) }}</h3>
                        <p class="text-sm font-normal text-gray-500">{{ $history->notes }}</p>

                        {{-- ========================================================== --}}
                        {{-- == PERBAIKAN 2: Menampilkan Nama Pelaku di setiap Riwayat == --}}
                        {{-- ========================================================== --}}
                        @if ($history->user)
                            @if ($history->status === 'pending')
                                <p class="text-xs font-medium text-gray-400 mt-1">oleh: {{ $history->report->reportName }}
                                    via {{ ucfirst($report->source) }}
                                    ({{ str_replace('-', ' ', $history->user->role->name) }})
                                </p>
                            @elseif ($history->status === 'completed')
                                <p class="text-xs font-medium text-gray-400 mt-1">oleh: {{ $history->user->name }}, dkk
                                    ({{ str_replace('-', ' ', $history->user->role->name) }})
                                </p>
                            @else
                                <p class="text-xs font-medium text-gray-400 mt-1">oleh: {{ $history->user->name }}
                                    ({{ str_replace('-', ' ', $history->user->role->name) }})
                                </p>
                            @endif
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('socialShare', () => ({
                pageUrl: encodeURIComponent(window.location.href),
                shareText: '',
                showFeedback: false,

                init() {
                    // ## PERBAIKAN DI SINI: Berikan nilai default jika null ##
                    const fullDescription = @js($report->description ?? 'Tidak ada deskripsi.');
                    const reportTitle = @js('Laporan #' . ($report->report_code ?? 'N/A') . ': ' . ($report->title ?? 'Tanpa Judul'));
                    const pageLink = window.location.href;

                    let descriptionSnippet = fullDescription;

                    // Batasi deskripsi jika lebih dari 150 karakter
                    if (fullDescription.length > 150) {
                        descriptionSnippet = fullDescription.substring(0, 150) +
                            '... (Lihat selengkapnya)';
                    }

                    // Buat teks pesan yang akan dibagikan
                    const message = `${reportTitle}\n\n"${descriptionSnippet}"\n\n${pageLink}`;

                    this.shareText = encodeURIComponent(message);
                },

                copyLink() {
                    navigator.clipboard.writeText(decodeURIComponent(this.pageUrl)).then(() => {
                        this.showFeedback = true;
                        setTimeout(() => {
                            this.showFeedback = false;
                        }, 2000);
                    });
                }
            }));
        });
    </script>

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
