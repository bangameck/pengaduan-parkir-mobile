{{--
    File: resources/views/public/partials/report-card.blade.php
    Deskripsi: Kartu laporan dinamis yang bisa menampilkan berbagai status.
--}}
@php
    // PERUBAHAN 1: Siapkan data dinamis untuk status badge
    $statusClasses = [
        'verified' => 'bg-blue-100 text-blue-800',
        'in_progress' => 'bg-orange-100 text-orange-800',
        'completed' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
    ];
    $statusText = str_replace('_', ' ', $report->status); // Mengubah 'in_progress' menjadi 'in progress'
@endphp

<a href="{{ route('public.laporan.show', $report) }}"
    class="block bg-white rounded-lg shadow-sm border border-gray-200 p-4 transition-all duration-300 hover:shadow-lg hover:border-blue-400 hover:-translate-y-1">
    <div class="flex flex-col sm:flex-row items-start">
        @php
            // PERUBAHAN 2: Ambil gambar dari dokumentasi AWAL, bukan dari tindak lanjut
            $media = $report->images->first();
        @endphp

        @if ($media)
            {{-- Tampilan media (gambar atau video thumbnail) --}}
            <div
                class="w-full sm:w-32 h-32 bg-gray-100 rounded-md mb-4 sm:mb-0 sm:mr-4 flex items-center justify-center text-gray-400 flex-shrink-0 overflow-hidden">
                <img src="{{ $media->file_type == 'video' ? Storage::url($media->thumbnail_path) : Storage::url($media->file_path) }}"
                    alt="Dokumentasi Laporan" class="w-full h-full object-cover">
            </div>
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
                <h3 class="font-semibold text-gray-800 break-words pr-2">{{ Str::limit($report->title, 50) }}</h3>

                {{-- PERUBAHAN 3: Status badge & teks menjadi dinamis --}}
                <span
                    class="text-xs font-medium px-2.5 py-0.5 rounded-full {{ $statusClasses[$report->status] ?? 'bg-gray-100 text-gray-800' }} ml-2 flex-shrink-0 capitalize">
                    {{ $statusText }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">
                <span class="font-semibold">Lokasi:</span>
                {{ Str::limit($report->location_address, 40) }}
            </p>

            {{-- PERUBAHAN 4: Label tanggal dan nilainya menjadi dinamis --}}
            <p class="text-xs text-gray-400 mt-2">
                @if ($report->status == 'completed' && $report->completed_at)
                    Selesai pada: {{ \Carbon\Carbon::parse($report->completed_at)->isoFormat('D MMM YYYY, HH:mm') }}
                @else
                    Diperbarui: {{ $report->updated_at->diffForHumans() }}
                @endif
            </p>
        </div>
    </div>
</a>
