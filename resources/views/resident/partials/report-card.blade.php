{{-- Desain kartu laporan khusus untuk halaman "Laporan Saya" --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col">
    {{-- Bagian Media (Gambar/Thumbnail Video) --}}
    @php $firstMedia = $report->images->first(); @endphp
    <a href="{{ route('laporan.show', $report) }}" class="block">
        @if ($firstMedia)
            <div class="relative w-full aspect-video bg-gray-100 rounded-t-lg">
                @if ($firstMedia->file_type == 'video' && $firstMedia->thumbnail_path)
                    <img src="{{ Storage::url($firstMedia->thumbnail_path) }}" alt="Thumbnail Laporan"
                        class="w-full h-full object-cover rounded-t-lg">
                    <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-25 rounded-t-lg">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z">
                            </path>
                        </svg>
                    </div>
                @else
                    <img src="{{ Storage::url($firstMedia->file_path) }}" alt="Foto Laporan"
                        class="w-full h-full object-cover rounded-t-lg">
                @endif
            </div>
        @else
            <div class="w-full aspect-video bg-gray-100 rounded-t-lg flex items-center justify-center text-gray-400">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"></path>
                </svg>
            </div>
        @endif
    </a>

    {{-- Bagian Detail Teks --}}
    <div class="p-4 flex-grow">
        <p class="text-xs text-gray-500">{{ $report->report_code }}</p>
        <a href="{{ route('laporan.show', $report) }}" class="block">
            <h3 class="font-bold text-gray-800 hover:text-blue-600 leading-tight mt-1">{{ $report->title }}</h3>
        </a>
        <p class="text-xs text-gray-500 mt-2">Dibuat {{ $report->created_at->diffForHumans() }}</p>
    </div>

    {{-- Bagian Footer Kartu (Status & Aksi dengan Ikon & Tooltip) --}}
    <div class="px-4 py-3 bg-gray-50 border-t rounded-b-lg flex justify-between items-center">
        <div>
            {{-- Badge Status --}}
            @if ($report->status == 'pending')
                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-800">Menunggu
                    Verifikasi</span>
            @elseif (in_array($report->status, ['verified', 'in_progress']))
                <span
                    class="text-xs font-medium px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 capitalize">{{ Str::replace('_', ' ', $report->status) }}</span>
            @elseif ($report->status == 'completed')
                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-green-100 text-green-800">Selesai</span>
            @elseif ($report->status == 'rejected')
                <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-red-100 text-red-800">Ditolak</span>
            @endif
        </div>

        <div class="flex items-center space-x-2">
            {{-- Tombol Edit HANYA muncul jika status 'pending' --}}
            @if ($report->status == 'pending')
                <div x-data="{ tooltip: false }" class="relative">
                    <a href="{{ route('laporan.edit', $report) }}" @mouseenter="tooltip = true"
                        @mouseleave="tooltip = false" class="text-gray-500 hover:text-yellow-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                    </a>
                    <div x-show="tooltip" x-transition
                        class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-800 text-white text-xs rounded-md shadow-lg whitespace-nowrap">
                        Edit Laporan
                    </div>
                </div>
            @endif

            {{-- Tombol Lihat Detail --}}
            <div x-data="{ tooltip: false }" class="relative">
                <a href="{{ route('laporan.show', $report) }}" @mouseenter="tooltip = true"
                    @mouseleave="tooltip = false" class="text-gray-500 hover:text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                </a>
                <div x-show="tooltip" x-transition
                    class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-800 text-white text-xs rounded-md shadow-lg">
                    Lihat Detail
                </div>
            </div>
        </div>
    </div>
</div>
