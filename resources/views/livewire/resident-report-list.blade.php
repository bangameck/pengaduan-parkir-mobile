<div> {{-- <== ATURAN EMAS: Semua konten wajib ada di dalam satu div ini --}}

    {{-- Kotak Pencarian --}}
    <div class="mb-6">
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </span>
            <input wire:model.live.debounce.350ms="search" type="text"
                placeholder="Cari berdasarkan judul atau kode..."
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150">
        </div>
    </div>

    {{-- Kotak Info Debug (bisa kamu hapus jika sudah tidak perlu) --}}
    {{-- <div class="p-2 mb-4 text-xs bg-yellow-100 border border-yellow-300 rounded-md">
        <p><strong>Info Debug:</strong> Laporan Ditampilkan: <strong>{{ $reports->count() }}</strong> | Ada Halaman
            Berikutnya? <strong>{{ $hasMorePages ? 'Ya' : 'Tidak' }}</strong></p>
    </div> --}}

    {{-- Grid untuk Kartu Laporan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        @forelse ($reports as $report)
            {{-- Menggunakan cetakan kartu khusus resident --}}
            @include('resident.partials.report-card', ['report' => $report])
        @empty
            <div class="col-span-1 sm:col-span-2 p-6 text-center bg-white rounded-lg shadow-sm border border-gray-200">
                <p class="text-gray-500">
                    @if (empty($search))
                        Anda belum pernah membuat laporan.
                    @else
                        Tidak ada laporan yang cocok dengan kata kunci <span
                            class="font-semibold">"{{ $search }}"</span>.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    {{-- Tombol "Load More" --}}
    @if ($hasMorePages)
        <div class="mt-8 text-center">
            <button wire:click="loadMore" wire:loading.attr="disabled"
                class="bg-white text-gray-700 font-semibold py-2 px-6 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 disabled:opacity-50">

                <div wire:loading wire:target="loadMore" class="flex items-center justify-center">
                    <svg class="animate-spin h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="ml-2">Memuat...</span>
                </div>

                <span wire:loading.remove wire:target="loadMore">
                    Muat Lebih Banyak
                </span>
            </button>
        </div>
    @endif

</div> {{-- <== Tag penutup dari div pembungkus utama --}}
