@extends('layouts.mobile')

@section('title', 'Buat Laporan Baru')

@push('style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <style>
        .filepond--root {
            font-family: 'Inter', sans-serif;
            margin-bottom: 1.5rem;
        }

        .filepond--panel-root {
            background-color: #f9fafb;
            border-radius: 0.5rem;
        }

        /* Tombol akan menjadi abu-abu HANYA saat proses submit (isSubmitting = true) */
        button:disabled {
            background-color: #9ca3af;
            /* gray-400 */
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content')
    <x-resident-header />
    <x-page-header>Buat Laporan Baru</x-page-header>

    <div class="p-4 sm:p-6 pb-24" x-data="reportFormHandler()">
        <form @submit.prevent="submitForm">
            {{-- Bagian input form tidak berubah --}}
            <div class="space-y-8">
                <div class="relative">
                    <input type="text" id="title" x-model="title" @input="updateFormState"
                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required />
                    <label for="title"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-gray-50 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Judul
                        Laporan</label>
                </div>
                <div class="relative">
                    <textarea id="description" rows="2" x-model="description" @input="updateFormState"
                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required></textarea>
                    <label for="description"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-gray-50 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Deskripsi
                        Laporan</label>
                </div>
                <div class="relative">
                    <input type="text" id="location_address" x-model="location_address" @input="updateFormState"
                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required />
                    <label for="location_address"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-gray-50 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Alamat
                        Lokasi</label>
                </div>
            </div>

            <div wire:ignore class="mt-8">
                <label for="file_input" class="block mb-2 text-sm font-medium text-gray-900">Bukti Laporan (Max 5 File,
                    Video Max 20MB)</label>
                <input type="file" id="file_input" x-ref="filepond" multiple>

                <div class="mt-4" x-show="isSubmitting" x-transition>
                    <div class="flex justify-between items-center text-sm text-gray-600 mb-2">
                        <span class="truncate pr-2" x-text="`Memproses ${currentFileName}...`"></span>
                        <span class="flex-shrink-0" x-text="progress + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 h-2 rounded-full transition-all duration-300 ease-out"
                            :style="'width: ' + progress + '%'"></div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                {{-- ## PERUBAHAN DI SINI: Logika disabled dan class disederhanakan ## --}}
                <button type="submit" :disabled="isSubmitting"
                    class="text-white font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center flex items-center justify-center transition-colors duration-300 bg-blue-700 hover:bg-blue-800">

                    <span x-show="isSubmitting" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Tunggu proses sedang berlangsung!
                    </span>
                    <span x-show="!isSubmitting">Kirim Laporan</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('script')
    {{-- CDN tidak berubah --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportFormHandler', () => ({
                title: '',
                description: '',
                location_address: '',
                isSubmitting: false,
                progress: 0,
                pond: null,
                currentFileName: '',
                isFormComplete: false,

                init() {
                    FilePond.registerPlugin(
                        FilePondPluginImagePreview, FilePondPluginFileValidateType,
                        FilePondPluginFileValidateSize
                    );
                    this.pond = FilePond.create(this.$refs.filepond, {
                        allowMultiple: true,
                        maxFiles: 5,
                        maxFileSize: '20MB',
                        acceptedFileTypes: ['image/jpeg', 'image/png', 'video/mp4',
                            'video/quicktime'
                        ],
                        server: {
                            process: {
                                url: '{{ route('temp.upload') }}',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                onload: (response) => JSON.parse(response).location,
                            },
                            revert: {
                                url: '{{ route('temp.revert') }}',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                            },
                        },
                        labelIdle: `Seret & Lepas file atau <span class="filepond--label-action">Jelajahi</span>`,
                        onupdatefiles: () => {
                            this.updateFormState();
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

                updateFormState() {
                    const allFieldsFilled = this.title.trim() !== '' && this.description.trim() !==
                        '' && this.location_address.trim() !== '';
                    const hasFiles = this.pond && this.pond.getFiles().filter(file => file.status ===
                        5 && file.serverId).length > 0;
                    this.isFormComplete = allFieldsFilled && hasFiles;
                },

                async submitForm() {
                    // ## PERUBAHAN DI SINI: Validasi Swal dijalankan pertama kali ##
                    if (!this.isFormComplete) {
                        return Swal.fire({
                            icon: 'warning',
                            title: 'Data Belum Lengkap',
                            text: 'Mohon isi semua field dan unggah minimal satu file bukti sebelum mengirim laporan.',
                            confirmButtonColor: '#3085d6'
                        });
                    }

                    this.isSubmitting = true;
                    this.progress = 0;

                    const successfullyUploadedFiles = this.pond.getFiles().filter(file => file
                        .status === 5 && file.serverId);
                    const fileNames = successfullyUploadedFiles.map(f => f.file.name);
                    this.currentFileName = fileNames[0] || 'data laporan';

                    const progressInterval = setInterval(() => {
                        if (this.progress < 95) this.progress += 5;
                        const fileIndex = Math.floor((this.progress / 100) * fileNames
                            .length);
                        if (fileNames[fileIndex]) this.currentFileName = fileNames[
                            fileIndex];
                    }, 200);

                    try {
                        const formData = new FormData();
                        formData.append('title', this.title);
                        formData.append('description', this.description);
                        formData.append('location_address', this.location_address);

                        const imagePaths = successfullyUploadedFiles.map(pondFile => pondFile
                            .serverId);
                        formData.append('images', JSON.stringify(imagePaths));

                        const response = await fetch('{{ route('laporan.store') }}', {
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
                        if (!response.ok) throw new Error(data.message ||
                            'Terjadi kesalahan server.');

                        await Swal.fire('Berhasil!', data.message, 'success');
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
    </script>
@endpush
