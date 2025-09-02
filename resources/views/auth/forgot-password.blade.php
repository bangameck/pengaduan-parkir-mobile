<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 px-4">
        {{-- Logo dan Subtitle --}}
        <div class="mb-6 text-center">
            <a href="/">
                <img src="{{ asset('logo-parkir.png') }}" alt="Logo ParkirPKU" class="w-24 h-24 mx-auto">
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg"
            x-data="{ isSubmitting: false }">

            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-800">Lupa Password?</h2>
                <p class="text-sm text-gray-600 mt-2">
                    Jangan khawatir. Masukkan nomor WhatsApp yang terdaftar dan kami akan mengirimkan kode OTP untuk
                    mereset password Anda.
                </p>
            </div>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.otp.send') }}" @submit="isSubmitting = true">
                @csrf

                <div class="relative">
                    <input id="phone_number" type="tel" name="phone_number" value="{{ old('phone_number') }}"
                        required autofocus
                        class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " />
                    <label for="phone_number"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">
                        Nomor WhatsApp (Contoh: 62812...)
                    </label>
                    <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                </div>

                <div class="mt-8">
                    <button type="submit" :disabled="isSubmitting"
                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 disabled:opacity-50">
                        <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="isSubmitting ? 'MENGIRIM OTP...' : 'KIRIM KODE RESET'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
