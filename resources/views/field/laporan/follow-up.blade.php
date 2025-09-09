<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tindak Lanjut Laporan #{{ $report->report_code }}
            </h2>
            <a href="{{ route('petugas.tugas.index') }}"
                class="mt-2 sm:mt-0 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Daftar Tugas
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- KOLOM KIRI: DETAIL LAPORAN AWAL (SEBAGAI REFERENSI) --}}
                <div class="lg:col-span-5">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-24">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-bold text-dishub-blue-800 border-b pb-3 mb-4">Detail Laporan Awal
                            </h3>
                            <div class="space-y-3 text-sm">
                                <p><strong class="w-24 inline-block text-gray-500">Kode:</strong>
                                    #{{ $report->report_code }}</p>
                                <p><strong class="w-24 inline-block text-gray-500">Judul:</strong> {{ $report->title }}
                                </p>
                                <p><strong class="w-24 inline-block text-gray-500">Pelapor:</strong>
                                    {{ $report->resident->name }}</p>
                                <p><strong class="w-24 inline-block text-gray-500">Lokasi:</strong>
                                    {{ $report->location_address }}</p>
                                <div class="pt-2">
                                    <strong class="w-32 block text-gray-500 mb-1">Deskripsi:</strong>
                                    <p
                                        class="whitespace-pre-wrap bg-gray-50 p-3 rounded-md border max-h-48 overflow-y-auto">
                                        {{ $report->description }}</p>
                                </div>
                                <div class="pt-2">
                                    <strong class="w-32 block text-gray-500 mb-2">Dokumentasi Awal:</strong>
                                    <div class="grid grid-cols-3 md:grid-cols-4 gap-3">
                                        @forelse($report->images as $media)
                                            <a href="{{ Storage::url($media->file_path) }}" data-lity
                                                class="relative block aspect-square group rounded-md overflow-hidden border">
                                                <img src="{{ $media->file_type == 'video' ? ($media->thumbnail_path ? Storage::url($media->thumbnail_path) : asset('images/video-placeholder.png')) : Storage::url($media->file_path) }}"
                                                    alt="Media Laporan Awal"
                                                    class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                                @if ($media->file_type == 'video')
                                                    <div
                                                        class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30">
                                                        <svg class="w-8 h-8 text-white" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </a>
                                        @empty
                                            <p class="text-sm text-gray-500 col-span-full">Tidak ada dokumentasi.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: FORM TINDAK LANJUT --}}
                <div class="lg:col-span-7">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-bold text-dishub-blue-800 border-b pb-3 mb-6">Formulir Tindak Lanjut
                            </h3>
                            @if (session('error'))
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                                    <p class="font-bold">Gagal</p>
                                    <p>{{ session('error') }}</p>
                                </div>
                            @endif
                            <form action="{{ route('petugas.tugas.storeFollowUp', $report) }}" method="POST"
                                enctype="multipart/form-data" class="space-y-8" x-data="{ isSubmitting: false, geoStatus: 'Mencari lokasi Anda...' }"
                                @submit="isSubmitting = true">
                                @csrf

                                {{-- Input Petugas (Multi-select dengan Tom Select) --}}
                                <div>
                                    <label for="tom-select-officers"
                                        class="block mb-2 text-sm font-medium text-gray-700">Petugas yang
                                        Terlibat</label>
                                    <select name="officer_ids[]" id="tom-select-officers" multiple required
                                        placeholder="Pilih satu atau lebih petugas...">
                                        @foreach ($fieldOfficers as $officer)
                                            <option value="{{ $officer->id }}"
                                                {{ Auth::id() == $officer->id ? 'selected' : '' }}>
                                                {{ $officer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('officer_ids')" />
                                </div>

                                {{-- Floating Label untuk Catatan --}}
                                <div class="relative">
                                    <textarea name="notes" id="notes" rows="4" required
                                        class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                        placeholder=" ">{{ old('notes') }}</textarea>
                                    <label for="notes"
                                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">
                                        Catatan Tindak Lanjut (Wajib diisi)
                                    </label>
                                    <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                                </div>

                                {{-- Upload via FilePond --}}
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-700">Foto/Video Bukti (Wajib
                                        diisi, maks 5 file)</label>
                                    <input type="file" name="proof_media[]" id="proof_media" class="filepond"
                                        multiple required>
                                    <x-input-error class="mt-2" :messages="$errors->get('proof_media')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('proof_media.*')" />
                                </div>

                                {{-- Koordinat GPS --}}
                                <div x-init="if (navigator.geolocation) {
                                    navigator.geolocation.getCurrentPosition(
                                        (position) => {
                                            $refs.latitude.value = position.coords.latitude;
                                            $refs.longitude.value = position.coords.longitude;
                                            geoStatus = `Lat: ${position.coords.latitude.toFixed(6)}, Lon: ${position.coords.longitude.toFixed(6)}`;
                                        },
                                        () => { geoStatus = 'Gagal mendapatkan lokasi. Izinkan akses.'; }
                                    );
                                } else { geoStatus = 'Geolocation tidak didukung browser ini.'; }">
                                    <label class="block text-sm font-medium text-gray-700">Koordinat GPS
                                        (Otomatis)</label>
                                    <input type="text" x-model="geoStatus" readonly
                                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-sm">
                                    <input type="hidden" name="latitude" x-ref="latitude"
                                        value="{{ old('latitude') }}">
                                    <input type="hidden" name="longitude" x-ref="longitude"
                                        value="{{ old('longitude') }}">
                                    <x-input-error class="mt-2" :messages="$errors->get('latitude')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('longitude')" />
                                </div>

                                {{-- Tombol Submit --}}
                                <div class="flex items-center justify-end pt-4 border-t">
                                    <button type="submit" x-bind:disabled="isSubmitting"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-dishub-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dishub-blue-700 active:bg-dishub-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                                        <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span x-show="!isSubmitting">Simpan Tindak Lanjut</span>
                                        <span x-show="isSubmitting">Menyimpan...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script untuk Tom-select & Filepond --}}
    @push('scripts')
        {{-- CDN untuk Tom Select --}}
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Inisialisasi Tom Select untuk memilih petugas
                const officerSelect = document.getElementById('tom-select-officers');
                if (officerSelect) {
                    new TomSelect(officerSelect, {
                        plugins: ['remove_button'],
                    });
                }

                // Inisialisasi Filepond untuk upload bukti
                const inputElement = document.querySelector('input[id="proof_media"]');
                if (inputElement) {
                    FilePond.create(inputElement, {
                        storeAsFile: true,
                        acceptedFileTypes: ['image/png', 'image/jpeg', 'video/mp4'],
                        labelFileTypeNotAllowed: 'Jenis file tidak valid',
                        fileValidateTypeLabelExpectedTypes: 'Hanya .JPG, .PNG, atau .MP4',
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
