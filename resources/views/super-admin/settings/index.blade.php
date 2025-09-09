<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Aplikasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-lg font-bold text-dishub-blue-800">Konfigurasi Umum</h3>
                    <p class="mt-1 text-sm text-gray-500 mb-8">Atur detail umum aplikasi, logo, dan integrasi API.</p>

                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('super-admin.settings.update') }}"
                        enctype="multipart/form-data" class="space-y-8" x-data="{
                            isSubmitting: false,
                            tokenVisible: false,
                            imagePreviewUrl: null
                        }"
                        @submit="isSubmitting = true">
                        @csrf

                        <div>
                            <x-input-label for="app_name" :value="__('Nama Aplikasi')" />
                            <x-text-input id="app_name" class="block mt-1 w-full" type="text" name="app_name"
                                :value="old('app_name', $settings['app_name'] ?? config('app.name'))" required />
                            <x-input-error :messages="$errors->get('app_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="app_logo" :value="__('Logo Aplikasi (Opsional)')" />
                            <div class="mt-2 flex items-center gap-4">
                                {{-- Tampilkan logo saat ini atau preview logo baru --}}
                                <div class="w-20 h-20 flex-shrink-0">
                                    <img x-show="!imagePreviewUrl"
                                        src="{{ isset($settings['app_logo']) ? Storage::url($settings['app_logo']) : 'https://via.placeholder.com/150' }}"
                                        alt="Logo Saat Ini"
                                        class="h-full w-full object-contain rounded-md bg-gray-100 p-1 border">
                                    <img x-show="imagePreviewUrl" x-bind:src="imagePreviewUrl" alt="Preview Logo Baru"
                                        class="h-full w-full object-contain rounded-md bg-gray-100 p-1 border">
                                </div>
                                <input @change="imagePreviewUrl = URL.createObjectURL($event.target.files[0])"
                                    type="file" name="app_logo" id="app_logo"
                                    class="block w-full text-sm text-slate-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100
                                " />
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Ganti logo. Biarkan kosong jika tidak ingin mengubah.
                                (Format: PNG, JPG. Maks: 1MB)</p>
                            <x-input-error :messages="$errors->get('app_logo')" class="mt-2" />
                        </div>

                        <div class="border-t pt-6">
                            <x-input-label for="fonnte_token" :value="__('Fonnte API Token')" />
                            <div class="relative mt-1">
                                <input :type="tokenVisible ? 'text' : 'password'" id="fonnte_token" name="fonnte_token"
                                    value="{{ old('fonnte_token', $settings['fonnte_token'] ?? config('services.fonnte.token')) }}"
                                    class="block w-full font-mono border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm pr-10">
                                <button @click.prevent="tokenVisible = !tokenVisible" type="button"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                    {{-- Ikon Mata Terbuka --}}
                                    <svg x-show="!tokenVisible" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{-- Ikon Mata Tercoret --}}
                                    <svg x-show="tokenVisible" x-cloak class="w-5 h-5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 .847 0 1.67.127 2.452.368m4.438 4.438A9.953 9.953 0 0117 12c0 .94-.132 1.85-.369 2.712m-2.121-5.321c.49-.603.84-1.295 1.126-2.032M3.75 4.5L21.25 22.5" />
                                    </svg>
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Masukkan API Token dari layanan WhatsApp Gateway
                                Fonnte.</p>
                            <x-input-error :messages="$errors->get('fonnte_token')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-8 border-t pt-6">
                            <button type="submit" x-bind:disabled="isSubmitting"
                                class="inline-flex items-center justify-center px-4 py-2 bg-dishub-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dishub-blue-700 active:bg-dishub-blue-900 focus:outline-none focus:ring-2 focus:ring-dishub-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">

                                <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span x-show="!isSubmitting">Simpan Pengaturan</span>
                                <span x-show="isSubmitting">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
