<x-guest-layout>
    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="flex justify-center mb-6">
        {{-- ## KODE LOGO BARU DENGAN EFEK ORBIT ## --}}
        <a href="#" class="logo-orbit-container">

            {{-- Elemen ini yang akan menjadi cahaya berputar --}}
            <div class="logo-orbit-gradient"></div>

            {{-- Kontainer untuk logo Anda --}}
            <div class="logo-orbit-content">
                <img src="{{ asset('logo.png') }}" alt="Logo Aplikasi" class="logo-orbit-image">
            </div>
        </a>
    </div>

    <div x-data="{ showPassword: false, isSubmitting: false }">
        <h1 class="auth-title text-center">Silahkan Login!</h1>
        <p class="auth-subtitle text-center">Selamat Datang di Aplikasi Pengaduan Parkir.</p>

        <form method="POST" action="{{ route('login') }}" @submit="isSubmitting = true">
            @csrf

            {{-- Username --}}
            <div class="relative">
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                    @input="$event.target.value = $event.target.value.toLowerCase().replace(/[^a-z0-9_]/g, '')"
                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" " />

                <label for="username"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">
                    Username
                </label>

                {{-- <x-input-error :messages="$errors->get('username')" class="mt-2" /> --}}
            </div>

            {{-- Password --}}
            <div class="mt-6 relative">
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                    autocomplete="current-password"
                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" " />
                <label for="password"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">
                    Password
                </label>
                {{-- Tombol Show/Hide Password --}}
                <button type="button" @click="showPassword = !showPassword"
                    class="absolute top-0 right-0 p-3.5 text-gray-500 hover:text-gray-700">
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 .946-3.11 3.564-5.394 6.837-5.965M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.593 4.593l-1.414 1.414m3.536 3.536l-1.768 1.768M4.407 4.593l1.414 1.414M2.75 8.25L4.5 10.5" />
                    </svg>
                </button>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            {{-- Remember Me & Forgot Password --}}
            <div class="flex items-center justify-between mt-6">
                {{-- Switch Button untuk Remember Me --}}
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <span class="relative">
                        <input id="remember_me" type="checkbox" name="remember" class="sr-only peer">
                        <div
                            class="w-10 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                        </div>
                    </span>
                    <span class="ml-3 text-sm text-gray-600">{{ __('Ingat Saya') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            {{-- Link ke Register --}}
            <div class="text-center mt-6">
                <p class="text-sm text-gray-600">
                    Belum mempunyai akun?
                    <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:underline">
                        Register disini
                    </a>
                </p>
            </div>

            <div class="flex items-center justify-end mt-4">
                <button type="submit" :disabled="isSubmitting"
                    class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                    <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span x-text="isSubmitting ? 'MEMPROSES...' : 'LOG IN'"></span>
                </button>
            </div>
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">
                    Klik disini untuk menuju halaman
                    <a href="{{ route('home') }}" class="font-medium text-blue-600 hover:underline">
                        Dashboard
                    </a>
                </p>
            </div>
        </form>
    </div>

    @if ($errors->any() || session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: '{{ session('success') }}',
                    });
                @elseif ($errors->any())
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal',
                        html: 'Username atau password yang Anda masukkan salah.',
                    });
                @endif
            });
        </script>
    @endif
</x-guest-layout>
