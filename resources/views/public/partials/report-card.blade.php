<a href="{{ route('laporan.show', $report) }}"
    class="block bg-white rounded-lg shadow-sm border border-gray-200 p-4 transition-all duration-200 hover:shadow-lg hover:border-blue-300">
    <div class="flex flex-col sm:flex-row items-start">
        @php
            // Ambil media bukti pertama dari tindak lanjut (jika ada)
            $media = $report->followUp?->media->first();
        @endphp

        @if ($media)
            @if ($media->file_type == 'video')
                {{-- Tampilkan sebagai tag <video> jika file adalah video --}}
                <video src="{{ Storage::url($media->file_path) }}"
                    class="w-full sm:w-32 h-32 object-cover rounded-md mb-4 sm:mb-0 sm:mr-4 bg-black flex-shrink-0"
                    preload="metadata"></video>
            @else
                {{-- Tampilkan sebagai tag <img> jika file adalah gambar --}}
                <img src="{{ Storage::url($media->file_path) }}" alt="Foto Bukti"
                    class="w-full sm:w-32 h-32 object-cover rounded-md mb-4 sm:mb-0 sm:mr-4 flex-shrink-0">
            @endif
        @else
            {{-- Tampilan fallback jika tidak ada media bukti --}}
            <div
                class="w-full sm:w-32 h-32 bg-gray-100 rounded-md mb-4 sm:mb-0 sm:mr-4 flex items-center justify-center text-gray-400 flex-shrink-0">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"></path>
                </svg>
            </div>
        @endif

        <div class="flex-1">
            <div class="flex justify-between items-start">
                <h3 class="font-semibold text-gray-800 break-words">{{ $report->title }}</h3>
                <span
                    class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-green-100 text-green-800 ml-2 flex-shrink-0">Selesai</span>
            </div>
            <p class="text-sm text-gray-500 mt-1"><span class="font-semibold">Lokasi:</span>
                {{ $report->location_address }}</p>
            <p class="text-xs text-gray-400 mt-2">Selesai pada:
                {{ $report->completed_at ? \Carbon\Carbon::parse($report->completed_at)->isoFormat('D MMMM YYYY, HH:mm') : '-' }}
            </p>
        </div>
    </div>
</a>
