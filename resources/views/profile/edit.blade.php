@extends('layouts.mobile')

@section('title', 'Edit Profil')

@section('header')
    <x-page-header>Edit Profil</x-page-header>
@endsection

@section('content')
    <div class="p-4 sm:p-6 space-y-6" x-data="profileForm('{{ auth()->user()->image ? Storage::url(auth()->user()->image) : '' }}', '{{ urlencode(auth()->user()->name) }}')">

        {{-- BAGIAN 1: FORM UPDATE PROFIL --}}
        <div class="p-6 bg-white rounded-lg shadow-sm border">
            <header>
                <h2 class="text-lg font-medium text-gray-900">Informasi Profil</h2>
                <p class="mt-1 text-sm text-gray-600">Perbarui informasi profil dan data diri Anda.</p>
            </header>

            @if (session('status') === 'profile-updated')
                <div class="mt-4 p-3 bg-green-100 text-green-700 rounded-md text-sm" x-data="{ show: true }" x-show="show"
                    x-transition x-init="setTimeout(() => show = false, 3000)">
                    Profil berhasil diperbarui.
                </div>
            @endif

            <form x-ref="profileForm" method="post" action="{{ route('profile.update') }}"
                @submit.prevent="submitProfileForm" class="mt-6 space-y-8" enctype="multipart/form-data">
                @csrf
                @method('patch')

                {{-- Foto Profil (VERSI BARU DENGAN KOREKSI) --}}
                <div class="flex flex-col items-center space-y-3">
                    <div class="relative group w-24 h-24">
                        <img :src="imageUrl || defaultAvatar" alt="Profile Picture"
                            class="w-24 h-24 rounded-full object-cover border-2 border-gray-300">

                        {{-- Overlay yang muncul saat hover --}}
                        <div
                            class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">

                            {{-- Tombol Ubah Foto (di dalam bingkai) --}}
                            <label for="image-upload-input" class="cursor-pointer text-white p-2" title="Ubah Foto">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <input type="file" name="image" id="image-upload-input" class="sr-only"
                                    @change="handleFileChange" accept="image/*">
                            </label>

                            {{-- Tombol Lihat Foto (di dalam bingkai) --}}
                            <button type="button" x-show="imageUrl"
                                @click.prevent="window.dispatchEvent(new CustomEvent('open-media-viewer', { detail: { url: imageUrl, type: 'image' } }))"
                                class="cursor-pointer text-white p-2" title="Lihat Foto">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Tombol Hapus Foto (di luar bingkai) --}}
                    @if (auth()->user()->image)
                        <button type="button" @click="confirmDeleteImage"
                            class="inline-flex items-center text-xs bg-red-50 text-red-600 hover:bg-red-100 font-semibold py-1 px-3 rounded-full transition-colors">
                            <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            Hapus Foto
                        </button>
                    @endif
                    <x-input-error class="mt-1" :messages="$errors->get('image')" />
                </div>

                {{-- Input Fields Floating Label (LENGKAP) --}}
                <div class="relative">
                    <input type="text" name="name" id="name"
                        class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " value="{{ old('name', $user->name) }}" required />
                    <label for="name"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Nama</label>
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>
                <div class="relative">
                    <input type="text" name="username"
                        @input="$event.target.value = $event.target.value.toLowerCase().replace(/[^a-z0-9_]/g, '')"
                        id="username"
                        class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " value="{{ old('username', $user->username) }}" required />
                    <label for="username"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Username</label>
                    <x-input-error class="mt-2" :messages="$errors->get('username')" />
                </div>
                <div class="relative">
                    <input type="email" name="email" id="email"
                        class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " value="{{ old('email', $user->email) }}" required />
                    <label for="email"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Email</label>
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>
                <div class="relative">
                    <input type="tel" name="phone_number" id="phone_number"
                        class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " value="{{ old('phone_number', $user->phone_number) }}" />
                    <label for="phone_number"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Nomor
                        Telepon (62...)</label>
                    <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" :disabled="isSubmitting"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                        <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'">Simpan Perubahan</span>
                    </button>
                </div>
            </form>
            <form x-ref="deleteImageForm" action="{{ route('profile.image.destroy') }}" method="POST" class="hidden">
                @csrf @method('delete')</form>
        </div>

        {{-- BAGIAN 2: FORM UPDATE PASSWORD (LENGKAP) --}}
        <div class="p-6 bg-white rounded-lg shadow-sm border" x-data="passwordForm()">
            <header>
                <h2 class="text-lg font-medium text-gray-900">Perbarui Password</h2>
                <p class="mt-1 text-sm text-gray-600">Pastikan akun Anda menggunakan password yang panjang dan acak agar
                    tetap aman.</p>
            </header>

            @if (session('status') === 'password-updated')
                <div class="mt-4 p-3 bg-green-100 text-green-700 rounded-md text-sm" x-data="{ show: true }"
                    x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)">
                    Password berhasil diperbarui.
                </div>
            @endif

            <form method="post" action="{{ route('password.update') }}" @submit="isSavingPassword = true"
                class="mt-6 space-y-8">
                @csrf
                @method('put')

                <div class="relative">
                    <input :type="showCurrent ? 'text' : 'password'" name="current_password" id="current_password"
                        @input="cleanInput($event)"
                        class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " autocomplete="current-password" />
                    <label for="current_password"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Password
                        Saat Ini</label>
                    <button type="button" @click="showCurrent = !showCurrent"
                        class="absolute top-0 right-0 p-3.5 text-gray-500 hover:text-gray-700">
                        <svg x-show="!showCurrent" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showCurrent" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .946-3.11 3.564-5.394 6.837-5.965M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.593 4.593l-1.414 1.414m3.536 3.536l-1.768 1.768M4.407 4.593l1.414 1.414M2.75 8.25L4.5 10.5" />
                        </svg>
                    </button>
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                </div>

                <div class="relative">
                    <input :type="showNew ? 'text' : 'password'" name="password" id="password"
                        @input="cleanInput($event)" x-model.debounce.200ms="newPassword"
                        class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " autocomplete="new-password" />
                    <label for="password"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Password
                        Baru</label>
                    <button type="button" @click="showNew = !showNew"
                        class="absolute top-0 right-0 p-3.5 text-gray-500 hover:text-gray-700">
                        <svg x-show="!showNew" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .946-3.11 3.564-5.394 6.837-5.965M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.593 4.593l-1.414 1.414m3.536 3.536l-1.768 1.768M4.407 4.593l1.414 1.414M2.75 8.25L4.5 10.5" />
                        </svg>
                    </button>
                    <div class="mt-2" x-show="newPassword.length > 0" x-transition>
                        <div class="h-1.5 w-full bg-gray-200 rounded-full">
                            <div class="h-1.5 rounded-full transition-all" :class="strength.class"
                                :style="`width: ${strength.width}%`"></div>
                        </div>
                        <p class="text-xs mt-1" :class="strength.textColor" x-text="strength.text"></p>
                    </div>
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                </div>

                <div class="relative">
                    <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation"
                        id="password_confirmation" x-model="confirmPassword" @input="cleanInput($event)"
                        class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 appearance-none focus:outline-none focus:ring-0 peer transition-colors"
                        :class="confirmPassword && newPassword !== confirmPassword ? 'border-red-500' : (confirmPassword &&
                            newPassword === confirmPassword && newPassword.length > 0 ? 'border-green-500' :
                            'border-gray-300')"
                        placeholder=" " autocomplete="new-password" />
                    <label for="password_confirmation"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Konfirmasi
                        Password Baru</label>
                    <button type="button" @click="showConfirm = !showConfirm"
                        class="absolute top-0 right-0 p-3.5 text-gray-500 hover:text-gray-700">
                        <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .946-3.11 3.564-5.394 6.837-5.965M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.593 4.593l-1.414 1.414m3.536 3.536l-1.768 1.768M4.407 4.593l1.414 1.414M2.75 8.25L4.5 10.5" />
                        </svg>
                    </button>
                    <p x-show="confirmPassword && newPassword !== confirmPassword" x-transition
                        class="text-xs text-red-500 mt-1">Password tidak cocok.</p>
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <button type="submit" :disabled="isSavingPassword"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                        <svg x-show="isSavingPassword" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="isSavingPassword ? 'Menyimpan...' : 'Simpan Password'">Simpan Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function profileForm(initialImageUrl, defaultName) {
                return {
                    imageUrl: initialImageUrl,
                    defaultAvatar: `https://ui-avatars.com/api/?name=${defaultName}&background=EBF4FF&color=76A9FA`,
                    isSubmitting: false,
                    handleFileChange(event) {
                        if (event.target.files.length > 0) {
                            this.imageUrl = URL.createObjectURL(event.target.files[0]);
                        }
                    },
                    confirmDeleteImage() {
                        Swal.fire({
                            title: 'Hapus Foto Profil?',
                            text: "Tindakan ini tidak dapat diurungkan!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Ya, Hapus Saja!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.$refs.deleteImageForm.submit();
                            }
                        });
                    },
                    submitProfileForm() {
                        this.isSubmitting = true;
                        // Menggunakan form submit biasa, bukan AJAX, untuk kesederhanaan
                        this.$refs.profileForm.submit();
                    }
                };
            }

            function passwordForm() {
                return {
                    showCurrent: false,
                    showNew: false,
                    showConfirm: false,
                    newPassword: '',
                    confirmPassword: '',
                    isSavingPassword: false,
                    cleanInput(event) {
                        event.target.value = event.target.value.replace(/\s/g, '');
                    },
                    get strength() {
                        let s = 0;
                        if (this.newPassword.length > 7) s++;
                        if (this.newPassword.match(/[A-Z]/)) s++;
                        if (this.newPassword.match(/[0-9]/)) s++;
                        if (this.newPassword.match(/[^A-Za-z0-9]/)) s++;
                        if (s === 0 && this.newPassword.length > 0) s = 1;
                        switch (s) {
                            case 1:
                                return {
                                    width: 25, class: 'bg-red-500', textColor: 'text-red-500', text: 'Sangat Lemah'
                                };
                            case 2:
                                return {
                                    width: 50, class: 'bg-yellow-500', textColor: 'text-yellow-500', text: 'Lemah'
                                };
                            case 3:
                                return {
                                    width: 75, class: 'bg-blue-500', textColor: 'text-blue-500', text: 'Sedang'
                                };
                            case 4:
                                return {
                                    width: 100, class: 'bg-green-500', textColor: 'text-green-500', text: 'Kuat'
                                };
                            default:
                                return {
                                    width: 0, class: 'bg-gray-200', text: ''
                                };
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection
