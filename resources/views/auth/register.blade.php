<x-guest-layout>
    <div x-data="registerForm">
        <h1 class="auth-title text-center">Silahkan Daftar!</h1>
        <p class="auth-subtitle text-center">Selamat Datang di Aplikasi Pengaduan Parkir.</p>

        <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" @submit="isSubmitting = true"
            class="space-y-6">
            @csrf

            {{-- Foto Profil (Opsional) --}}
            <div class="flex flex-col items-center space-y-2">
                <div class="w-24 h-24 rounded-full border-2 border-dashed flex items-center justify-center"
                    :class="imageUrl ? 'border-blue-500 p-1' : 'border-gray-300'">
                    <img x-show="imageUrl" :src="imageUrl" class="w-full h-full object-cover rounded-full">
                    <span x-show="!imageUrl" class="text-xs text-gray-400 text-center px-2">
                        Foto Profil (Opsional)
                    </span>
                </div>
                <label for="image-upload-input"
                    class="cursor-pointer text-xs font-semibold text-blue-600 hover:underline">Pilih Foto</label>
                <input type="file" name="image" id="image-upload-input" class="sr-only"
                    @change="imageUrl = URL.createObjectURL($event.target.files[0])" accept="image/*">
            </div>

            {{-- Nama Lengkap --}}
            <div class="relative">
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent
                              rounded-lg border-1 border-gray-300 appearance-none
                              focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" " />
                <label for="name"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75
                              top-2 z-10 origin-[0] bg-white px-2
                              peer-focus:text-blue-600 peer-placeholder-shown:scale-100
                              peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2
                              peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">
                    Nama Lengkap
                </label>
            </div>

            {{-- Username --}}
            <div class="relative">
                <input id="username" type="text" name="username" value="{{ old('username') }}" required
                    @input="$event.target.value = $event.target.value.toLowerCase().replace(/[^a-z0-9_]/g, '')"
                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent
                              rounded-lg border-1 border-gray-300 appearance-none
                              focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" " />
                <label for="username"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75
                              top-2 z-10 origin-[0] bg-white px-2 peer-focus:text-blue-600
                              peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2
                              peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75
                              peer-focus:-translate-y-4 start-1">
                    Username
                </label>
            </div>

            {{-- Email --}}
            <div class="relative">
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent
                              rounded-lg border-1 border-gray-300 appearance-none
                              focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" " />
                <label for="email"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75
                              top-2 z-10 origin-[0] bg-white px-2 peer-focus:text-blue-600
                              peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2
                              peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75
                              peer-focus:-translate-y-4 start-1">
                    Alamat Email
                </label>
            </div>

            {{-- Nomor HP --}}
            <div class="relative">
                <input id="phone_number" type="tel" name="phone_number" value="{{ old('phone_number') }}" required
                    @input="formatPhoneNumber($event)"
                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent
                              rounded-lg border-1 border-gray-300 appearance-none
                              focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" " />
                <label for="phone_number"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75
                              top-2 z-10 origin-[0] bg-white px-2 peer-focus:text-blue-600
                              peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2
                              peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75
                              peer-focus:-translate-y-4 start-1">
                    Nomor WhatsApp (62…)
                </label>
            </div>

            {{-- Password --}}
            <div class="relative">
                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                    autocomplete="new-password" @input="cleanInput($event)" x-model.debounce.200ms="newPassword"
                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent
                              rounded-lg border-1 border-gray-300 appearance-none
                              focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                    placeholder=" " />
                <label for="password"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75
                              top-2 z-10 origin-[0] bg-white px-2 peer-focus:text-blue-600
                              peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2
                              peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75
                              peer-focus:-translate-y-4 start-1">
                    Password
                </label>
                <button type="button" @click="showPassword = !showPassword"
                    class="absolute top-0 right-0 p-3.5 text-gray-500 hover:text-gray-700">
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274
                                 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7
                                 .946-3.11 3.564-5.394 6.837-5.965M15 12a3 3 0
                                 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.593 4.593l-1.414 1.414m3.536 3.536l-1.768
                                 1.768M4.407 4.593l1.414 1.414M2.75 8.25L4.5
                                 10.5" />
                    </svg>
                </button>
                {{-- Password Strength Meter --}}
                <div class="mt-2" x-show="newPassword.length > 0" x-transition>
                    <div class="h-1.5 w-full bg-gray-200 rounded-full">
                        <div class="h-1.5 rounded-full transition-all" :class="strength.class"
                            :style="`width: ${strength.width}%`"></div>
                    </div>
                    <p class="text-xs mt-1" :class="strength.textColor" x-text="strength.text"></p>
                </div>
            </div>

            {{-- Konfirmasi Password --}}
            <div class="relative">
                <input id="password_confirmation" :type="showConfirm ? 'text' : 'password'"
                    name="password_confirmation" required autocomplete="new-password" @input="cleanInput($event)"
                    x-model="confirmPassword"
                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg
                              border-1 appearance-none focus:outline-none focus:ring-0 peer transition-colors"
                    :class="confirmPassword && newPassword !== confirmPassword ? 'border-red-500' :
                        (confirmPassword && newPassword === confirmPassword && newPassword.length > 0 ?
                            'border-green-500' : 'border-gray-300')"
                    placeholder=" " />
                <label for="password_confirmation"
                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75
                              top-2 z-10 origin-[0] bg-white px-2 peer-focus:text-blue-600
                              peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2
                              peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75
                              peer-focus:-translate-y-4 start-1">
                    Konfirmasi Password
                </label>
                <button type="button" @click="showConfirm = !showConfirm"
                    class="absolute top-0 right-0 p-3.5 text-gray-500 hover:text-gray-700">
                    <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478
                                 0 8.268 2.943 9.542 7-1.274 4.057-5.064
                                 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" style="display:none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478
                                 0-8.268-2.943-9.542-7 .946-3.11 3.564-5.394
                                 6.837-5.965M15 12a3 3 0
                                 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.593 4.593l-1.414 1.414m3.536 3.536l-1.768
                                 1.768M4.407 4.593l1.414 1.414M2.75 8.25L4.5
                                 10.5" />
                    </svg>
                </button>
                <p x-show="confirmPassword && newPassword !== confirmPassword" x-transition
                    class="text-xs text-red-500 mt-1">Password tidak cocok.</p>
            </div>

            {{-- Tombol Submit --}}
            <div class="mt-8">
                <button type="submit" :disabled="isSubmitting"
                    class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600
                               border border-transparent rounded-md font-semibold text-xs text-white uppercase
                               tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900
                               focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                               transition ease-in-out duration-150 disabled:opacity-50">
                    <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373
                                 0 12h4zm2 5.291A7.962 7.962 0
                                 014 12H0c0 3.042 1.135 5.824
                                 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="isSubmitting ? 'MEMPROSES...' : 'REGISTER'"></span>
                </button>
            </div>

            {{-- Link ke Login --}}
            <div class="text-center mt-6">
                <p class="text-sm text-gray-600">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:underline">
                        Login disini
                    </a>
                </p>
            </div>
        </form>
    </div>

    <script>
        function registerForm() {
            return {
                imageUrl: '',
                isSubmitting: false,
                showPassword: false,
                showConfirm: false,
                newPassword: '',
                confirmPassword: '',

                cleanInput(event) {
                    event.target.value = event.target.value.replace(/\s/g, '');
                },

                formatPhoneNumber(event) {
                    let value = event.target.value.replace(/[^0-9]/g, '');
                    if (value.startsWith('0')) {
                        value = '62' + value.substring(1);
                    } else if (value.length > 0 && !value.startsWith('62')) {
                        value = '62' + value;
                    }
                    event.target.value = value;
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
