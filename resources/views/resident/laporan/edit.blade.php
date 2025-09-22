@extends('layouts.mobile')

@section('title', 'Edit Laporan')

@push('style')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <style>
        .filepond--root { font-family: 'Inter', sans-serif; margin-bottom: 1.5rem; }
        .filepond--panel-root { background-color: #f9fafb; border-radius: 0.5rem; }
        button:disabled {
            background-color: #9ca3af;
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content')
    <x-resident-header />
    <x-page-header>Edit Laporan #{{ $report->report_code }}</x-page-header>

    <div class="p-4 sm:p-6 pb-24" x-data="editReportFormHandler({{ $report->images->count() }})">
        <form @submit.prevent="submitForm">
            <div class="space-y-4">
                {{-- Judul Laporan --}}
                <div class="relative">
                    <input type="text" id="title" x-model="title"
                        class="block rounded-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required />
                    <label for="title"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-blue-600 peer-focus:scale-75 peer-focus:-translate-y-4 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:top-1/2">
                        Judul Laporan
                    </label>
                </div>

                {{-- Deskripsi Laporan --}}
                <div class="relative">
                    <textarea id="description" rows="2" x-model="description"
                        class="block rounded-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required></textarea>
                    <label for="description"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-blue-600 peer-focus:scale-75 peer-focus:-translate-y-4 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:top-1/2">
                        Deskripsi Laporan
                    </label>
                </div>

                {{-- Alamat Lokasi --}}
                <div class="relative">
                    <input type="text" id="location_address" x-model="location_address"
                        class="block rounded-lg px-2.5 pb-2.5 pt-5 w-full text-sm text-gray-900 bg-gray-50 border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required />
                    <label for="location_address"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-blue-600 peer-focus:scale-75 peer-focus:-translate-y-4 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:top-1/2">
                        Alamat Lokasi
                    </label>
                </div>
            </div>

            <div class="mt-8">
                <label class="block mb-2 text-sm font-medium text-gray-900">Dokumentasi Saat Ini</label>
                @if ($report->images->isNotEmpty())
                    <div class="grid grid-cols-3 gap-2">
                        @foreach ($report->images as $media)
                            <div class="relative" x-show="!deletedImages.includes({{ $media->id }})" x-transition:leave.duration.300ms>
                                <img src="{{ $media->thumbnail_path ? Storage::url($media->thumbnail_path) : Storage::url($media->file_path) }}"
                                    class="w-full aspect-square object-cover rounded-lg">
                                <button type="button" @click="deletedImages.push({{ $media->id }})"
                                    class="absolute top-1 right-1 bg-red-600/80 text-white rounded-full p-1 w-6 h-6 flex items-center justify-center shadow-lg hover:bg-red-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">Tidak ada dokumentasi.</p>
                @endif
            </div>

            <div wire:ignore class="mt-8">
                <label for="file_input" class="block mb-2 text-sm font-medium text-gray-900">Tambah Bukti Baru</label>
                <input type="file" id="file_input" x-ref="filepond" multiple>
            </div>

            <div class="mt-4" x-show="isSubmitting" x-transition>
                <div class="flex justify-between items-center text-sm text-gray-600 mb-2">
                    <span class="truncate pr-2" x-text="`Memproses ${currentFileName}...`"></span>
                    <span class="flex-shrink-0" x-text="progress + '%'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 h-2 rounded-full transition-all duration-300 ease-out" :style="'width: ' + progress + '%'"></div>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit"
                        :disabled="isSubmitting"
                        class="text-white font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center flex items-center justify-center transition-colors duration-300 bg-blue-700 hover:bg-blue-800">
                    <span x-show="isSubmitting" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Tunggu proses sedang berlangsung...
                    </span>
                    <span x-show="!isSubmitting">Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('editReportFormHandler', (initialImageCount) => ({
                title: @json(old('title', $report->title)),
                description: @json(old('description', $report->description)),
                location_address: @json(old('location_address', $report->location_address)),
                isSubmitting: false,
                progress: 0,
                pond: null,
                currentFileName: '',
                deletedImages: [],
                init() {
                    const maxNewFiles = 5 - initialImageCount;
                    FilePond.registerPlugin(
                        FilePondPluginImagePreview, FilePondPluginFileValidateType, FilePondPluginFileValidateSize
                    );
                    this.pond = FilePond.create(this.$refs.filepond, {
                        allowMultiple: true,
                        maxFiles: Math.max(0, maxNewFiles),
                        maxFileSize: '20MB',
                        acceptedFileTypes: ['image/jpeg', 'image/png', 'video/mp4', 'video/quicktime'],
                        server: {
                            process: { url: '{{ route('temp.upload') }}', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, onload: (response) => JSON.parse(response).location },
                            revert: { url: '{{ route('temp.revert') }}', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }
                        },
                        labelIdle: `Seret & Lepas file atau <span class="filepond--label-action">Jelajahi</span>`,
                        onwarning: (error) => {
                            if (error.body === 'Max files exceeded') {
                                Swal.fire({ icon: 'warning', title: 'Batas Maksimal Terlampaui', text: `Anda hanya dapat menambah ${this.pond.options.maxFiles} file lagi.` });
                            }
                        }
                    });
                    this.$watch('deletedImages', (newValue) => {
                        const remainingOldImages = initialImageCount - newValue.length;
                        const newMaxFiles = 5 - remainingOldImages;
                        this.pond.setOptions({ maxFiles: Math.max(0, newMaxFiles) });
                    });
                },
                async submitForm() {
                    const isFormValid = this.title.trim() !== '' && this.description.trim() !== '' && this.location_address.trim() !== '';
                    if (!isFormValid) {
                        return Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Mohon isi semua field yang wajib diisi.' });
                    }
                    this.isSubmitting = true;
                    this.progress = 0;
                    const successfullyUploadedFiles = this.pond.getFiles().filter(file => file.status === 5 && file.serverId);
                    const fileNames = successfullyUploadedFiles.map(f => f.file.name);
                    this.currentFileName = fileNames.length > 0 ? fileNames[0] : 'menyimpan data';
                    const progressInterval = setInterval(() => { /* ... progress logic ... */ }, 200);
                    try {
                        const formData = new FormData();
                        formData.append('title', this.title);
                        formData.append('description', this.description);
                        formData.append('location_address', this.location_address);
                        formData.append('_method', 'PATCH');
                        this.deletedImages.forEach(id => formData.append('delete_images[]', id));
                        const newImagePaths = successfullyUploadedFiles.map(pondFile => pondFile.serverId);
                        // Kirim 'images' sebagai JSON string kosong jika tidak ada file baru
                        formData.append('images', JSON.stringify(newImagePaths.length > 0 ? newImagePaths : []));

                        const response = await fetch('{{ route('laporan.update', $report) }}', {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        });

                        clearInterval(progressInterval);
                        this.progress = 100;

                        const data = await response.json();
                        if (!response.ok) {
                            if (response.status === 422) { throw new Error(Object.values(data.errors).flat().join('\n')); }
                            throw new Error(data.message || 'Terjadi kesalahan server.');
                        }

                        // ## PERUBAHAN DI SINI: Tampilkan Swal sebelum redirect ##
                        await Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message, // Pesan dari backend: "Laporan berhasil diperbarui!"
                            confirmButtonColor: '#3085d6'
                        });

                        // Redirect SETELAH pengguna menekan "OK"
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
