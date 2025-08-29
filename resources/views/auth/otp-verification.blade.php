<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 px-4">
        {{-- ... (Logo & Subtitle) ... --}}
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg"
            x-data="otpForm">

            <div class="text-center">
                <h2 class="text-2xl font-bold">Verifikasi Akun</h2>
                <p class="text-sm text-gray-600 mt-2">
                    Kami telah mengirimkan 6 digit kode OTP ke nomor WhatsApp Anda.
                </p>
            </div>

            <form method="POST" action="{{ route('otp.verify') }}" class="mt-8">
                @csrf
                <input type="hidden" name="username" value="{{ $user->username }}">

                {{-- OTP Input Boxes --}}
                <div class="flex justify-center space-x-2" @paste="handlePaste">
                    <template x-for="(input, index) in otp" :key="index">
                        <input type="text" maxlength="1" :id="'otp-' + (index + 1)" x-model="otp[index]"
                            @input="handleInput(index, $event)" @keydown.backspace="handleBackspace(index, $event)"
                            class="w-12 h-14 text-center text-2xl font-bold border-2 rounded-md focus:border-blue-500 focus:ring-blue-500">
                    </template>
                </div>
                <input type="hidden" name="otp" :value="otp.join('')">
                <x-input-error :messages="$errors->get('otp')" class="mt-2 text-center" />

                <div class="mt-6">
                    <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-3 ...">
                        VERIFIKASI
                    </button>
                </div>
            </form>

            <div class="text-center mt-4 text-sm">
                <span x-show="resendCooldown > 0" class="text-gray-500">Kirim ulang kode dalam <span
                        x-text="resendCooldown"></span> detik</span>
                <a href="#" @click.prevent="resendOtp" x-show="resendCooldown === 0"
                    class="font-medium text-blue-600 hover:underline">Kirim Ulang Kode OTP</a>
            </div>
        </div>
    </div>
    <script>
        function otpForm() {
            return {
                otp: Array(6).fill(''),
                resendCooldown: 30,
                init() {
                    this.startCooldown();
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
                    // Hanya izinkan angka
                    this.otp[index] = event.target.value.replace(/[^0-9]/g, '');
                    if (this.otp[index] && index < 5) {
                        document.getElementById(`otp-${index + 2}`).focus();
                    }
                },
                handleBackspace(index, event) {
                    if (!this.otp[index] && index > 0) {
                        document.getElementById(`otp-${index}`).focus();
                    }
                },
                handlePaste(event) {
                    const paste = (event.clipboardData || window.clipboardData).getData('text').slice(0, 6);
                    if (/^[0-9]{6}$/.test(paste)) {
                        this.otp = paste.split('');
                        // Mungkin perlu submit otomatis di sini
                    }
                },
                resendOtp() {
                    // TODO: Buat route dan logic untuk kirim ulang OTP
                    // window.location.href = '/otp/resend/{{ $user->username }}';
                    alert('Logika kirim ulang OTP belum dibuat.');
                    this.resendCooldown = 30;
                    this.startCooldown();
                }
            }
        }
    </script>
</x-guest-layout>
