<x-app-layout>
    {{-- Custom CSS for animations and styling --}}
    <style>
        .drag-area {
            transition: all 0.3s ease;
        }

        .drag-area-over {
            transform: scale(1.02);
            box-shadow: 0 0 25px rgba(30, 86, 160, 0.4);
            border-color: #1e56a0;
            /* dishub-blue-700 */
        }

        .fade-enter-active,
        .fade-leave-active {
            transition: opacity 0.3s ease;
        }

        .fade-enter-from,
        .fade-leave-to {
            opacity: 0;
        }
    </style>

    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            Pengaturan Aplikasi
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 md:p-8 text-gray-900">

                    <form method="POST" action="{{ route('super-admin.settings.update') }}" enctype="multipart/form-data"
                        x-data="settingsForm()" @submit="isSubmitting = true">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 border-b pb-8 mb-8">
                            <div class="md:col-span-1">
                                <h3 class="text-xl font-bold text-dishub-blue-800">Konfigurasi Umum</h3>
                                <p class="mt-2 text-sm text-gray-500">Atur nama, logo, dan identitas utama aplikasi
                                    Anda.</p>
                            </div>
                            <div class="md:col-span-2 space-y-8">
                                <div class="relative">
                                    <input type="text" id="app_name" name="app_name"
                                        class="block px-3.5 pb-2.5 pt-4 w-full text-base text-gray-900 bg-transparent rounded-lg border-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-dishub-blue-600 peer"
                                        placeholder=" "
                                        value="{{ old('app_name', $settings['app_name'] ?? config('app.name')) }}"
                                        required />
                                    <label for="app_name"
                                        class="absolute text-base text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-dishub-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">
                                        Nama Aplikasi
                                    </label>
                                    <x-input-error :messages="$errors->get('app_name')" class="mt-2" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Logo Aplikasi</label>
                                    <div class="drag-area w-full p-6 border-2 border-dashed border-gray-300 rounded-lg text-center cursor-pointer hover:border-dishub-blue-500"
                                        :class="{ 'drag-area-over': isDraggingLogo }"
                                        @dragover.prevent="isDraggingLogo = true"
                                        @dragleave.prevent="isDraggingLogo = false"
                                        @drop.prevent="handleFileDrop($event, 'logo')" @click="$refs.logoInput.click()">

                                        <input type="file" name="app_logo" id="app_logo" class="hidden"
                                            x-ref="logoInput" @change="handleFileChange($event, 'logo')"
                                            accept="image/png, image/jpeg, image/gif">

                                        <div x-show="!logo.previewUrl" class="space-y-2">
                                            <div
                                                class="mx-auto bg-gray-100 rounded-full h-16 w-16 flex items-center justify-center">
                                                <svg class="h-8 w-8 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm text-gray-600"><span
                                                    class="font-semibold text-dishub-blue-700">Klik untuk upload</span>
                                                atau seret dan lepas</p>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF (Maks. 1MB)</p>
                                        </div>

                                        <div x-show="logo.previewUrl" x-transition:enter="fade-enter-active"
                                            x-transition:enter-start="fade-enter-from"
                                            x-transition:enter-end="fade-enter-to" class="relative text-left">
                                            <div class="flex items-center space-x-4">
                                                <img :src="logo.previewUrl"
                                                    class="h-20 w-20 rounded-lg object-contain bg-gray-50 p-1 border">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-800" x-text="logo.name"></p>
                                                    <p class="text-xs text-gray-500" x-text="logo.size"></p>
                                                </div>
                                                <button @click.stop.prevent="removeFile('logo')" type="button"
                                                    class="p-1 text-gray-400 rounded-full hover:bg-gray-200 hover:text-gray-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('app_logo')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 border-b pb-8 mb-8">
                            <div class="md:col-span-1">
                                <h3 class="text-xl font-bold text-dishub-blue-800">Pop-up Promosi</h3>
                                <p class="mt-2 text-sm text-gray-500">Atur banner informasi yang muncul di halaman
                                    utama.</p>
                            </div>
                            <div class="md:col-span-2 space-y-8">
                                <label for="popup_enabled"
                                    class="inline-flex items-center cursor-pointer p-2 rounded-lg hover:bg-gray-50">
                                    <input type="checkbox" id="popup_enabled" name="popup_enabled" value="1"
                                        class="sr-only peer"
                                        {{ ($settings['popup_enabled'] ?? '0') == '1' ? 'checked' : '' }}>
                                    <div
                                        class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600">
                                    </div>
                                    <span class="ms-3 text-sm font-medium text-gray-900">Aktifkan Pop-up
                                        Informasi</span>
                                </label>

                                <div class="relative">
                                    <input type="text" id="popup_title" name="popup_title"
                                        class="block px-3.5 pb-2.5 pt-4 w-full text-base text-gray-900 bg-transparent rounded-lg border-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-dishub-blue-600 peer"
                                        placeholder=" "
                                        value="{{ old('popup_title', $settings['popup_title'] ?? '') }}" />
                                    <label for="popup_title"
                                        class="absolute text-base text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-dishub-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Judul
                                        Pop-up</label>
                                </div>

                                <div class="relative">
                                    <textarea id="popup_text" name="popup_text" rows="3"
                                        class="block px-3.5 pb-2.5 pt-4 w-full text-base text-gray-900 bg-transparent rounded-lg border-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-dishub-blue-600 peer"
                                        placeholder=" ">{{ old('popup_text', $settings['popup_text'] ?? '') }}</textarea>
                                    <label for="popup_text"
                                        class="absolute text-base text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-dishub-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-4 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Teks/Deskripsi
                                        Pop-up</label>
                                </div>

                                <div class="relative">
                                    <input type="text" id="popup_button_text" name="popup_button_text"
                                        class="block px-3.5 pb-2.5 pt-4 w-full text-base text-gray-900 bg-transparent rounded-lg border-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-dishub-blue-600 peer"
                                        placeholder=" "
                                        value="{{ old('popup_button_text', $settings['popup_button_text'] ?? '') }}" />
                                    <label for="popup_button_text"
                                        class="absolute text-base text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-dishub-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Teks
                                        Tombol</label>
                                </div>

                                <div class="relative">
                                    <input type="url" id="popup_button_url" name="popup_button_url"
                                        class="block px-3.5 pb-2.5 pt-4 w-full text-base text-gray-900 bg-transparent rounded-lg border-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-dishub-blue-600 peer"
                                        placeholder=" "
                                        value="{{ old('popup_button_url', $settings['popup_button_url'] ?? '') }}" />
                                    <label for="popup_button_url"
                                        class="absolute text-base text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-dishub-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">URL
                                        Tombol (https://...)</label>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Banner Pop-up</label>
                                    <div class="drag-area w-full p-6 border-2 border-dashed border-gray-300 rounded-lg text-center cursor-pointer hover:border-dishub-blue-500"
                                        :class="{ 'drag-area-over': isDraggingBanner }"
                                        @dragover.prevent="isDraggingBanner = true"
                                        @dragleave.prevent="isDraggingBanner = false"
                                        @drop.prevent="handleFileDrop($event, 'banner')"
                                        @click="$refs.bannerInput.click()">

                                        <input type="file" name="popup_image" id="popup_image" class="hidden"
                                            x-ref="bannerInput" @change="handleFileChange($event, 'banner')"
                                            accept="image/png, image/jpeg, image/gif">

                                        <div x-show="!banner.previewUrl" class="space-y-2">
                                            <div
                                                class="mx-auto bg-gray-100 rounded-full h-16 w-16 flex items-center justify-center">
                                                <svg class="h-8 w-8 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm text-gray-600"><span
                                                    class="font-semibold text-dishub-blue-700">Klik untuk upload</span>
                                                atau seret dan lepas</p>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF (Maks. 1MB)</p>
                                        </div>

                                        <div x-show="banner.previewUrl" x-transition:enter="fade-enter-active"
                                            x-transition:enter-start="fade-enter-from"
                                            x-transition:enter-end="fade-enter-to" class="relative text-left">
                                            <div class="flex items-center space-x-4">
                                                <img :src="banner.previewUrl"
                                                    class="h-20 w-32 rounded-lg object-contain bg-gray-50 p-1 border">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-800" x-text="banner.name">
                                                    </p>
                                                    <p class="text-xs text-gray-500" x-text="banner.size"></p>
                                                </div>
                                                <button @click.stop.prevent="removeFile('banner')" type="button"
                                                    class="p-1 text-gray-400 rounded-full hover:bg-gray-200 hover:text-gray-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('popup_image')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <div class="md:col-span-1">
                                <h3 class="text-xl font-bold text-dishub-blue-800">Integrasi API</h3>
                                <p class="mt-2 text-sm text-gray-500">Hubungkan aplikasi dengan layanan pihak ketiga
                                    seperti Fonnte.</p>
                            </div>
                            <div class="md:col-span-2">
                                <div class="relative">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center z-20">
                                        <button @click.prevent="tokenVisible = !tokenVisible" type="button"
                                            class="text-gray-400 hover:text-gray-600">
                                            <svg x-show="!tokenVisible" class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg x-show="tokenVisible" x-cloak class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 .847 0 1.67.127 2.452.368m4.438 4.438A9.953 9.953 0 0117 12c0 .94-.132 1.85-.369 2.712m-2.121-5.321c.49-.603.84-1.295 1.126-2.032M3.75 4.5L21.25 22.5" />
                                            </svg>
                                        </button>
                                    </div>
                                    <input :type="tokenVisible ? 'text' : 'password'" id="fonnte_token"
                                        name="fonnte_token"
                                        class="block px-3.5 pb-2.5 pt-4 w-full text-base font-mono text-gray-900 bg-transparent rounded-lg border-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-dishub-blue-600 peer"
                                        placeholder=" "
                                        value="{{ old('fonnte_token', $settings['fonnte_token'] ?? config('services.fonnte.token')) }}" />
                                    <label for="fonnte_token"
                                        class="absolute text-base text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-dishub-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Fonnte
                                        API Token</label>
                                </div>
                                <x-input-error :messages="$errors->get('fonnte_token')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-10 pt-6 border-t">
                            <button type="submit" x-bind:disabled="isSubmitting"
                                class="inline-flex items-center justify-center px-6 py-3 bg-dishub-blue-800 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-dishub-blue-700 active:bg-dishub-blue-900 focus:outline-none focus:ring-2 focus:ring-dishub-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span x-show="!isSubmitting">Simpan Perubahan</span>
                                <span x-show="isSubmitting">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- AlpineJS Logic --}}
    <script>
        function settingsForm() {
            return {
                isSubmitting: false,
                tokenVisible: false,
                isDraggingLogo: false,
                isDraggingBanner: false,

                logo: {
                    previewUrl: '{{ isset($settings['app_logo']) ? Storage::url($settings['app_logo']) : '' }}',
                    name: 'logo_tersimpan.png',
                    size: ''
                },
                banner: {
                    previewUrl: '{{ isset($settings['popup_image']) ? Storage::url($settings['popup_image']) : '' }}',
                    name: 'banner_tersimpan.png',
                    size: ''
                },


                handleFileChange(event, type) {
                    this.processFile(event.target.files[0], type);
                },

                handleFileDrop(event, type) {
                    this[type === 'logo' ? 'isDraggingLogo' : 'isDraggingBanner'] = false;
                    this.processFile(event.dataTransfer.files[0], type);
                },

                processFile(file, type) {
                    if (!file || !file.type.startsWith('image/')) return;

                    const target = this[type];
                    target.name = file.name;
                    target.size = (file.size / 1024).toFixed(2) + ' KB';
                    target.previewUrl = URL.createObjectURL(file);
                },

                removeFile(type) {
                    const targetInput = this.$refs[type + 'Input'];
                    targetInput.value = null;

                    const target = this[type];
                    target.previewUrl = '';
                    target.name = '';
                    target.size = '';
                }
            }
        }
    </script>
</x-app-layout>
