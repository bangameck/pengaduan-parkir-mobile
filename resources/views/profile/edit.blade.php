@extends('layouts.mobile')

@section('title', 'Edit Profil')

@section('header')
    <x-page-header>Edit Profil</x-page-header>
@endsection

@section('content')
    <div class="p-4 sm:p-6 space-y-6"
         x-data="{
             imageUrl: '{{ auth()->user()->image ? Storage::url(auth()->user()->image) : '' }}',
             handleFileChange(event) {
                 if (event.target.files.length > 0) {
                     this.imageUrl = URL.createObjectURL(event.target.files[0]);
                 }
             },
             showCurrent: false,
             showNew: false,
             showConfirm: false
         }">

        {{-- BAGIAN 1: FORM UPDATE PROFIL --}}
        <div class="p-6 bg-white rounded-lg shadow-sm border">
            <header>
                <h2 class="text-lg font-medium text-gray-900">Informasi Profil</h2>
                <p class="mt-1 text-sm text-gray-600">Perbarui informasi profil dan data diri Anda.</p>
            </header>

            @if (session('status') === 'profile-updated')
                <div class="mt-4 p-3 bg-green-100 text-green-700 rounded-md text-sm" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)">
                    Profil berhasil diperbarui.
                </div>
            @endif

            <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-8" enctype="multipart/form-data">
                @csrf
                @method('patch')

                {{-- Foto Profil --}}
                <div class="flex flex-col items-center space-y-2">
                    <div class="w-24 h-24 rounded-full overflow-hidden border-2" :class="imageUrl ? 'border-blue-500' : 'border-gray-300'">
                        <img :src="imageUrl || 'https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=EBF4FF&color=76A9FA'"
                             alt="Profile Picture" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <label for="image-upload-btn" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-xs leading-4 font-medium text-gray-700 hover:bg-gray-50">
                            <span>Ubah Foto</span>
                            <input type="file" name="image" id="image-upload-btn" class="sr-only" @change="handleFileChange">
                        </label>
                    </div>
                    <x-input-error class="mt-1" :messages="$errors->get('image')" />
                </div>

                {{-- Floating Label: Nama --}}
                <div class="relative">
                    <input type="text" name="name" id="name" class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " value="{{ old('name', $user->name) }}" required />
                    <label for="name" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Nama</label>
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                {{-- Floating Label: Username --}}
                <div class="relative">
                    <input type="text" name="username" id="username" class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " value="{{ old('username', $user->username) }}" required />
                    <label for="username" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Username</label>
                    <x-input-error class="mt-2" :messages="$errors->get('username')" />
                </div>

                {{-- Floating Label: Email --}}
                <div class="relative">
                    <input type="email" name="email" id="email" class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " value="{{ old('email', $user->email) }}" required />
                    <label for="email" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Email</label>
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                {{-- Floating Label: Nomor Telepon --}}
                <div class="relative">
                    <input type="tel" name="phone_number" id="phone_number" class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " value="{{ old('phone_number', $user->phone_number) }}" />
                    <label for="phone_number" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Nomor Telepon (62...)</label>
                    <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                </div>
            </form>
        </div>

        {{-- BAGIAN 2: FORM UPDATE PASSWORD --}}
        <div class="p-6 bg-white rounded-lg shadow-sm border">
            <header>
                <h2 class="text-lg font-medium text-gray-900">Perbarui Password</h2>
                <p class="mt-1 text-sm text-gray-600">Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.</p>
            </header>

             @if (session('status') === 'password-updated')
                <div class="mt-4 p-3 bg-green-100 text-green-700 rounded-md text-sm" x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)">
                    Password berhasil diperbarui.
                </div>
            @endif

            <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-8">
                @csrf
                @method('put')

                {{-- Floating Label: Password Saat Ini --}}
                <div class="relative">
                    <input :type="showCurrent ? 'text' : 'password'" name="current_password" id="current_password" class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " autocomplete="current-password" />
                    <label for="current_password" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Password Saat Ini</label>
                    <button type="button" @click="showCurrent = !showCurrent" class="absolute top-0 right-0 p-3.5 text-gray-500 hover:text-gray-700">
                        <svg x-show="!showCurrent" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="showCurrent" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .946-3.11 3.564-5.394 6.837-5.965M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.593 4.593l-1.414 1.414m3.536 3.536l-1.768 1.768M4.407 4.593l1.414 1.414M2.75 8.25L4.5 10.5" /></svg>
                    </button>
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                </div>

                {{-- Floating Label: Password Baru --}}
                <div class="relative">
                    <input :type="showNew ? 'text' : 'password'" name="password" id="password" class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " autocomplete="new-password" />
                    <label for="password" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Password Baru</label>
                    <button type="button" @click="showNew = !showNew" class="absolute top-0 right-0 p-3.5 text-gray-500 hover:text-gray-700">
                        <svg x-show="!showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="showNew" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .946-3.11 3.564-5.394 6.837-5.965M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.593 4.593l-1.414 1.414m3.536 3.536l-1.768 1.768M4.407 4.593l1.414 1.414M2.75 8.25L4.5 10.5" /></svg>
                    </button>
                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                </div>

                {{-- Floating Label: Konfirmasi Password Baru --}}
                <div class="relative">
                    <input :type="showConfirm ? 'text' : 'password'" name="password_confirmation" id="password_confirmation" class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " autocomplete="new-password" />
                    <label for="password_confirmation" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Konfirmasi Password Baru</label>
                    <button type="button" @click="showConfirm = !showConfirm" class="absolute top-0 right-0 p-3.5 text-gray-500 hover:text-gray-700">
                        <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .946-3.11 3.564-5.394 6.837-5.965M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.593 4.593l-1.414 1.414m3.536 3.536l-1.768 1.768M4.407 4.593l1.414 1.414M2.75 8.25L4.5 10.5" /></svg>
                    </button>
                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                </div>
            </form>
        </div>

    </div>
@endsection
