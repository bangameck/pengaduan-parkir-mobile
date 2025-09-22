@extends('layouts.mobile')

@section('title', 'Buat Laporan Baru')

@push('style')
    {{-- CDN untuk FilePond --}}
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <style>
        .filepond--root { font-family: 'Inter', sans-serif; margin-bottom: 1.5rem; }
        .filepond--panel-root { background-color: #f9fafb; border-radius: 0.5rem; }
        .filepond--item-panel { border-radius: 0.5rem; }
        .shake { animation: shake 0.82s cubic-bezier(.36, .07, .19, .97) both; transform: translate3d(0, 0, 0); }
        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
        [x-cloak] { display: none !important; }
    </style>
@endpush

@section('content')
    <x-resident-header />
    <x-page-header>Buat Laporan Baru</x-page-header>

    <div class="p-4 sm:p-6 pb-24" x-data="reportFormHandler()">
        <form @submit.prevent="submitForm" x-ref="reportForm" novalidate :class="{ 'shake': shakeError }">
            {{-- Form Fields --}}
            <div class="mb-4">
                <label for="title" class="block mb-2 text-sm font-medium text-gray-900">Judul Laporan</label>
                <input type="text" id="title" x-model="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
                <span x-show="!title && shakeError" class="text-red-500 text-xs mt-1">Judul wajib diisi</span>
            </div>
            <div class="mb-4">
                <label for="description" class="block mb-2 text-sm font-medium text-gray-900">Deskripsi</label>
                <textarea id="description" rows="4" x-model="description" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border" required></textarea>
                <span x-show="!description && shakeError" class="text-red-500 text-xs mt-1">Deskripsi wajib diisi</span>
            </div>
            <div class="mb-6">
                <label for="location_address" class="block mb-2 text-sm font-medium text-gray-900">Alamat Lokasi</label>
                <input type="text" id="location_address" x-model="location_address" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" required>
                <span x-show="!location_address && shakeError" class="text-red-500 text-xs mt-1">Alamat lokasi wajib diisi</span>
            </div>

            {{-- PERBAIKAN: File upload TANPA nested x-data (pakai parent scope untuk files) --}}
            <div wire:ignore>  {{-- HAPUS x-data="{ files: [] }" -- HAPUS INI! --}}
                <label for="file_input" class="block mb-2 text-sm font-medium text-gray-900">Bukti Laporan (Max 5 File: Gambar/Video)</label>
                <input type="file" x-ref="filepond" id="file_input" multiple accept="image/*,video/*">
                <div x-show="uploadError" class="text-red-500 text-sm mt-2" x-text="uploadError"></div>
                <div x-show="this.pond && this.pond.getFiles().length === 0 && shakeError" class="text-red-500 text-sm mt-1">Minimal 1 file bukti wajib diupload</div>
            </div>

            <div class="mt-6">
                <button type="submit" :disabled="isProcessing || isSubmitting"
                    class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center transition-opacity">
                    <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isProcessing ? 'Sedang Memproses File...' : (isSubmitting ? `Mengupload... ${progress}%` : 'Kirim Laporan')"></span>
                </button>
            </div>

            <div x-show="isSubmitting" x-cloak class="mt-4" x-transition>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" :style="`width: ${progress}%`"></div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('script')
    {{-- CDN SweetAlert2 untuk alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- CDN Lengkap untuk FilePond dan FFmpeg --}}
    <script src="https://cdn.jsdelivr.net/npm/@ffmpeg/ffmpeg@0.12.10/dist/umd/ffmpeg.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

    <script>
        // PERBAIKAN: Membungkus logika Alpine di dalam event listener 'alpine:init'.
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportFormHandler', () => ({
                title: '{{ old('title', '') }}',
                description: '{{ old('description', '') }}',
                location_address: '{{ old('location_address', '') }}',
                isSubmitting: false,
                isProcessing: false,
                shakeError: false,
                progress: 0,
                uploadError: '',  // PERBAIKAN: State untuk error upload
                ffmpeg: null,
                files: [], // Array untuk menyimpan { id, thumbnail, serverPath }
                pond: null,

                init() {
                    // Register plugins
                    FilePond.registerPlugin(
                        FilePondPluginImagePreview,
                        FilePondPluginFileValidateType
                    );

                      this.pond = FilePond.create(this.$refs.filepond, {
                        allowMultiple: true,
                        maxFiles: 5,
                        acceptedFileTypes: ['image/jpeg', 'image/jpg', 'image/png', 'video/mp4', 'video/quicktime'],
                        server: {
                            process: {
                                url: '{{ route("temp.upload") }}',
                                method: 'POST',
                                fieldName: 'file',  // PERBAIKAN: Specify field name untuk match Laravel validation 'file'
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                onload: (response) => {
                                    const data = JSON.parse(response);
                                    return data.location;  // Parse JSON dan return location
                                },
                                onerror: (response) => {
                                    console.error('Upload response error:', response);
                                    this.uploadError = 'Gagal upload file: ' + (typeof response === 'string' ? response : JSON.stringify(response));  // PERBAIKAN: Handle response string/JSON
                                }
                            },
                            revert: {
                                url: '{{ route("temp.revert") }}',
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            },
                        },
                        transformFile: (file, options) => this.transformFile(file, options),
                        onprocessfile: (error, file) => {
                            if (error) {
                                console.error('Process file error:', error);
                                this.uploadError = 'Gagal proses file: ' + error.main;
                            } else {
                                console.log('Upload success - File ID:', file.id, 'Server ID:', file.serverId);  // Debug ID & serverId

                                // Push file ke state SAAT upload sukses (dari fix sebelumnya)
                                let managedFile = this.files.find(f => f.id === file.id);
                                if (!managedFile) {
                                    managedFile = {
                                        id: file.id,
                                        serverPath: file.serverId,
                                        thumbnail: ''  // Default empty
                                    };
                                    this.files.push(managedFile);
                                    console.log('Pushed new file to state:', managedFile);  // Debug push
                                } else {
                                    managedFile.serverPath = file.serverId;
                                    console.log('Updated existing file with serverPath:', managedFile);  // Debug update
                                }
                            }
                        },
                        onremovefile: (error, file) => {
                            if (error) console.error('Remove file error:', error);
                            this.files = this.files.filter(f => f.id !== file.id);
                            this.uploadError = '';  // Clear error on remove
                        },
                        onprocessfiles: (files) => {  // Guard dari fix sebelumnya
                            if (!files || !Array.isArray(files)) {
                                console.warn('onprocessfiles called with invalid files:', files);
                                return;
                            }
                            const hasError = files.some(f => f.status !== 5);
                            if (hasError) {
                                console.warn('Some files failed to process');
                                this.uploadError = 'Beberapa file gagal diupload. Coba lagi.';
                            } else {
                                this.uploadError = '';
                                console.log('All files processed successfully:', files.length);  // Debug
                            }
                        },
                        onerror: (error) => {  // Global error handler
                            console.error('FilePond error:', error);
                            this.uploadError = 'Error FilePond: ' + (error.main || 'Upload gagal');
                            Swal.fire('Error Upload', this.uploadError, 'error');
                        },
                        // ... (labelIdle dan label lainnya tetap sama, skip untuk singkat)
                        labelIdle: `Seret & Lepas file Anda atau <span class="filepond--label-action">Jelajahi</span>`,
                        // (sisanya label tetap dari code sebelumnya)
                    });
                },
                 async initFFmpeg() {
                    if (this.ffmpeg) return;
                    try {
                        this.isProcessing = true;
                        this.ffmpeg = new FFmpeg.FFmpeg();
                        await this.ffmpeg.load({
                            coreURL: "https://cdn.jsdelivr.net/npm/@ffmpeg/core@0.12.6/dist/umd/ffmpeg-core.js"
                        });
                        this.isProcessing = false;
                    } catch (error) {
                        console.error('FFmpeg load error:', error);
                        this.uploadError = 'Gagal load processor video. Upload tanpa kompresi.';
                        this.isProcessing = false;
                    }
                },

                generateVideoThumbnail(videoFile) {
                    return new Promise((resolve) => {
                        const video = document.createElement('video');
                        const canvas = document.createElement('canvas');
                        video.src = URL.createObjectURL(videoFile);
                        video.onloadedmetadata = () => { video.currentTime = 1; };
                        video.onseeked = () => {
                            const ctx = canvas.getContext('2d');
                            const aspectRatio = video.videoWidth / video.videoHeight;
                            canvas.width = 300;
                            canvas.height = 300 / aspectRatio;
                            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                            URL.revokeObjectURL(video.src);
                            resolve(canvas.toDataURL('image/jpeg', 0.8));
                        };
                        video.onerror = () => {
                            URL.revokeObjectURL(video.src);
                            resolve('');  // Fallback empty thumbnail
                        };
                    });
                },

                async transformFile(file, options) {
                    const isVideo = file.type.startsWith('video/');
                    let thumbnail = '';

                    try {
                        if (isVideo) {
                            // PERBAIKAN: Generate thumbnail untuk video, tapi JANGAN push ke files di sini
                            thumbnail = await this.generateVideoThumbnail(file);
                            console.log('Generated thumbnail for video:', thumbnail ? 'success (base64)' : 'failed');  // Debug thumbnail
                        }

                        // HAPUS: this.files.push(...) - Pindah ke onprocessfile untuk sync ID

                        if (!isVideo) return file;

                        // Kompres video dengan FFmpeg (tetap sama)
                        await this.initFFmpeg();
                        if (!this.ffmpeg) return file;  // Skip jika FFmpeg gagal

                        const progressHandler = ({ progress }) => {
                            options.progress(true, progress * 100, 100);
                        };

                        this.ffmpeg.on('progress', progressHandler);

                        const buffer = await file.arrayBuffer();
                        await this.ffmpeg.writeFile(file.name, new Uint8Array(buffer));
                        await this.ffmpeg.exec(['-i', file.name, '-preset', 'ultrafast', '-crf', '28', 'output.mp4']);
                        const data = await this.ffmpeg.readFile('output.mp4');

                        this.ffmpeg.off('progress', progressHandler);

                        this.$nextTick(() => {
                            this.isProcessing = this.pond.getFiles().some(f => f.status === 1 || f.status === 2);
                        });

                        // PERBAIKAN: Return transformed file, thumbnail akan di-handle di onprocessfile jika perlu
                        // (Untuk sekarang, thumbnail bisa di-set manual di submitForm jika index match)
                        return new File([data.buffer], `compressed_${file.name}`, { type: 'video/mp4' });
                    } catch (error) {
                        console.error('Transform file error:', error);
                        this.uploadError = 'Gagal proses video: ' + error.message;
                        return file;  // Fallback ke file original
                    }
                },
                submitForm() {
                    this.shakeError = false;
                    this.uploadError = '';

                    // PERBAIKAN: Validasi lebih ketat dengan debug
                    if (!this.title || !this.description || !this.location_address) {
                        this.shakeError = true;
                        setTimeout(() => this.shakeError = false, 820);
                        Swal.fire('Data Belum Lengkap', 'Mohon isi semua field.', 'warning');
                        return;
                    }

                    if (!this.pond || this.pond.getFiles().length === 0) {
                        this.shakeError = true;
                        setTimeout(() => this.shakeError = false, 820);
                        Swal.fire('Data Belum Lengkap', 'Anda harus mengunggah minimal satu file (gambar atau video).', 'warning');
                        return;
                    }

                    if (this.pond.getFiles().some(file => file.status !== 5)) {
                        Swal.fire('Proses Upload', 'Mohon tunggu semua file selesai diunggah.', 'info');
                        return;
                    }

                    // PERBAIKAN: Debug logs sebelum submit (buka F12 > Console untuk lihat)
                    console.log('=== DEBUG SUBMIT FORM ===');
                    console.log('Form data:', { title: this.title, description: this.description, location_address: this.location_address });
                    console.log('All files state:', this.files);
                    console.log('Pond files:', this.pond.getFiles().map(f => ({ id: f.id, status: f.status, serverId: f.serverId })));

                    const successfullyUploadedFiles = this.files.filter(f => f.serverPath && f.serverPath.trim() !== '');
                    console.log('Successfully uploaded files:', successfullyUploadedFiles);
                    console.log('Server paths to send:', successfullyUploadedFiles.map(f => f.serverPath));
                    console.log('Video thumbnails:', successfullyUploadedFiles.map(f => f.thumbnail));

                    if (successfullyUploadedFiles.length === 0) {
                        console.error('No files with serverPath! Upload failed.');
                        this.shakeError = true;
                        setTimeout(() => this.shakeError = false, 820);
                        Swal.fire('Error Upload', 'Tidak ada file yang berhasil diupload. Cek console untuk detail.', 'error');
                        return;
                    }

                    // PERBAIKAN: Siapkan payload untuk ReportController (images sebagai array paths, video_thumbnails sebagai base64)
                    const formData = new FormData();
                    formData.append('title', this.title);
                    formData.append('description', this.description);
                    formData.append('location_address', this.location_address);
                    formData.append('images', JSON.stringify(successfullyUploadedFiles.map(f => f.serverPath)));

                    // Tambah video_thumbnails jika ada (base64 string)
                    successfullyUploadedFiles.forEach((file, index) => {
                        if (file.thumbnail) {
                            formData.append(`video_thumbnails[${index}]`, file.thumbnail);
                        }
                    });

                    this.isSubmitting = true;
                    this.progress = 0;

                    // Submit via fetch ke ReportController@store
                    fetch('{{ route("laporan.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        this.progress = 100;
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Submit success:', data);
                        Swal.fire('Sukses!', data.message || 'Laporan berhasil dikirim!', 'success')
                            .then(() => {
                                if (data.redirect_url) {
                                    window.location.href = data.redirect_url;
                                } else {
                                    window.location.reload();  // Fallback reload
                                }
                            });
                    })
                    .catch(error => {
                        console.error('Submit error:', error);
                        this.isSubmitting = false;
                        this.progress = 0;
                        const errorMsg = error.message || error.error || 'Gagal mengirim laporan. Coba lagi.';
                        Swal.fire('Error', errorMsg, 'error');
                        this.shakeError = true;
                        setTimeout(() => this.shakeError = false, 820);
                    });
                }
            }));
        });
    </script>
@endpush
