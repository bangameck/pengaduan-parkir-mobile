@extends('layouts.mobile')

@section('title', 'Buat Laporan Baru')

@push('style')
    {{-- CDN untuk FilePond --}}
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

        .filepond--item-panel {
            border-radius: 0.5rem;
        }

        .shake {
            animation: shake 0.82s cubic-bezier(.36, .07, .19, .97) both;
        }

        @keyframes shake {

            10%,
            90% {
                transform: translate3d(-1px, 0, 0);
            }

            20%,
            80% {
                transform: translate3d(2px, 0, 0);
            }

            30%,
            50%,
            70% {
                transform: translate3d(-4px, 0, 0);
            }

            40%,
            60% {
                transform: translate3d(4px, 0, 0);
            }
        }

        /* TAMBAHAN: Progress Bar & Spinner Smooth */
        .animate-spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endpush

@section('content')
    <x-resident-header />
    <x-page-header>Buat Laporan Baru</x-page-header>

    <div class="p-4 sm:p-6 pb-24" x-data="reportFormHandler()">
        <form @submit.prevent="submitForm" x-ref="reportForm" novalidate :class="{ 'shake': shakeError }">
            <div class="space-y-8">
                <div class="relative">
                    <input type="text" id="title" x-model="title"
                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required />
                    <label for="title"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-gray-50 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">
                        Judul Laporan
                    </label>
                </div>
                <div class="relative">
                    <textarea id="description" rows="2" x-model="description"
                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required></textarea>
                    <label for="description"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-gray-50 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">
                        Deskripsi Laporan
                    </label>
                </div>
                <div class="relative">
                    <input type="text" id="location_address" x-model="location_address"
                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " required />
                    <label for="location_address"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-gray-50 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">
                        Alamat Lokasi
                    </label>
                </div>
            </div>

            <div wire:ignore class="mt-8">
                <label class="block mb-2 text-sm font-medium text-gray-900">Bukti Laporan (Max 5 File)</label>
                <input type="file" x-ref="filepond" multiple>
                {{-- TAMBAHAN: Progress Bar untuk Upload File & Submit --}}
                <div class="mt-4" x-show="isProcessing || isSubmitting" x-transition>
                    <div class="flex justify-between items-center text-sm text-gray-600 mb-2">
                        <span x-text="isProcessing ? 'Memproses file...' : 'Mengirim laporan...'"></span>
                        <span x-text="progress + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 h-2 rounded-full transition-all duration-300 ease-out"
                            :style="'width: ' + progress + '%'"></div>
                    </div>
                    {{-- Spinner Icon (Opsional, show saat processing) --}}
                    <div x-show="isProcessing" class="flex items-center mt-1 text-blue-500 text-xs">
                        <svg class="animate-spin -ml-1 mr-1 h-3 w-3 text-blue-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>Kompresi dengan FFmpeg sedang berjalan...</span>
                    </div>
                </div>
                <p x-show="uploadError" class="text-red-500 text-sm mt-1" x-text="uploadError"></p>
            </div>

            <div class="mt-6">
                <button type="submit" :disabled="isProcessing || isSubmitting"
                    class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center transition-opacity">
                    <span
                        x-text="isProcessing ? 'Sedang Memproses File...' : (isSubmitting ? 'Mengirim Laporan...' : 'Kirim Laporan')"></span>
                </button>
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
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportFormHandler', () => ({
                title: '{{ old('title', '') }}',
                description: '{{ old('description', '') }}',
                location_address: '{{ old('location_address', '') }}',
                isSubmitting: false,
                isProcessing: false,
                shakeError: false,
                uploadError: '',
                progress: 0,
                ffmpeg: null,
                files: [],
                pond: null,

                init() {
                    FilePond.registerPlugin(
                        FilePondPluginImagePreview,
                        FilePondPluginFileValidateType
                    );
                    this.pond = FilePond.create(this.$refs.filepond, {
                        allowMultiple: true,
                        maxFiles: 5,
                        acceptedFileTypes: ['image/jpeg', 'image/jpg', 'image/png', 'video/mp4',
                            'video/quicktime'
                        ],
                        server: {
                            process: {
                                url: '{{ route('temp.upload') }}',
                                method: 'POST',
                                fieldName: 'file',
                                chunked: false,
                                chunksUpload: false,
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                },
                                onload: (response) => {
                                    try {
                                        const data = JSON.parse(response);
                                        console.log('**UPLOAD SUKSES**:', data);
                                        this.progress = 100;
                                        setTimeout(() => {
                                            this.progress = 0;
                                        }, 1000);
                                        return data.location || response;
                                    } catch (e) {
                                        console.warn('onload not JSON:', response);
                                        return response;
                                    }
                                },
                                onerror: (response) => {
                                    console.error('**UPLOAD ERROR**:', response);
                                    this.progress = 0;
                                    this.uploadError = 'Gagal upload: ' + (
                                        typeof response === 'string' ? response : JSON
                                        .stringify(response));
                                }
                            },
                            revert: {
                                url: '{{ route('temp.revert') }}',
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            },
                        },
                        transformFile: (file, options) => this.transformFile(file, options),
                        onprocessprogress: (fileItem, progress) => {
                            this.progress = Math.round(progress * 100);
                            console.log('**PROGRESS UPLOAD**:', this.progress + '%');
                        },
                        onaddfile: (error, file) => {
                            if (error) {
                                console.error('**ADD FILE ERROR**:', error);
                                return;
                            }
                            const fileType = file.file ? file.file.type : 'unknown';
                            const type = fileType.startsWith('image/') ? 'image' : (fileType
                                .startsWith('video/') ? 'video' : 'unknown');
                            this.files.push({
                                id: file.id,
                                thumbnail: null,
                                serverPath: null,
                                type: type,
                                originalFile: file
                                    .file // Simpan file asli untuk fallback
                            });
                            console.log('**FILE ADDED**:', {
                                id: file.id,
                                type: type,
                                mime: fileType,
                                size: file.file.size
                            });
                        },
                        onprocessfile: (error, file) => {
                            if (error) {
                                console.error('**PROCESS FILE ERROR**:', error);
                                this.progress = 0;
                                this.uploadError = 'Gagal proses file: ' + error.main;
                                return;
                            }
                            const managedFile = this.files.find(f => f.id === file.id);
                            if (managedFile) {
                                managedFile.serverPath = file.serverId;
                                console.log('**FILE PROCESSED**:', {
                                    id: managedFile.id,
                                    serverPath: managedFile.serverPath,
                                    type: managedFile.type,
                                    thumbnail: managedFile.thumbnail ? 'OK' : 'NULL'
                                });
                            }
                            this.progress = 0;
                        },
                        onremovefile: (error, file) => {
                            if (error) console.error('**REMOVE FILE ERROR**:', error);
                            this.files = this.files.filter(f => f.id !== file.id);
                            console.log('**FILE REMOVED**, remaining:', this.files.length);
                        },
                        onerror: (error) => {
                            console.error('**FILEPOND ERROR**:', error);
                            this.progress = 0;
                            this.uploadError = 'Error FilePond: ' + (error.main ||
                                'Upload gagal');
                            Swal.fire('Error Upload', this.uploadError, 'error');
                        },
                        labelIdle: `Seret & Lepas file atau <span class="filepond--label-action">Jelajahi</span>`
                    });
                },

                // Fallback untuk kompresi video yang gagal
                async fallbackToOriginalVideo(fileId) {
                    const managedFile = this.files.find(f => f.id === fileId);
                    if (!managedFile || !managedFile.originalFile) {
                        console.error('**FALLBACK ERROR**: No original file found for', fileId);
                        return null;
                    }

                    console.log('**FALLBACK**: Using original video file for', fileId);

                    // Upload file asli tanpa kompresi
                    try {
                        const formData = new FormData();
                        formData.append('filepond', managedFile.originalFile);

                        const response = await fetch('{{ route('temp.upload') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        if (response.ok) {
                            const result = await response.text();
                            console.log('**FALLBACK UPLOAD SUCCESS**:', result);
                            return result;
                        } else {
                            console.error('**FALLBACK UPLOAD FAILED**:', response.status);
                            return null;
                        }
                    } catch (error) {
                        console.error('**FALLBACK UPLOAD ERROR**:', error);
                        return null;
                    }
                },

                // Generate thumbnail dengan fallback
                async generateVideoThumbnailWithFallback(videoFile, fileId) {
                    try {
                        const thumbnail = await this.generateVideoThumbnail(videoFile);
                        if (thumbnail) {
                            return thumbnail;
                        }

                        // Fallback: buat thumbnail dari frame pertama
                        console.log('**THUMBNAIL FALLBACK**: Trying first frame for', fileId);
                        return await this.generateFirstFrameThumbnail(videoFile);
                    } catch (error) {
                        console.error('**THUMBNAIL GENERATION ERROR**:', error);
                        return null;
                    }
                },

                // Generate thumbnail dari frame pertama (fallback)
                async generateFirstFrameThumbnail(videoFile) {
                    return new Promise((resolve) => {
                        const video = document.createElement('video');
                        const canvas = document.createElement('canvas');
                        const url = URL.createObjectURL(videoFile);

                        video.src = url;
                        video.muted = true;
                        video.currentTime = 0.1; // Coba frame pertama

                        video.onloadeddata = () => {
                            if (video.videoWidth > 0 && video.videoHeight > 0) {
                                canvas.width = video.videoWidth;
                                canvas.height = video.videoHeight;
                                const ctx = canvas.getContext('2d');
                                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                                const thumbnail = canvas.toDataURL('image/jpeg', 0.7);
                                URL.revokeObjectURL(url);
                                resolve(thumbnail);
                            } else {
                                URL.revokeObjectURL(url);
                                resolve(null);
                            }
                        };

                        video.onerror = () => {
                            URL.revokeObjectURL(url);
                            resolve(null);
                        };

                        // Timeout setelah 5 detik
                        setTimeout(() => {
                            URL.revokeObjectURL(url);
                            resolve(null);
                        }, 5000);
                    });
                },

                // Transform file dengan error handling yang lebih baik
                async transformFile(file, options) {
                    console.log('**TRANSFORM START**:', {
                        name: file.name,
                        type: file.type,
                        size: file.size
                    });

                    options.progress(true, 0, 100);

                    const isVideo = file.type.startsWith('video/');
                    const isImage = file.type.startsWith('image/');
                    let thumbnail = null;
                    let processedFile = file;

                    try {
                        if (isVideo) {
                            // Generate thumbnail terlebih dahulu
                            this.progress = 10;
                            options.progress(true, 10, 100);

                            thumbnail = await this.generateVideoThumbnailWithFallback(file, file
                                .id);

                            // Set thumbnail
                            const managedFile = this.files.find(f => f.id === file.id);
                            if (managedFile) {
                                managedFile.thumbnail = thumbnail;
                                console.log('**THUMBNAIL SET**:', thumbnail ? 'Success' : 'Failed');
                            }

                            this.progress = 20;
                            options.progress(true, 20, 100);

                            // Coba kompresi video dengan FFmpeg
                            try {
                                const ffmpeg = await this.initFFmpeg();
                                if (ffmpeg) {
                                    this.isProcessing = true;
                                    this.progress = 30;
                                    options.progress(true, 30, 100);

                                    console.log('**FFMPEG EXEC**: Starting compress for', file
                                        .name);

                                    const progressHandler = ({
                                        progress
                                    }) => {
                                        const uiProgress = Math.round(progress * 70) + 30;
                                        options.progress(true, uiProgress / 100, 1);
                                        this.progress = uiProgress;
                                    };

                                    ffmpeg.on('progress', progressHandler);

                                    const buffer = await file.arrayBuffer();
                                    await ffmpeg.writeFile(file.name, new Uint8Array(buffer));

                                    await ffmpeg.exec([
                                        '-i', file.name,
                                        '-preset', 'ultrafast',
                                        '-crf', '28',
                                        '-movflags', '+faststart',
                                        '-y',
                                        'output.mp4'
                                    ]);

                                    const data = await ffmpeg.readFile('output.mp4');
                                    ffmpeg.off('progress', progressHandler);

                                    processedFile = new File([data.buffer],
                                        `compressed_${file.name}`, {
                                            type: 'video/mp4'
                                        });

                                    console.log('**VIDEO COMPRESSED SUCCESS**:', {
                                        originalSize: file.size,
                                        compressedSize: processedFile.size,
                                        reduction: ((1 - processedFile.size / file.size) *
                                            100).toFixed(1) + '%'
                                    });

                                    // Cleanup
                                    try {
                                        await ffmpeg.deleteFile(file.name);
                                        await ffmpeg.deleteFile('output.mp4');
                                    } catch (e) {
                                        console.warn('FFmpeg cleanup error:', e.message);
                                    }
                                }
                            } catch (ffmpegError) {
                                console.warn('**FFMPEG COMPRESSION FAILED**:', ffmpegError.message);
                                console.log('**FALLBACK**: Using original video file');

                                // Tidak perlu melakukan apa-else, processedFile sudah di-set ke file asli
                            }

                        } else if (isImage) {
                            // Kompres image
                            this.progress = 20;
                            options.progress(true, 20, 100);
                            processedFile = await this.compressImage(file);
                            this.progress = 80;
                            options.progress(true, 80, 100);
                        }

                        this.progress = 100;
                        options.progress(true, 100, 100);
                        this.isProcessing = false;

                        return processedFile;

                    } catch (error) {
                        console.error('**TRANSFORM ERROR**:', error);
                        this.progress = 0;
                        options.progress(true, 0, 100);
                        this.isProcessing = false;

                        // Untuk video, kembalikan file asli jika kompresi gagal
                        if (isVideo) {
                            console.log('**TRANSFORM FAILED**: Returning original video file');
                            return file;
                        }

                        Swal.fire('Error Proses', 'Gagal memproses file: ' + error.message,
                            'warning');
                        return file;
                    }
                },

                // Submit form dengan handling untuk video tanpa thumbnail
                async submitForm() {
                    this.shakeError = false;
                    this.uploadError = '';

                    // Validasi form
                    if (!this.title || !this.description || !this.location_address) {
                        this.shakeError = true;
                        setTimeout(() => this.shakeError = false, 820);
                        Swal.fire('Data Belum Lengkap', 'Mohon isi semua field yang wajib diisi.',
                            'warning');
                        return;
                    }

                    const uploadedFiles = this.pond.getFiles();
                    if (uploadedFiles.length === 0) {
                        this.shakeError = true;
                        setTimeout(() => this.shakeError = false, 820);
                        Swal.fire('Data Belum Lengkap', 'Mohon upload setidaknya satu bukti.',
                            'warning');
                        return;
                    }

                    const processingFiles = uploadedFiles.filter(file => file.status !== 5);
                    if (processingFiles.length > 0) {
                        Swal.fire('Proses Upload', 'Mohon tunggu semua file selesai diunggah.',
                            'info');
                        return;
                    }

                    const successfullyUploadedFiles = this.files.filter(f => f.serverPath && f
                        .serverPath.trim() !== '');
                    if (successfullyUploadedFiles.length === 0) {
                        Swal.fire('Error', 'Tidak ada file yang berhasil diupload.', 'error');
                        return;
                    }

                    console.log('**SUBMIT START**:', successfullyUploadedFiles.length,
                        'files ready');

                    this.isSubmitting = true;
                    this.progress = 0;

                    try {
                        const formData = new FormData();
                        formData.append('title', this.title);
                        formData.append('description', this.description);
                        formData.append('location_address', this.location_address);

                        // Tambahkan paths gambar
                        successfullyUploadedFiles.forEach((file, index) => {
                            formData.append('images[]', file.serverPath);
                        });

                        // Tambahkan thumbnails untuk video (jika ada)
                        successfullyUploadedFiles.forEach((file, index) => {
                            if (file.type === 'video' && file.thumbnail) {
                                formData.append('video_thumbnails[]', file.thumbnail);
                            } else if (file.type === 'video') {
                                // Untuk video tanpa thumbnail, kirim string kosong
                                formData.append('video_thumbnails[]', '');
                            }
                        });

                        const response = await fetch('{{ route('laporan.store') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        this.progress = 100;

                        if (!response.ok) {
                            const errorData = await response.json();
                            throw {
                                response: {
                                    status: response.status,
                                    data: errorData
                                }
                            };
                        }

                        const data = await response.json();
                        console.log('**SUBMIT SUCCESS**:', data.message);

                        Swal.fire('Berhasil!', data.message, 'success')
                            .then(() => {
                                window.location.href = data.redirect_url ||
                                    '{{ route('dashboard') }}';
                            });

                    } catch (error) {
                        console.error('**SUBMIT ERROR**:', error);
                        this.isSubmitting = false;

                        let errorMessage = 'Terjadi kesalahan saat mengirim laporan.';
                        if (error.response && error.response.data) {
                            if (error.response.status === 422) {
                                const errors = error.response.data.errors || {};
                                errorMessage = Object.values(errors).flat().join('\n');
                            } else if (error.response.data.message) {
                                errorMessage = error.response.data.message;
                            }
                        }

                        Swal.fire('Error', errorMessage, 'error');
                        this.shakeError = true;
                        setTimeout(() => this.shakeError = false, 820);
                    }
                },
                // PERBAIKAN: FFmpeg dengan retry (3x) + alternative CDN + mobile skip
                async initFFmpeg(retryCount = 0) {
                    if (this.ffmpeg) return this.ffmpeg;

                    // TAMBAHAN: Skip FFmpeg di mobile (memory low)
                    if (navigator.userAgent.includes('Mobile') || navigator.userAgent.includes(
                            'Android')) {
                        console.warn(
                            '**FFMPEG MOBILE SKIP**: Browser mobile detected, kompresi video skip untuk stability.'
                        );
                        this.uploadError = 'Kompresi video skip di mobile (upload original).';
                        Swal.fire('Info', this.uploadError, 'info');
                        return null;
                    }

                    if (typeof FFmpeg === 'undefined') {
                        console.error('**FFMPEG CDN FAIL**: Global not defined!');
                        this.uploadError = 'FFmpeg tidak tersedia (kompresi skip).';
                        Swal.fire('Warning',
                            'Kompresi video skip - FFmpeg gagal load. Upload original?',
                            'warning');
                        return null;
                    }

                    try {
                        this.isProcessing = true;
                        this.progress = 10;
                        console.log('**FFMPEG INIT**: Loading core (retry ' + retryCount + ')...');
                        this.ffmpeg = new FFmpeg.FFmpeg();

                        // TAMBAHAN: Alternative CDN jika jsdelivr fail
                        let coreURL =
                            'https://cdn.jsdelivr.net/npm/@ffmpeg/core@0.12.6/dist/umd/ffmpeg-core.js';
                        if (retryCount > 0) {
                            coreURL =
                                'https://unpkg.com/@ffmpeg/core@0.12.6/dist/umd/ffmpeg-core.js';
                            console.log('**FFMPEG RETRY**: Switch to unpkg CDN');
                        }

                        await this.ffmpeg.load({
                            coreURL: coreURL
                        });
                        this.progress = 20;
                        this.isProcessing = false;
                        console.log('**FFMPEG SUKSES**: Loaded OK!');
                        return this.ffmpeg;
                    } catch (error) {
                        console.error('**FFMPEG LOAD FAIL** (retry ' + retryCount + '):', error
                            .message);
                        this.progress = 0;
                        if (retryCount < 3) {
                            console.log('Retry FFmpeg load...');
                            await new Promise(resolve => setTimeout(resolve, 2000));
                            return this.initFFmpeg(retryCount + 1);
                        }
                        this.uploadError = 'Gagal load FFmpeg setelah 3 retry: ' + error.message +
                            ' (kompresi skip).';
                        Swal.fire('Warning', this.uploadError, 'warning');
                        this.isProcessing = false;
                        this.ffmpeg = null;
                        return null;
                    }
                },

                // PERBAIKAN: Thumbnail dengan timeout 10s + preload='metadata' + fallback codec check
                generateVideoThumbnail(videoFile) {
                    return new Promise((resolve) => {
                        const video = document.createElement('video');
                        const canvas = document.createElement('canvas');
                        const url = URL.createObjectURL(videoFile);
                        video.src = url;
                        video.muted = true;
                        video.preload = 'metadata'; // TAMBAHAN: Faster metadata load
                        video.load();

                        let seekTimeout = setTimeout(() => {
                            console.warn(
                                '**THUMBNAIL TIMEOUT**: Seek gagal setelah 10s, fallback null'
                            );
                            URL.revokeObjectURL(url);
                            resolve(null);
                        }, 10000); // Extend ke 10s untuk video besar

                        video.onloadedmetadata = () => {
                            console.log('**THUMBNAIL**: Metadata loaded, duration:', video
                                .duration, 'type:', videoFile.type);
                            // TAMBAHAN: Fallback seek jika duration 0 atau invalid
                            const seekTime = (video.duration > 0 && !isNaN(video
                                .duration)) ? Math.min(1, video.duration * 0.1) : 0.5;
                            video.currentTime = seekTime;
                        };

                        video.onseeked = () => {
                            clearTimeout(seekTimeout);
                            console.log('**THUMBNAIL**: Seeked to', video.currentTime);
                            if (video.videoWidth === 0 || video.videoHeight === 0) {
                                console.warn(
                                    '**THUMBNAIL**: Invalid video dimensions, fallback null'
                                );
                                URL.revokeObjectURL(url);
                                resolve(null);
                                return;
                            }
                            canvas.width = 320;
                            canvas.height = 180;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                            URL.revokeObjectURL(url);
                            const thumbnail = canvas.toDataURL('image/jpeg', 0.7);
                            console.log('**THUMBNAIL SUKSES**: Generated, size ~' + (
                                thumbnail.length / 1024).toFixed(1) + 'KB');
                            resolve(thumbnail);
                        };

                        video.onerror = (e) => {
                            clearTimeout(seekTimeout);
                            console.error('**THUMBNAIL ERROR**:', e, 'Type:', videoFile
                                .type);
                            URL.revokeObjectURL(url);
                            resolve(null);
                        };

                        video.onloadeddata = () => console.log('**THUMBNAIL**: Data loaded');
                    });
                },

                // Compress image (sama, tambah log)
                async compressImage(file) {
                    return new Promise((resolve) => {
                        const img = new Image();
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        const url = URL.createObjectURL(file);
                        img.src = url;

                        img.onload = () => {
                            console.log('**IMAGE COMPRESS**: Loaded, original size',
                                file.size);
                            let {
                                width,
                                height
                            } = img;
                            if (width > 1920 || height > 1080) {
                                const ratio = Math.min(1920 / width, 1080 / height);
                                width *= ratio;
                                height *= ratio;
                            }
                            canvas.width = width;
                            canvas.height = height;
                            ctx.drawImage(img, 0, 0, width, height);
                            canvas.toBlob((blob) => {
                                const compressedFile = new File([blob],
                                    `compressed_${file.name}`, {
                                        type: 'image/jpeg'
                                    });
                                console.log('**IMAGE COMPRESSED**:', {
                                    originalSize: file.size,
                                    compressedSize: compressedFile.size,
                                    reduction: ((1 - compressedFile
                                            .size / file.size) *
                                        100).toFixed(1) + '%'
                                });
                                URL.revokeObjectURL(url);
                                resolve(compressedFile);
                            }, 'image/jpeg', 0.8);
                        };

                        img.onerror = () => {
                            console.error('**IMAGE LOAD ERROR**: Fallback original');
                            URL.revokeObjectURL(url);
                            resolve(file);
                        };
                    });
                },
                // PERBAIKAN: TransformFile dengan logs detail + force progress
                async transformFile(file, options) {
                    console.log('**TRANSFORM START**:', {
                        name: file.name,
                        type: file.type,
                        size: file.size
                    });
                    options.progress(true, 0, 100); // Init 0%

                    const isVideo = file.type.startsWith('video/');
                    const isImage = file.type.startsWith('image/');
                    let thumbnail = null;
                    let processedFile = file;

                    try {
                        if (isVideo) {
                            // Thumbnail
                            this.progress = 10;
                            options.progress(true, 10, 100);
                            thumbnail = await this.generateVideoThumbnail(file);
                            this.progress = 20;
                            options.progress(true, 20, 100);

                            // Set thumbnail IMMEDIATE (sebelum kompres)
                            const managedFile = this.files.find(f => f.id === file.id);
                            if (managedFile) {
                                managedFile.thumbnail = thumbnail;
                                console.log('**THUMBNAIL SET**: To state', managedFile.id,
                                    thumbnail ? 'OK' : 'NULL');
                            }

                            // FFmpeg kompres
                            const ffmpeg = await this.initFFmpeg();
                            if (!ffmpeg) {
                                console.warn(
                                    '**VIDEO SKIP COMPRESS**: FFmpeg unavailable, upload original'
                                );
                                options.progress(true, 100, 100);
                                Swal.fire('Info', 'Kompresi video skip - upload file asli.',
                                    'info');
                                return processedFile;
                            }

                            this.isProcessing = true;
                            this.progress = 30;
                            options.progress(true, 30, 100);
                            console.log('**FFMPEG EXEC**: Starting compress for', file.name);

                            // Progress + log handler
                            const progressHandler = ({
                                progress,
                                time
                            }) => {
                                const uiProgress = Math.round(progress * 70) +
                                    30; // 30-100% FFmpeg
                                options.progress(true, uiProgress / 100, 1);
                                this.progress = uiProgress;
                                console.log('**FFMPEG PROGRESS**:', (progress * 100).toFixed(
                                    1) + '% (time:', time, 's)');
                            };
                            const logHandler = ({
                                message
                            }) => {
                                console.log('**FFMPEG LOG**:', message);
                            };
                            ffmpeg.on('progress', progressHandler);
                            ffmpeg.on('log', logHandler);

                            // Write input file
                            const buffer = await file.arrayBuffer();
                            await ffmpeg.writeFile(file.name, new Uint8Array(buffer));
                            console.log('**FFMPEG**: Input written,', file.name, 'size:', buffer
                                .byteLength);

                            // Exec kompres (ultrafast untuk speed, CRF 28 untuk size)
                            await ffmpeg.exec([
                                '-i', file.name,
                                '-preset', 'ultrafast',
                                '-crf', '28',
                                '-movflags', '+faststart',
                                '-y', // Overwrite output
                                'output.mp4'
                            ]);
                            console.log('**FFMPEG EXEC**: Command done');

                            // Read output
                            const data = await ffmpeg.readFile('output.mp4');
                            ffmpeg.off('progress', progressHandler);
                            ffmpeg.off('log', logHandler);

                            this.progress = 100;
                            options.progress(true, 100, 100);
                            this.isProcessing = false;

                            processedFile = new File([data.buffer], `compressed_${file.name}`, {
                                type: 'video/mp4'
                            });
                            console.log('**VIDEO COMPRESSED SUKSES**:', {
                                originalSize: file.size,
                                compressedSize: processedFile.size,
                                reduction: ((1 - processedFile.size / file.size) * 100)
                                    .toFixed(1) + '%'
                            });

                            // Cleanup FFmpeg files (optional, save memory)
                            try {
                                await ffmpeg.deleteFile(file.name);
                                await ffmpeg.deleteFile('output.mp4');
                                console.log('**FFMPEG CLEANUP**: Files deleted');
                            } catch (e) {
                                console.warn('**FFMPEG CLEANUP**: Skip', e.message);
                            }

                        } else if (isImage) {
                            // Kompres image
                            this.progress = 20;
                            options.progress(true, 20, 100);
                            console.log('**IMAGE COMPRESS START**:', file.name);
                            processedFile = await this.compressImage(file);
                            this.progress = 80;
                            options.progress(true, 80, 100);
                            console.log('**IMAGE COMPRESS DONE**: Ready for upload');

                        } else {
                            console.warn('**UNSUPPORTED TYPE**:', file.type, '- upload original');
                            options.progress(true, 100, 100);
                        }

                        return processedFile;
                    } catch (error) {
                        console.error('**TRANSFORM ERROR**:', error);
                        this.progress = 0;
                        options.progress(true, 0, 100); // Reset bar
                        this.uploadError = 'Gagal proses file (' + file.name + '): ' + error
                            .message + ' (upload original)';
                        this.isProcessing = false;
                        Swal.fire('Error Proses', this.uploadError, 'warning');
                        return file; // Fallback original
                    }
                },
                // SubmitForm (full dengan progress simulate + thumbnail append)
                async submitForm() {
                    this.shakeError = false;
                    this.uploadError = '';

                    if (!this.title || !this.description || !this.location_address) {
                        this.shakeError = true;
                        setTimeout(() => this.shakeError = false, 820);
                        Swal.fire('Data Belum Lengkap', 'Mohon isi semua field yang wajib diisi.',
                            'warning');
                        return;
                    }

                    const uploadedFiles = this.pond.getFiles();
                    if (uploadedFiles.length === 0) {
                        this.shakeError = true;
                        setTimeout(() => this.shakeError = false, 820);
                        Swal.fire('Data Belum Lengkap', 'Mohon upload setidaknya satu bukti.',
                            'warning');
                        return;
                    }

                    const processingFiles = uploadedFiles.filter(file => file.status !== 5);
                    if (processingFiles.length > 0) {
                        Swal.fire('Proses Upload', 'Mohon tunggu semua file selesai diunggah.',
                            'info');
                        return;
                    }

                    const successfullyUploadedFiles = this.files.filter(f => f.serverPath && f
                        .serverPath.trim() !== '');
                    if (successfullyUploadedFiles.length === 0) {
                        Swal.fire('Error', 'Tidak ada file yang berhasil diupload.', 'error');
                        return;
                    }

                    console.log('**SUBMIT START**: Files ready', successfullyUploadedFiles.length,
                        'with thumbnails:', successfullyUploadedFiles.filter(f => f.thumbnail)
                        .length);

                    this.isSubmitting = true;
                    this.progress = 0;

                    const progressInterval = setInterval(() => {
                        if (this.progress < 90) {
                            this.progress += Math.random() * 10 + 5;
                            if (this.progress > 90) this.progress = 90;
                        }
                    }, 200);

                    try {
                        const formData = new FormData();
                        formData.append('title', this.title);
                        formData.append('description', this.description);
                        formData.append('location_address', this.location_address);
                        formData.append('images', JSON.stringify(successfullyUploadedFiles.map(f =>
                            f.serverPath)));

                        // Append thumbnails (video only, index match)
                        successfullyUploadedFiles.forEach((file, index) => {
                            if (file.thumbnail && file.type === 'video') {
                                console.log('**THUMBNAIL APPEND**: Index', index, 'size ~' +
                                    (file.thumbnail.length / 1024).toFixed(1) + 'KB');
                                formData.append(`video_thumbnails[${index}]`, file
                                    .thumbnail);
                            }
                        });

                        console.log('**SUBMIT FORM DATA**:', {
                            title: this.title.substring(0, 50) + '...',
                            images_count: successfullyUploadedFiles.length,
                            thumbnails_count: successfullyUploadedFiles.filter(f => f
                                .thumbnail).length
                        });

                        const response = await fetch('{{ route('laporan.store') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        });

                        this.progress = 100;
                        clearInterval(progressInterval);
                        setTimeout(() => {
                            this.progress = 0;
                        }, 1000);

                        if (!response.ok) {
                            const err = await response.json();
                            throw {
                                response: {
                                    status: response.status,
                                    data: err
                                }
                            };
                        }

                        const data = await response.json();
                        console.log('**SUBMIT SUKSES**:', data.message);
                        Swal.fire('Berhasil!', data.message || 'Laporan berhasil dikirim!',
                                'success')
                            .then(() => {
                                if (data.redirect_url) {
                                    window.location.href = data.redirect_url;
                                } else {
                                    window.location.href = '{{ route('dashboard') }}';
                                }
                            });
                    } catch (error) {
                        clearInterval(progressInterval);
                        this.progress = 0;
                        console.error('**SUBMIT ERROR**:', error);
                        this.isSubmitting = false;
                        let errorMessage = 'Terjadi kesalahan saat mengirim laporan.';
                        if (error.response && error.response.status === 422 && error.response
                            .data) {
                            const errors = error.response.data.errors || {};
                            errorMessage = Object.values(errors).flat().join('\n');
                        } else if (error.message) {
                            errorMessage = error.message;
                        }
                        Swal.fire('Error', errorMessage, 'error');
                        this.shakeError = true;
                        setTimeout(() => this.shakeError = false, 820);
                    }
                }
            }));
        });
    </script>
@endpush
