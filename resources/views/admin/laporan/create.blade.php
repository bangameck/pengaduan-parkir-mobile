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

                    <div class="p-4 sm:p-6" x-data="{
                        isSubmitting: false,
                        progress: 0,
                        source: 'whatsapp',
                        isSubmitting: false,
                        description: '{{ old('description', '') }}',
                        submitForm() {
                    
                            this.isSubmitting = true;
                            this.progress = 0;
                    
                            // Siapkan data form untuk dikirim via AJAX
                            const formData = new FormData(this.$refs.reportForm);
                    
                            // Kirim data menggunakan Axios
                            axios.post('{{ route('admin.laporan.store') }}', formData, {
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
                                window.location.href = '{{ route('admin.laporan.index') }}?from_creation=1';
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
                            @if ($errors->any())
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                                    <p class="font-bold">Terjadi Kesalahan Validasi</p>
                                    <ul class="mt-2 list-disc list-inside text-sm">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            {{-- Input Sumber Laporan (Ini akan mengontrol form di bawahnya) --}}
                            <div>
                                <label for="source" class="block mb-2 text-sm font-medium text-gray-700">Sumber
                                    Laporan</label>
                                <select id="source" name="source" x-model="source"
                                    class="border-gray-300 focus:border-dishub-blue-500 focus:ring-dishub-blue-500 rounded-md shadow-sm w-full text-sm">
                                    <option value="whatsapp">WhatsApp</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="tiktok">TikTok</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>

                            {{-- ======================================================= --}}
                            {{-- == BAGIAN FORM YANG AKAN BERUBAH SECARA DINAMIS == --}}
                            {{-- ======================================================= --}}

                            {{-- Tampil jika sumbernya WhatsApp --}}
                            <template x-if="source === 'whatsapp'">
                                <div class="space-y-8" x-transition>
                                    <div class="relative">
                                        <input type="text" id="resident_name_wa" name="resident_name"
                                            value="{{ old('resident_name') }}"
                                            class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                            placeholder=" " />
                                        <label for="resident_name_wa"
                                            class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Nama
                                            Pelapor</label>
                                    </div>
                                    <div class="relative">
                                        <input type="tel" id="source_contact_wa" name="source_contact"
                                            value="{{ old('source_contact') }}"
                                            class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                            placeholder=" " />
                                        <label for="source_contact_wa"
                                            class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Nomor
                                            WhatsApp Pelapor</label>
                                        <x-input-error :messages="$errors->get('source_contact')" class="mt-2" />
                                    </div>
                                </div>
                            </template>

                            {{-- Tampil jika sumbernya Medsos --}}
                            <template x-if="['facebook', 'tiktok', 'instagram'].includes(source)">
                                <div class="space-y-8" x-transition>
                                    <div class="relative">
                                        <input type="text" id="source_contact_social" name="source_contact"
                                            value="{{ old('source_contact') }}"
                                            class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                            placeholder=" " />
                                        <label for="source_contact_social"
                                            class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Username
                                            Akun Pelapor</label>
                                        <x-input-error :messages="$errors->get('source_contact')" class="mt-2" />
                                    </div>
                                </div>
                            </template>

                            {{-- Tampil jika sumbernya Lainnya --}}
                            <template x-if="source === 'lainnya'">
                                <div class="space-y-8" x-transition>
                                    <div class="relative">
                                        <input type="text" id="source_contact_other" name="source_contact"
                                            value="{{ old('source_contact') }}"
                                            class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                            placeholder=" " />
                                        <label for="source_contact_other"
                                            class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Sebutkan
                                            Sumber Lainnya</label>
                                        <x-input-error :messages="$errors->get('source_contact')" class="mt-2" />
                                    </div>
                                </div>
                            </template>

                            {{-- =============================================== --}}
                            {{-- == BAGIAN FORM YANG SELALU TAMPIL == --}}
                            {{-- =============================================== --}}

                            <div class="relative">
                                <input type="text" id="title" name="title" required value="{{ old('title') }}"
                                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " />
                                <label for="title"
                                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Judul
                                    Laporan</ax_label>
                            </div>

                            <div class="relative">
                                <textarea id="description" name="description" rows="5" required x-model="description" {{-- Hubungkan dengan state Alpine --}}
                                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" "></textarea>
                                <label for="description"
                                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Deskripsi
                                    Lengkap</label>

                                {{-- ELEMEN PENGHITUNG KARAKTER --}}
                                <div class="mt-2 text-xs text-right">
                                    {{-- Tampil jika kurang dari 20 karakter --}}
                                    <span x-show="description.length < 20" class="text-red-600">
                                        Minimal 20 karakter (<span x-text="description.length"></span>/20)
                                    </span>
                                    {{-- Tampil jika sudah 20 karakter atau lebih --}}
                                    <span x-show="description.length >= 20"
                                        class="text-green-600 flex items-center justify-end">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Deskripsi cukup
                                    </span>
                                </div>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <div class="relative">
                                <input type="text" id="location_address" name="location_address" required
                                    value="{{ old('location_address') }}"
                                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " />
                                <label for="location_address"
                                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Alamat/Lokasi
                                    Kejadian</ax_label>
                            </div>

                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-700">Dokumentasi
                                    (Foto/Video)</label>
                                <input type="file" name="images[]" id="images" class="filepond" required
                                    multiple>
                            </div>
                            {{-- Kita ganti <x-primary-button> dengan <button> biasa --}}
                            <div>
                                <button type="submit" :disabled="isSubmitting"
                                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-blue-400 disabled:cursor-not-allowed">
                                    <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
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
                                        <p class="text-xs text-gray-500 animate-pulse">Lama proses tergantung
                                            ukuran
                                            file...</p>
                                        <p class="text-sm font-semibold text-blue-600" x-text="`${progress}%`">
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
