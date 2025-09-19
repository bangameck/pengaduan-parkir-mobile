<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 px-4">
        {{-- Logo --}}
        <div class="mb-6 text-center">
            <a href="/">
                <img src="{{ asset('logo-parkir.png') }}" alt="Logo ParkirPKU" class="w-24 h-24 mx-auto">
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg"
            x-data="otpForm('{{ route('otp.resend', $user->username) }}')">

            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-800">Verifikasi Kode OTP</h2>
                <p class="text-sm text-gray-600 mt-2">
                    Kami telah mengirimkan 6 digit kode OTP ke nomor WhatsApp
                    <br><strong>{{ Str::mask($user->phone_number, '*', 4, 4) }}</strong>.
                </p>
            </div>

            @if (session('success'))
                <div class="mt-4 p-3 bg-green-100 text-green-700 rounded-md text-sm text-center">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.otp.confirm') }}" class="mt-8" x-ref="otpForm">
                @csrf

                {{-- === INI DIA KUNCINYA, BRODY! === --}}
                {{-- Kita selipkan username di sini agar controller tahu OTP ini milik siapa --}}
                <input type="hidden" name="username" value="{{ $user->username }}">
                {{-- =================================== --}}

                {{-- OTP Input Boxes (Versi Compact) --}}
                <div class="flex justify-center space-x-2" @paste.prevent="handlePaste">
                    <template x-for="(char, index) in otp" :key="index">
                        <input type="text" maxlength="1" :id="'otp-' + (index + 1)" x-model="otp[index]"
                            @input="handleInput(index, $event)" @keydown.backspace="handleBackspace(index, $event)"
                            class="w-10 h-12 sm:w-12 sm:h-14 text-center text-xl sm:text-2xl font-bold border-2 rounded-md focus:border-blue-500 focus:ring-blue-500 transition-colors duration-200">
                    </template>
                </div>
                <input type="hidden" name="otp" :value="otp.join('')">
                <x-input-error :messages="$errors->get('otp')" class="mt-2 text-center" />

                <div class="mt-6">
                    <button type="submit" :disabled="isSubmitting || otp.join('').length < 6"
                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 disabled:opacity-50 transition">
                        <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="isSubmitting ? 'MEMVERIFIKASI...' : 'VERIFIKASI'"></span>
                    </button>
                </div>
            </form>

            <div class="text-center mt-6 text-sm">
                <span x-show="resendCooldown > 0" class="text-gray-500">
                    Kirim ulang kode dalam <strong x-text="resendCooldown"></strong> detik
                </span>
                <a href="#" @click.prevent="resendOtp" x-show="resendCooldown === 0"
                    class="font-medium text-blue-600 hover:underline" x-transition>
                    Kirim Ulang Kode OTP
                </a>
            </div>
        </div>
    </div>

    <script>
        function otpForm(resendUrl) {
            return {
                otp: Array(6).fill(''),
                isSubmitting: false,
                resendCooldown: 60,
                init() {
                    this.startCooldown();
                    this.$nextTick(() => document.getElementById('otp-1').focus());
                },
                startCooldown() {
                    const timer = setInterval(() => {
                        if (this.resendCooldown > 0) this.resendCooldown--;
                        else clearInterval(timer);
                    }, 1000);
                },
                handleInput(index, event) {
                    let value = event.target.value.replace(/[^0-9]/g, '');
                    this.otp[index] = value;
                    if (value && index < 5) {
                        this.$nextTick(() => document.getElementById(`otp-${index + 2}`).focus());
                    }
                    if (this.otp.every(char => char !== '')) {
                        this.isSubmitting = true;
                        this.$refs.otpForm.submit();
                    }
                },
                handleBackspace(index, event) {
                    if (this.otp[index] === '' && index > 0) {
                        this.$nextTick(() => document.getElementById(`otp-${index}`).focus());
                    }
                },
                handlePaste(event) {
                    const paste = (event.clipboardData || window.clipboardData).getData('text').slice(0, 6);
                    if (/^[0-9]{6}$/.test(paste)) {
                        this.otp = paste.split('');
                        this.isSubmitting = true;
                        this.$refs.otpForm.submit();
                    }
                },
                resendOtp() {
                    window.location.href = resendUrl;
                }
            }
        }
    </script>
</x-guest-layout>
