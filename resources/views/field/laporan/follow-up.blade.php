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

                {{-- KOLOM KIRI: DETAIL LAPORAN AWAL --}}
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

                                <div class="pt-2" x-data="{ expanded: false }">
                                    <strong class="w-32 block text-gray-500 mb-1">Deskripsi:</strong>
                                    <div x-show="!expanded" class="relative max-h-24 overflow-hidden">
                                        <p class="text-gray-800">{{ $report->description }}</p>
                                        <div
                                            class="absolute bottom-0 left-0 w-full h-8 bg-gradient-to-t from-white to-transparent">
                                        </div>
                                    </div>
                                    <p x-show="expanded" class="text-gray-800 whitespace-pre-wrap" x-collapse>
                                        {{ $report->description }}</p>
                                    <button @click="expanded = !expanded"
                                        class="text-blue-600 text-xs font-semibold mt-1"
                                        x-text="expanded ? 'Sembunyikan' : 'Lihat Selengkapnya'"></button>
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

                            <div x-data="followUpFormHandler()">
                                <form @submit.prevent="submitForm" x-ref="followUpForm" class="space-y-8">
                                    @csrf
                                    <div wire:ignore>
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
                                    </div>

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

                                    <div wire:ignore>
                                        <label for="file_input"
                                            class="block mb-2 text-sm font-medium text-gray-700">Foto/Video Bukti (Wajib
                                            diisi, maks 5 file, video maks 20MB)</label>
                                        <input type="file" id="file_input" x-ref="filepond" multiple>
                                    </div>

                                    <div x-init="getGeolocation()">
                                        <label class="block text-sm font-medium text-gray-700">Lokasi Tindak Lanjut
                                            (Otomatis)</label>
                                        <div id="map" class="mt-2 h-64 w-full rounded-lg bg-gray-200"></div>
                                        <div class="mt-2">
                                            <input type="text" x-model="geoStatus" readonly
                                                class="block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-sm">
                                            <p class="mt-1 text-xs text-gray-500" x-text="locationDescription"></p>
                                        </div>
                                        <input type="hidden" name="latitude" x-ref="latitude">
                                        <input type="hidden" name="longitude" x-ref="longitude">
                                        <input type="hidden" name="location_description" x-model="locationDescription">
                                    </div>

                                    <div class="pt-4 border-t">
                                        <div class="w-full" x-show="isSubmitting" x-transition>
                                            <div class="flex justify-between items-center text-sm text-gray-600 mb-2">
                                                <span class="truncate pr-2"
                                                    x-text="`Memproses ${currentFileName}...`"></span>
                                                <span class="flex-shrink-0" x-text="progress + '%'"></span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                                                    :style="`width: ${progress}%`"></div>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-end" x-show="!isSubmitting">
                                            <button type="submit" :disabled="isSubmitting"
                                                class="inline-flex items-center justify-center px-4 py-2 bg-dishub-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dishub-blue-700 active:bg-dishub-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                                                Simpan Tindak Lanjut
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('followUpFormHandler', () => ({
                    isSubmitting: false,
                    progress: 0,
                    currentFileName: '',
                    pond: null,
                    tomSelect: null,
                    geoStatus: 'Mencari lokasi Anda...',
                    locationDescription: 'Memuat nama lokasi...',
                    map: null,
                    marker: null,

                    init() {
                        this.tomSelect = new TomSelect('#tom-select-officers', {
                            plugins: ['remove_button']
                        });

                        FilePond.registerPlugin(
                            FilePondPluginImagePreview,
                            FilePondPluginFileValidateType,
                            FilePondPluginFileValidateSize
                        );

                        this.pond = FilePond.create(this.$refs.filepond, {
                            allowMultiple: true,
                            maxFiles: 5,
                            maxFileSize: '20MB',
                            acceptedFileTypes: ['image/jpeg', 'image/png', 'video/mp4'],
                            labelMaxFileSizeExceeded: 'File terlalu besar',
                            labelMaxFileSize: 'Ukuran maksimal file adalah {filesize}',
                            labelFileTypeNotAllowed: 'Jenis file tidak valid',
                            fileValidateTypeLabelExpectedTypes: 'Hanya .JPG, .PNG, atau .MP4',
                            server: {
                                process: {
                                    url: '{{ route('temp.upload') }}',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    onload: (r) => JSON.parse(r).location
                                },
                                revert: {
                                    url: '{{ route('temp.revert') }}',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                }
                            },
                            onwarning: (error) => {
                                if (error.body === 'Max files exceeded') {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Batas Maksimal Terlampaui',
                                        text: `Anda hanya dapat mengunggah maksimal 5 file.`,
                                    });
                                }
                            }
                        });
                    },

                    getGeolocation() {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(
                                // Success Callback (Tidak berubah)
                                (position) => {
                                    const lat = position.coords.latitude;
                                    const lon = position.coords.longitude;
                                    this.$refs.latitude.value = lat;
                                    this.$refs.longitude.value = lon;
                                    this.geoStatus = `Lat: ${lat.toFixed(6)}, Lon: ${lon.toFixed(6)}`;

                                    if (!this.map) {
                                        this.map = L.map('map').setView([lat, lon], 16);
                                        L.tileLayer(
                                            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                            }).addTo(this.map);
                                        this.marker = L.marker([lat, lon]).addTo(this.map);
                                    } else {
                                        this.map.setView([lat, lon], 16);
                                        this.marker.setLatLng([lat, lon]);
                                    }

                                    fetch(
                                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`
                                        )
                                        .then(response => response.json())
                                        .then(data => {
                                            this.locationDescription = data.display_name ||
                                                'Tidak dapat menemukan nama lokasi.';
                                        })
                                        .catch(() => {
                                            this.locationDescription = 'Gagal memuat nama lokasi.';
                                        });
                                },
                                // Error Callback (DITINGKATKAN)
                                (error) => {
                                    this.geoStatus = 'Gagal mendapatkan lokasi.';
                                    this.locationDescription =
                                        'Pastikan GPS aktif dan izin lokasi diberikan.';
                                    console.error(
                                        `Geolocation Error Code: ${error.code}, Message: ${error.message}`
                                    );

                                    let title = 'Lokasi Gagal Didapat';
                                    let text =
                                        'Tidak dapat mengambil lokasi Anda. Pastikan GPS aktif dan koneksi internet stabil.';

                                    // Deteksi jika pengguna menolak izin secara manual
                                    if (error.code === error.PERMISSION_DENIED) {
                                        title = 'Izin Lokasi Diblokir';
                                        text =
                                            'Anda telah memblokir izin lokasi. Mohon aktifkan melalui pengaturan browser Anda untuk melanjutkan.';
                                    }
                                    // Deteksi jika ada masalah overlay atau sinyal GPS lemah
                                    else if (error.code === error.POSITION_UNAVAILABLE || error.code ===
                                        error.TIMEOUT) {
                                        title = 'Gagal Mengakses GPS';
                                        text =
                                            'Pastikan GPS Anda aktif. Jika muncul peringatan "overlay" atau "balon", harap tutup aplikasi lain seperti chat Messenger, lalu coba lagi.';
                                    }

                                    Swal.fire({
                                        icon: 'warning',
                                        title: title,
                                        text: text,
                                        confirmButtonText: 'Saya Mengerti'
                                    });
                                }
                            );
                        } else {
                            this.geoStatus = 'Geolocation tidak didukung browser ini.';
                            this.locationDescription = '';
                        }
                    },

                    async submitForm() {
                        const form = this.$refs.followUpForm;
                        const notes = form.querySelector('[name="notes"]').value;
                        const officers = this.tomSelect.getValue();
                        const hasFiles = this.pond.getFiles().filter(f => f.status === 5).length > 0;

                        if (notes.trim() === '' || officers.length === 0 || !hasFiles) {
                            return Swal.fire('Data Belum Lengkap',
                                'Mohon isi catatan, pilih petugas, dan unggah minimal satu file bukti.',
                                'warning');
                        }

                        this.isSubmitting = true;
                        this.progress = 0;

                        const successfullyUploadedFiles = this.pond.getFiles().filter(f => f.status ===
                            5);
                        const fileNames = successfullyUploadedFiles.map(f => f.file.name);
                        this.currentFileName = fileNames.length > 0 ? fileNames[0] : 'menyimpan data';

                        const progressInterval = setInterval(() => {
                            if (this.progress < 95) this.progress += 5;
                            const fileIndex = Math.floor((this.progress / 100) * fileNames
                                .length);
                            if (fileNames[fileIndex]) this.currentFileName = fileNames[
                                fileIndex];
                        }, 200);

                        try {
                            const formData = new FormData(form);
                            formData.delete('proof_media[]');

                            const filePaths = successfullyUploadedFiles.map(f => f.serverId);
                            formData.append('proof_media', JSON.stringify(filePaths));

                            const response = await fetch(
                                '{{ route('petugas.tugas.storeFollowUp', $report) }}', {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                });

                            clearInterval(progressInterval);
                            this.progress = 100;

                            const data = await response.json();
                            if (!response.ok) throw new Error(data.message || 'Terjadi kesalahan.');

                            await Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message
                            });
                            window.location.href = data.redirect_url;

                        } catch (error) {
                            clearInterval(progressInterval);
                            this.isSubmitting = false;
                            this.progress = 0;
                            Swal.fire('Error', error.message, 'error');
                        }
                    }
                }));
            });

            // ## PERUBAHAN DI SINI: Tambahkan script untuk Swal blocking overlay ##
            document.addEventListener('DOMContentLoaded', function() {
                @if ($report->status !== 'verified')
                    Swal.fire({
                        title: 'Akses Ditolak!',
                        html: `Status Laporan <strong class="capitalize">{{ str_replace('_', ' ', $report->status) }}</strong> tidak bisa ditindak lanjuti atau sudah selesai.`,
                        icon: 'warning',
                        backdrop: `
                            rgba(0,0,0,0.4)
                            url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3e%3cpath d='M0 0h100v100H0z' fill='%23000' fill-opacity='.1'/%3e%3c/svg%3e")
                            left top
                            repeat
                        `,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        confirmButtonText: 'Kembali ke Daftar Tugas',
                        confirmButtonColor: '#1d4ed8', // blue-700
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route('petugas.tugas.index') }}';
                        }
                    });
                @endif
            });
        </script>
    @endpush
</x-app-layout>
