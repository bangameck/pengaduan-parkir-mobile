<x-guest-layout>
    <div x-data="otpForm('{{ route('otp.resend', $user->username) }}')">
        {{-- Judul & Subjudul --}}
        <h1 class="auth-title text-center">Verifikasi Akun Anda</h1>
        <p class="auth-subtitle text-center">
            Kami telah mengirimkan 6 digit kode OTP ke nomor WhatsApp<br>
            <strong>{{ Str::mask($user->phone_number, '*', 4, 4) }}</strong>
        </p>

        {{-- Notifikasi Sukses Kirim Ulang --}}
        @if (session('success'))
            <div class="mt-4 p-3 bg-green-100 text-green-700 rounded-md text-sm text-center">
                {{ session('success') }}
            </div>
        @endif

        {{-- Form OTP --}}
        <form method="POST" action="{{ route('otp.verify') }}" class="mt-6" x-ref="otpForm" @submit="isSubmitting = true">
            @csrf
            <input type="hidden" name="username" value="{{ $user->username }}">

            {{-- OTP Input --}}
            <div class="flex justify-center space-x-2 sm:space-x-3" @paste.prevent="handlePaste">
                <template x-for="(char, index) in otp" :key="index">
                    <input type="text" maxlength="1" :id="'otp-' + (index + 1)" x-model="otp[index]"
                        @input="handleInput(index, $event)" @keydown.backspace="handleBackspace(index, $event)"
                        class="w-10 h-12 sm:w-12 sm:h-14 text-center text-xl sm:text-2xl font-bold border-2 rounded-lg focus:border-blue-600 focus:ring-blue-600 transition-colors duration-200" />
                </template>
            </div>
            <input type="hidden" name="otp" :value="otp.join('')">
            <x-input-error :messages="$errors->get('otp')" class="mt-2 text-center" />

            {{-- Tombol Submit --}}
            <div class="mt-6">
                <button type="submit" :disabled="isSubmitting"
                    class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-white hover:bg-blue-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition disabled:opacity-50">
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

        {{-- Resend OTP --}}
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

    {{-- Script Alpine.js --}}
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
                        if (this.resendCooldown > 0) {
                            this.resendCooldown--;
                        } else {
                            clearInterval(timer);
                        }
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
                handleBackspace(index) {
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

    {{-- SweetAlert untuk error/sukses --}}
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
                        title: 'OTP Salah',
                        html: 'Kode OTP yang Anda masukkan tidak valid.',
                    });
                @endif
            });
        </script>
    @endif
</x-guest-layout>
