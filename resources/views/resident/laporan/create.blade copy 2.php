@extends('layouts.mobile')

@section('title', 'Buat Laporan Baru')

@push('style')
    {{-- CDN untuk FilePond --}}
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <style>
        /* Kustomisasi FilePond */
        .filepond--root { font-family: 'Inter', sans-serif; margin-bottom: 1.5rem; }
        .filepond--panel-root { background-color: #f9fafb; border-radius: 0.5rem; }
        .filepond--item-panel { border-radius: 0.5rem; }

        /* Style untuk Floating Labels (agar bekerja dengan 'placeholder-shown') */
        .floating-label .peer:not(:placeholder-shown) ~ label,
        .floating-label .peer:focus ~ label {
            transform: translateY(-1.5rem) scale(0.75);
            color: #2563eb; /* blue-600 */
        }
        .floating-label .peer:focus ~ label {
             color: #2563eb; /* blue-600 */
        }

        /* Animasi 'shake' untuk validasi */
        .shake { animation: shake 0.82s cubic-bezier(.36, .07, .19, .97) both; transform: translate3d(0, 0, 0); }
        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }
    </style>
@endpush

@section('content')
    <x-resident-header />
    <x-page-header>Buat Laporan Baru</x-page-header>

    <div class="p-4 sm:p-6 pb-24" x-data="reportFormHandler()">
        <form @submit.prevent="submitForm" x-ref="reportForm" novalidate :class="{ 'shake': shakeError }">

            {{-- Form Fields dengan Floating Labels --}}
            <div class="relative z-0 w-full mb-8 floating-label">
                <input type="text" id="title" x-model="title"
                    class="block pt-3 pb-2 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" " required />
                <label for="title"
                    class="absolute text-sm text-gray-500 duration-300 transform origin-[0] top-3 -z-10">Judul Laporan</label>
            </div>

            <div class="relative z-0 w-full mb-8 floating-label">
                <textarea id="description" rows="2" x-model="description"
                    class="block pt-3 pb-2 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" " required></textarea>
                <label for="description"
                    class="absolute text-sm text-gray-500 duration-300 transform origin-[0] top-3 -z-10">Deskripsi Laporan</label>
            </div>

            <div class="relative z-0 w-full mb-8 floating-label">
                <input type="text" id="location_address" x-model="location_address"
                    class="block pt-3 pb-2 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" " required />
                <label for="location_address"
                    class="absolute text-sm text-gray-500 duration-300 transform origin-[0] top-3 -z-10">Alamat Lokasi</label>
            </div>


            {{-- FilePond Input --}}
            <div wire:ignore>
                 <label class="block mb-2 text-sm font-medium text-gray-900">Bukti Laporan (Max 5 File)</label>
                <input type="file" x-ref="filepond" multiple>
            </div>

            {{-- Tombol Kirim --}}
            <div class="mt-6">
                 <button type="submit" :disabled="isProcessing || isSubmitting"
                    class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center transition-opacity">
                    <span x-text="isProcessing ? 'Sedang Memproses File...' : (isSubmitting ? 'Mengirim Laporan...' : 'Kirim Laporan')"></span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('script')
    {{-- CDN Lengkap untuk FilePond dan FFmpeg --}}
    <script src="https://cdn.jsdelivr.net/npm/@ffmpeg/ffmpeg@0.12.10/dist/umd/ffmpeg.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>

    <script>
        // PERBAIKAN: Membungkus logika Alpine di dalam event listener 'alpine:init'.
        // Ini adalah cara paling aman untuk memastikan Alpine siap sebelum
        // fungsi kustom Anda didefinisikan, terlepas dari urutan skrip di layout.
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportFormHandler', () => ({
                title: '{{ old('title') }}',
                description: '{{ old('description') }}',
                location_address: '{{ old('location_address') }}',
                isSubmitting: false,
                isProcessing: false,
                shakeError: false,
                progress: 0,
                ffmpeg: null,
                files: [], // Array untuk menyimpan { id, thumbnail, serverPath }
                pond: null,

                init() {
                    FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateType);

                    this.pond = FilePond.create(this.$refs.filepond, {
                        allowMultiple: true,
                        maxFiles: 5,
                        acceptedFileTypes: ['image/jpeg', 'image/png', 'video/mp4', 'video/quicktime'],
                        server: {
                            process: '{{ route("temp.upload") }}',
                            revert: '{{ route("temp.revert") }}',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        },
                        transformFile: (file, options) => this.transformFile(file, options),
                        onprocessfile: (error, file) => {
                            if (!error) {
                                const managedFile = this.files.find(f => f.id === file.id);
                                if (managedFile) {
                                    managedFile.serverPath = file.serverId;
                                }
                            }
                        },
                         onremovefile: (error, file) => {
                            this.files = this.files.filter(f => f.id !== file.id);
                        },
                        labelIdle: `Seret & Lepas file Anda atau <span class="filepond--label-action">Jelajahi</span>`
                    });
                },

                async initFFmpeg() {
                    if (this.ffmpeg) return;
                    this.isProcessing = true;
                    this.ffmpeg = new FFmpeg.FFmpeg();
                    await this.ffmpeg.load({ coreURL: "https://cdn.jsdelivr.net/npm/@ffmpeg/core@0.12.6/dist/umd/ffmpeg-core.js" });
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
                        video.onerror = () => resolve('');
                    });
                },

                async transformFile(file, options) {
                    const isVideo = file.type.startsWith('video/');
                    const thumbnail = isVideo ? await this.generateVideoThumbnail(file) : '';

                    this.files.push({ id: file.id, thumbnail: thumbnail, serverPath: null });

                    if (!isVideo) return file;

                    await this.initFFmpeg();
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
                        this.isProcessing = this.pond.getFiles().some(f => f.status === 1);
                    });

                    return new File([data.buffer], `compressed_${file.name}`, { type: 'video/mp4' });
                },

                submitForm() {
                    this.shakeError = false;
                    if (!this.title || !this.description || !this.location_address || this.pond.getFiles().length === 0) {
                        this.shakeError = true;
                        setTimeout(() => this.shakeError = false, 820);
                        Swal.fire('Data Belum Lengkap', 'Mohon isi semua field dan upload setidaknya satu bukti.', 'warning');
                        return;
                    }
                    if(this.pond.getFiles().some(file => file.status !== 5)) {
                        Swal.fire('Proses Upload', 'Mohon tunggu semua file selesai diunggah.', 'info');
                        return;
                    }

                    this.isSubmitting = true;
                    const successfullyUploadedFiles = this.files.filter(f => f.serverPath);

                    axios.post('{{ route("laporan.store") }}', {
                        title: this.title,
                        description: this.description,
                        location_address: this.location_address,
                        images: successfullyUploadedFiles.map(f => f.serverPath),
                        video_thumbnails: successfullyUploadedFiles.map(f => f.thumbnail),
                    }, {
                        onUploadProgress: (progressEvent) => {
                            this.progress = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                        }
                    }).then(response => {
                        window.location.href = response.data.redirect_url;
                    }).catch(error => {
                        this.isSubmitting = false;
                        let errorMessage = 'Terjadi kesalahan saat mengirim laporan.';
                        if (error.response && error.response.status === 422) {
                            errorMessage = Object.values(error.response.data.errors).flat().join(' ');
                        }
                        Swal.fire('Error', errorMessage, 'error');
                        console.error(error.response.data);
                    });
                }
            }));
        });
    </script>
@endpush
