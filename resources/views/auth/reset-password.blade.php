<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 px-4">
        <div class="mb-6 text-center">
            <a href="/"><img src="{{ asset('logo-parkir.png') }}" alt="Logo ParkirPKU" class="w-24 h-24 mx-auto"></a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg"
            x-data="passwordForm()">

            <div class="mb-6 text-center">
                <h2 class="text-2xl font-bold text-gray-800">Buat Password Baru</h2>
                <p class="text-sm text-gray-600 mt-2">Masukkan password baru yang kuat untuk akun <strong
                        class="text-gray-900">{{ $user->username }}</strong>.</p>
            </div>

            {{-- <x-auth-validation-errors class="mb-4" :errors="$errors" /> --}}

            {{-- !!! PERUBAHAN DI SINI !!! --}}
            <form method="POST" action="{{ route('password.update.via.otp') }}" @submit="isSubmitting = true"
                class="space-y-6">
                @csrf
                {{-- @method('put') tidak diperlukan lagi karena route menggunakan POST --}}
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="username" value="{{ $user->username }}">

                {{-- Floating Label: Password --}}
                <div class="relative">
                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                        autocomplete="new-password" @input="cleanInput($event)" x-model.debounce.200ms="newPassword"
                        class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                        placeholder=" " />
                    <label for="password"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Password</label>
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute top-0 right-0 p-3.5 text-gray-500 hover:text-gray-700">
                        <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" style="display: none;">
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
                </div>

                {{-- Floating Label: Confirm Password --}}
                <div class="relative">
                    <input id="password_confirmation" :type="showConfirm ? 'text' : 'password'"
                        name="password_confirmation" required autocomplete="new-password" @input="cleanInput($event)"
                        x-model="confirmPassword"
                        class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 appearance-none focus:outline-none focus:ring-0 peer transition-colors"
                        :class="confirmPassword && newPassword !== confirmPassword ? 'border-red-500' : (confirmPassword &&
                            newPassword === confirmPassword && newPassword.length > 0 ? 'border-green-500' :
                            'border-gray-300')"
                        placeholder=" " />
                    <label for="password_confirmation"
                        class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Konfirmasi
                        Password</label>
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
                </div>

                <div class="mt-8">
                    {{-- !!! PERUBAHAN DI SINI !!! --}}
                    <button type="submit"
                        :disabled="isSubmitting || newPassword !== confirmPassword || newPassword.length === 0"
                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="isSubmitting ? 'MENYIMPAN...' : 'RESET PASSWORD'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function passwordForm() {
            return {
                showPassword: false,
                showConfirm: false,
                isSubmitting: false,
                newPassword: '',
                confirmPassword: '',
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
</x-guest-layout>
