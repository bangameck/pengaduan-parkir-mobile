<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Laporan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-lg font-bold text-dishub-blue-800">Formulir Laporan Daring</h3>
                    <p class="mt-1 text-sm text-gray-500 mb-8">Isi detail laporan yang diterima dari berbagai sumber.</p>

                    {{-- Menggunakan AlpineJS Handler yang baru dan lebih lengkap --}}
                    <div x-data="adminReportFormHandler()">
                        <form x-ref="reportForm" @submit.prevent="submitForm" class="space-y-8">
                            @csrf

                            {{-- Input Sumber Laporan --}}
                            <div>
                                <label for="source" class="block mb-2 text-sm font-medium text-gray-700">Sumber Laporan</label>
                                <select id="source" name="source" x-model="source"
                                    class="border-gray-300 focus:border-dishub-blue-500 focus:ring-dishub-blue-500 rounded-md shadow-sm w-full text-sm">
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="tiktok">TikTok</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>

                            {{-- Form Dinamis Berdasarkan Sumber --}}
                            <template x-if="source === 'whatsapp'">
                                <div class="space-y-8" x-transition>
                                    <div class="relative">
                                        <input type="text" id="resident_name_wa" name="resident_name" value="{{ old('resident_name') }}" class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                                        <label for="resident_name_wa" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Nama Pelapor</label>
                                    </div>
                                    <div class="relative">
                                        <input type="tel" id="source_contact_wa" name="source_contact" value="{{ old('source_contact') }}" class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                                        <label for="source_contact_wa" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Nomor WhatsApp Pelapor</label>
                                    </div>
                                </div>
                            </template>
                            <template x-if="['facebook', 'tiktok', 'instagram'].includes(source)">
                                <div class="relative" x-transition>
                                    <input type="text" id="source_contact_social" name="source_contact" value="{{ old('source_contact') }}" class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                                    <label for="source_contact_social" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Username Akun Pelapor</label>
                                </div>
                            </template>
                            <template x-if="source === 'lainnya'">
                                <div class="relative" x-transition>
                                    <input type="text" id="source_contact_other" name="source_contact" value="{{ old('source_contact') }}" class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                                    <label for="source_contact_other" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Sebutkan Sumber Lainnya</label>
                                </div>
                            </template>

                            {{-- Form yang Selalu Tampil --}}
                            <div class="relative">
                                <input type="text" id="title" name="title" required value="{{ old('title') }}" class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                                <label for="title" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Judul Laporan</label>
                            </div>
                            <div class="relative">
                                <textarea id="description" name="description" rows="5" required class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" ">{{ old('description') }}</textarea>
                                <label for="description" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Deskripsi Lengkap</label>
                            </div>
                            <div class="relative">
                                <input type="text" id="location_address" name="location_address" required value="{{ old('location_address') }}" class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " />
                                <label for="location_address" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Alamat/Lokasi Kejadian</label>
                            </div>

                            {{-- Input FilePond --}}
                            <div wire:ignore>
                                <label for="file_input" class="block mb-2 text-sm font-medium text-gray-700">Dokumentasi (Foto/Video)</label>
                                <input type="file" id="file_input" x-ref="filepond" multiple>
                            </div>

                            {{-- Tombol Submit dan Progress Bar --}}
                            <div>
                                <button type="submit" :disabled="isSubmitting"
                                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-blue-400 disabled:cursor-not-allowed">
                                    <span x-show="isSubmitting" class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span x-text="`Memproses ${currentFileName}...`"></span>
                                    </span>
                                    <span x-show="!isSubmitting">Kirim Laporan</span>
                                </button>
                                <div x-show="isSubmitting" class="mt-4" x-transition>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" :style="`width: ${progress}%`"></div>
                                    </div>
                                    <div class="flex justify-between items-center mt-1">
                                        <p class="text-xs text-gray-500 animate-pulse">Lama proses tergantung ukuran file...</p>
                                        <p class="text-sm font-semibold text-blue-600" x-text="`${progress}%`"></p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminReportFormHandler', () => ({
                source: 'whatsapp',
                isSubmitting: false,
                progress: 0,
                pond: null,
                currentFileName: '',
                init() {
                    FilePond.registerPlugin(
                        FilePondPluginImagePreview, FilePondPluginFileValidateType, FilePondPluginFileValidateSize
                    );
                    this.pond = FilePond.create(this.$refs.filepond, {
                        allowMultiple: true, maxFiles: 5, maxFileSize: '25MB',
                        acceptedFileTypes: ['image/jpeg', 'image/png', 'video/mp4', 'video/quicktime', 'video/x-msvideo'],
                        server: {
                            process: { url: '{{ route('temp.upload') }}', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, onload: (response) => JSON.parse(response).location },
                            revert: { url: '{{ route('temp.revert') }}', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }
                        },
                        labelIdle: `Seret & Lepas file atau <span class="filepond--label-action">Jelajahi</span>`,
                    });
                },
                async submitForm() {
                    // Validasi file
                    const hasFiles = this.pond && this.pond.getFiles().filter(file => file.status === 5 && file.serverId).length > 0;
                    if (!hasFiles) {
                        return Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Mohon unggah minimal satu file bukti.' });
                    }

                    this.isSubmitting = true;
                    this.progress = 0;

                    // Logika Progress Bar
                    const successfullyUploadedFiles = this.pond.getFiles().filter(file => file.status === 5 && file.serverId);
                    const fileNames = successfullyUploadedFiles.map(f => f.file.name);
                    this.currentFileName = fileNames[0] || 'data laporan';
                    const progressInterval = setInterval(() => {
                        if (this.progress < 95) this.progress += 5;
                        const fileIndex = Math.floor((this.progress / 100) * fileNames.length);
                        if (fileNames[fileIndex]) this.currentFileName = fileNames[fileIndex];
                    }, 200);

                    try {
                        const formData = new FormData(this.$refs.reportForm);
                        formData.delete('images[]'); // Hapus file input bawaan

                        const imagePaths = successfullyUploadedFiles.map(pondFile => pondFile.serverId);
                        formData.append('images', JSON.stringify(imagePaths));

                        const response = await fetch('{{ route('admin.laporan.store') }}', {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        });

                        clearInterval(progressInterval);
                        this.progress = 100;

                        const data = await response.json();
                        if (!response.ok) {
                            if (response.status === 422) {
                                const validationErrors = Object.values(data.errors).flat().join('\n');
                                throw new Error(validationErrors);
                            }
                            throw new Error(data.message || 'Terjadi kesalahan server.');
                        }

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
</x-app-layout>
