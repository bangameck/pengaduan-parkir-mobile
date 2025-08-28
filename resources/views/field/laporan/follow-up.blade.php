<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tindak Lanjut Laporan #{{ $report->report_code }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold border-b pb-2 mb-4">Detail Laporan Awal</h3>
                    <p><strong class="w-32 inline-block">Judul:</strong> {{ $report->title }}</p>
                    <p><strong class="w-32 inline-block">Pelapor:</strong> {{ $report->resident->name }}</p>
                    <p><strong class="w-32 inline-block">Lokasi:</strong> {{ $report->location_address }}</p>
                    <p class="mt-2"><strong class="w-32 block">Deskripsi:</strong></p>
                    <p class="whitespace-pre-wrap bg-gray-50 p-3 rounded-md">{{ $report->description }}</p>
                    <div>
                        <p class="mt-2"><strong class="w-32 block mb-2">Dokumentasi Awal:</strong></p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @forelse($report->images as $media)
                                <a href="{{ Storage::url($media->file_path) }}" data-lity {{-- <-- CUKUP TAMBAHKAN INI, DAN HAPUS target="_blank" --}}
                                    class="relative block aspect-square group">

                                    <img src="{{ $media->file_type == 'video' ? ($media->thumbnail_path ? Storage::url($media->thumbnail_path) : 'https://via.placeholder.com/300') : Storage::url($media->file_path) }}"
                                        alt="Media Laporan Awal"
                                        class="w-full h-full object-cover rounded-md transition-transform group-hover:scale-105">

                                    @if ($media->file_type == 'video')
                                        <div
                                            class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30 rounded-md transition-opacity opacity-70 group-hover:opacity-100">
                                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </a>
                            @empty
                                <p class="text-sm text-gray-500 col-span-full">Tidak ada dokumentasi yang dilampirkan.
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold border-b pb-2 mb-6">Formulir Tindak Lanjut</h3>
                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                            <p class="font-bold">Gagal</p>
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif
                    <form id="follow-up-form" action="{{ route('admin.tugas.storeFollowUp', $report) }}" method="POST"
                        enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        {{-- Floating Label untuk Catatan --}}
                        <div class="relative">
                            <textarea name="notes" id="notes" rows="4" required
                                class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                placeholder=" ">{{ old('notes') }}</textarea>
                            <label for="notes"
                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">
                                Catatan Tindak Lanjut
                            </label>
                            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                        </div>

                        {{-- Upload Foto/Video Bukti (FilePond) --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">Foto/Video Bukti (Maks 5
                                file)</label>
                            <input type="file" name="proof_media[]" id="proof_media" class="filepond" multiple
                                required>
                            <x-input-error class="mt-2" :messages="$errors->get('proof_media')" />
                        </div>

                        {{-- Koordinat GPS --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Koordinat GPS (Otomatis)</label>
                            <input type="text" id="geo-location-display" readonly
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100"
                                placeholder="Mencari lokasi Anda...">
                            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                            <x-input-error class="mt-2" :messages="$errors->get('latitude')" />
                        </div>

                        {{-- Tombol Submit --}}
                        <div class="flex items-center gap-4">
                            <x-primary-button type="submit" id="submit-btn" class="flex items-center justify-center">
                                <svg id="loading-spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span id="button-text">Simpan Tindak Lanjut</span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Geolocation Otomatis
                const latInput = document.getElementById('latitude');
                const lonInput = document.getElementById('longitude');
                const geoLocationDisplay = document.getElementById('geo-location-display');
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            latInput.value = position.coords.latitude;
                            lonInput.value = position.coords.longitude;
                            geoLocationDisplay.value =
                                `Lat: ${position.coords.latitude.toFixed(6)}, Lon: ${position.coords.longitude.toFixed(6)}`;
                        },
                        () => {
                            geoLocationDisplay.value = 'Gagal mendapatkan lokasi. Izinkan akses.';
                        }
                    );
                } else {
                    geoLocationDisplay.value = 'Geolocation tidak didukung browser ini.';
                }

                // Inisialisasi Filepond
                const inputElement = document.querySelector('input[id="proof_media"]');
                FilePond.create(inputElement, {
                    storeAsFile: true,
                    acceptedFileTypes: ['image/png', 'image/jpeg', 'video/mp4'],
                    labelFileTypeNotAllowed: 'Jenis file tidak valid',
                    fileValidateTypeLabelExpectedTypes: 'Hanya .JPG, .PNG, atau .MP4',
                });

                // Animasi Tombol
                const form = document.getElementById('follow-up-form');
                const submitButton = document.getElementById('submit-btn');
                const spinner = document.getElementById('loading-spinner');
                const buttonText = document.getElementById('button-text');
                form.addEventListener('submit', function() {
                    submitButton.disabled = true;
                    spinner.classList.remove('hidden');
                    buttonText.textContent = 'Menyimpan...';
                });
            });
        </script>
    @endpush
</x-app-layout>
