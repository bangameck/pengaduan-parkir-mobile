@extends('layouts.mobile')

@section('title', 'Edit Laporan')

@section('skeleton')
    <x-skeletons.create-report />
@endsection

@section('content')
    {{-- Header Halaman --}}
    <x-resident-header />
    <x-page-header>Laporan #{{ $report->report_code }}</x-page-header>

    {{-- Progress Bar --}}
    <div id="progress-container" class="w-full h-1 bg-blue-100 fixed top-0 left-0 z-50 hidden">
        <div id="progress-bar" class="h-1 bg-blue-500 transition-all duration-500" style="width: 0%"></div>
    </div>

    {{-- Formulir dengan Kontrol Alpine.js --}}
    <div class="p-4 sm:p-6" x-data="{
        isSubmitting: false,
        progress: 0,
        deletedImages: [], // Menyimpan ID gambar yang akan dihapus
        title: `{{ old('title', $report->title) }}`,
        description: `{{ old('description', $report->description) }}`,
        location_address: `{{ old('location_address', $report->location_address) }}`,
        shakeError: false,
        submitForm() {
            this.shakeError = false;
            if (!this.title || !this.description || !this.location_address) {
                this.shakeError = true;
                setTimeout(() => this.shakeError = false, 820);
                return;
            }
            this.isSubmitting = true;
            this.progress = 0;
    
            const formData = new FormData(this.$refs.reportForm);
    
            // PENTING: Kita harus menambahkan _method PATCH secara manual untuk AJAX
            formData.append('_method', 'PATCH');
    
            // Kirim data menggunakan Axios
            axios.post('{{ route('laporan.update', $report) }}', formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
                onUploadProgress: (progressEvent) => {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    this.progress = percentCompleted;
                }
            }).then(response => {
                // Jika SUKSES, redirect ke halaman detail
                window.location.href = '{{ route('laporan.show', $report) }}?from_update=1';
            }).catch(error => {
                // Jika GAGAL, tampilkan notifikasi error
                this.isSubmitting = false;
                this.progress = 0;
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                if (error.response && error.response.status === 422) {
                    errorMessage = Object.values(error.response.data.errors).flat().join(' ');
                }
                Swal.fire({ icon: 'error', title: 'Oops...', text: errorMessage });
            });
        }
    }">

        <form x-ref="reportForm" @submit.prevent="submitForm" class="space-y-8">
            @csrf
            {{-- Method PATCH sesungguhnya dikirim via FormData di Alpine.js --}}

            {{-- Floating Label Input: Judul Laporan --}}
            <div class="relative">
                <input type="text" name="title" id="title" required x-model="title"
                    :class="{
                        'border-red-500 animate-shake': shakeError && !
                            title,
                        'border-green-500': title,
                        'border-gray-300': !shakeError && !title
                    }"
                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 appearance-none focus:outline-none focus:ring-0 peer transition-colors"
                    placeholder=" " />
                <label for="title"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-gray-50 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Judul
                    Laporan</label>
            </div>

            {{-- Floating Label Input: Deskripsi --}}
            <div class="relative">
                <textarea name="description" id="description" rows="4" required x-model="description"
                    :class="{
                        'border-red-500 animate-shake': shakeError && !
                            description,
                        'border-green-500': description,
                        'border-gray-300': !shakeError && !description
                    }"
                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 appearance-none focus:outline-none focus:ring-0 peer transition-colors"
                    placeholder=" " x-text="description"></textarea>
                <label for="description"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-gray-50 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Deskripsi
                    Lengkap</label>
            </div>

            {{-- Floating Label Input: Alamat --}}
            <div class="relative">
                <input type="text" name="location_address" id="location_address" required x-model="location_address"
                    :class="{
                        'border-red-500 animate-shake': shakeError && !
                            location_address,
                        'border-green-500': location_address,
                        'border-gray-300': !shakeError && !
                            location_address
                    }"
                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 appearance-none focus:outline-none focus:ring-0 peer transition-colors"
                    placeholder=" " />
                <label for="location_address"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-gray-50 px-2 ...">Alamat/Lokasi
                    Kejadian</label>
            </div>

            {{-- DOKUMENTASI SAAT INI (dengan tombol hapus) --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Dokumentasi Saat Ini</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach ($report->images as $media)
                        <div class="relative" x-show="!deletedImages.includes({{ $media->id }})" x-transition>
                            {{-- Input tersembunyi untuk mengirim ID gambar yang mau dihapus --}}
                            <template x-if="deletedImages.includes({{ $media->id }})">
                                <input type="hidden" name="delete_images[]" :value="{{ $media->id }}">
                            </template>

                            <img src="{{ $media->file_type == 'video' ? Storage::url($media->thumbnail_path) : Storage::url($media->file_path) }}"
                                class="w-full aspect-square object-cover rounded-md">
                            <button type="button" @click="deletedImages.push({{ $media->id }})"
                                class="absolute top-1 right-1 bg-red-600 text-white rounded-full p-0.5 w-6 h-6 flex items-center justify-center shadow-lg hover:bg-red-700 transition">&times;</button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- TAMBAH DOKUMENTASI BARU --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Tambah Foto/Video Bukti Baru</label>
                <input type="file" name="images[]" id="images" class="filepond" multiple>
            </div>

            {{-- Tombol Submit & Progress Bar --}}
            <div>
                <button type="submit" :disabled="isSubmitting"
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-blue-400 disabled:cursor-not-allowed">
                    <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span
                        x-text="isSubmitting ? (progress < 100 ? `Mengupload...` : 'Menyimpan...') : 'Kirim Laporan'">Kirim
                        Laporan</span>
                </button>

                {{-- Progress Bar & Info Teks --}}
                <div x-show="isSubmitting" class="mt-4" x-transition>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                            :style="`width: ${progress}%`"></div>
                    </div>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-xs text-gray-500 animate-pulse">Lama proses tergantung ukuran file...</p>
                        <p class="text-sm font-semibold text-blue-600" x-text="`${progress}%`"></p>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
