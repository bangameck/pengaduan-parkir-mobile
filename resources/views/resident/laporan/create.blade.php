@extends('layouts.mobile')

@section('title', 'Buat Laporan Baru')

@section('skeleton')
    <x-skeletons.create-report />
@endsection

@section('content')
    {{-- Header Halaman --}}
    <x-resident-header />
    <x-page-header>Buat Laporan Baru</x-page-header>

    {{-- Formulir dengan Kontrol Alpine.js untuk AJAX --}}
    <div class="p-4 sm:p-6" x-data="{
        isSubmitting: false,
        progress: 0,
        title: '{{ old('title') }}',
        description: '{{ old('description') }}',
        location_address: '{{ old('location_address') }}',
        shakeError: false,
        submitForm() {
            this.shakeError = false;
            // Validasi Sederhana di Frontend
            if (!this.title || !this.description || !this.location_address) {
                this.shakeError = true;
                setTimeout(() => this.shakeError = false, 820);
                return;
            }
    
            this.isSubmitting = true;
            this.progress = 0;
    
            // Siapkan data form untuk dikirim via AJAX
            const formData = new FormData(this.$refs.reportForm);
    
            // Kirim data menggunakan Axios
            axios.post('{{ route('laporan.store') }}', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
                // Ini adalah 'mata-mata' progress kita yang baru!
                onUploadProgress: (progressEvent) => {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    this.progress = percentCompleted;
                }
            }).then(response => {
                // Jika SUKSES, redirect ke dashboard
                window.location.href = '{{ route('dashboard') }}?from_creation=1';
            }).catch(error => {
                // Jika GAGAL, tampilkan notifikasi error
                this.isSubmitting = false;
                this.progress = 0;
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                if (error.response && error.response.status === 422) {
                    // Jika error validasi dari Laravel
                    errorMessage = Object.values(error.response.data.errors).flat().join(' ');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMessage,
                });
            });
        }
    }">

        <form x-ref="reportForm" @submit.prevent="submitForm" class="space-y-8">
            @csrf

            <div class="relative">
                <input type="text" name="title" id="title" required x-model="title"
                    :class="{
                        'border-red-500 animate-shake': shakeError && !title,
                        'border-green-500': title,
                        'border-gray-300': !shakeError && !title
                    }"
                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 appearance-none focus:outline-none focus:ring-0 peer transition-colors"
                    placeholder=" " />
                {{-- KUNCI PERBAIKAN: Semua class floating label digabungkan di sini --}}
                <label for="title"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-gray-50 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">
                    Judul Laporan
                </label>
            </div>

            {{-- Floating Label Input: Deskripsi --}}
            <div class="relative">
                <textarea name="description" id="description" rows="4" required x-model="description"
                    :class="{
                        'border-red-500 animate-shake': shakeError && !description,
                        'border-green-500': description,
                        'border-gray-300': !shakeError && !description
                    }"
                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 appearance-none focus:outline-none focus:ring-0 peer transition-colors"
                    placeholder=" ">{{ old('description') }}</textarea>
                <label for="description"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-gray-50 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">
                    Deskripsi Lengkap
                </label>
            </div>

            {{-- Floating Label Input: Alamat --}}
            <div class="relative">
                <input type="text" name="location_address" id="location_address" required x-model="location_address"
                    :class="{
                        'border-red-500 animate-shake': shakeError && !location_address,
                        'border-green-500': location_address,
                        'border-gray-300': !shakeError && !location_address
                    }"
                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 appearance-none focus:outline-none focus:ring-0 peer transition-colors"
                    placeholder=" " />
                <label for="location_address"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-gray-50 px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">
                    Alamat/Lokasi Kejadian
                </label>
            </div>

            {{-- FilePond Upload --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Foto/Video Bukti (Maks 5 file)</label>
                <input type="file" name="images[]" id="images" class="filepond" multiple required>
                @error('images.*') {{-- Menampilkan error validasi dari server jika ada --}}
                    @foreach ($errors->get('images.*') as $messages)
                        @foreach ($messages as $message)
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @endforeach
                    @endforeach
                @enderror
            </div>

            {{-- Tombol Submit & Progress Bar Baru --}}
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
