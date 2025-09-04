<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Pengguna Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-lg font-bold text-dishub-blue-800">Formulir Pengguna</h3>
                    <p class="mt-1 text-sm text-gray-500 mb-8">Buat akun baru untuk staf atau administrator.</p>

                    {{-- ========================================================== --}}
                    {{-- == AWAL FORM DENGAN ALPINE.JS UNTUK FITUR OTOMATIS == --}}
                    {{-- ========================================================== --}}
                    <form method="POST" action="{{ route('super-admin.users.store') }}" class="space-y-8"
                        x-data="{
                            name: '{{ old('name') }}',
                            username: '{{ old('username') }}',
                            email: '{{ old('email') }}',
                            password: '',
                            copyText: 'Salin',
                            isSubmitting: false,
                            generatePassword() {
                                const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
                                let pass = '';
                                for (let i = 0; i < 8; i++) {
                                    pass += chars.charAt(Math.floor(Math.random() * chars.length));
                                }
                                this.password = pass;
                                this.copyText = 'Salin';
                            },
                            copyToClipboard() {
                                if (!this.password) return;
                                navigator.clipboard.writeText(this.password);
                                this.copyText = 'Disalin!';
                                setTimeout(() => { this.copyText = 'Salin' }, 2000);
                            }
                        }" x-init="$watch('name', value => {
                            username = value.toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
                            if (username) {
                                email = username + '@parkir.pku';
                            } else {
                                email = '';
                            }
                        })" @submit="isSubmitting = true">
                        @csrf

                        <!-- Name -->
                        <div class="relative">
                            <input x-model="name" type="text" id="name" name="name"
                                class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                placeholder=" " required autofocus />
                            <label for="name"
                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Nama
                                Lengkap</label>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Username & Email -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="relative">
                                <input x-model="username"
                                    @input="username = $event.target.value.toLowerCase().replace(/[^a-z0-9_]/g, '')"
                                    type="text" id="username" name="username"
                                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " required />
                                <label for="username"
                                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Username</label>
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>
                            <div class="relative">
                                <input x-model="email" type="email" id="email" name="email"
                                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " required />
                                <label for="email"
                                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Email</label>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Phone Number & Role -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="relative">
                                <input type="tel" id="phone_number" name="phone_number"
                                    value="{{ old('phone_number') }}" maxlength="20" pattern="^62\d*$"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    title="Nomor harus diawali dengan 62 dan hanya berisi angka."
                                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " />
                                <label for="phone_number"
                                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Nomor
                                    Telepon (Opsional, diawali 62)</label>
                                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                            </div>
                            <div>
                                <label for="role_id" class="block mb-2 text-sm font-medium text-gray-700">Role /
                                    Jabatan</label>
                                <select id="role_id" name="role_id"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm"
                                    required>
                                    <option value="" disabled selected>Pilih Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('-', ' ', $role->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block mb-2 text-sm font-medium text-gray-700">Password</label>
                            <div class="relative">
                                <input x-model="password" type="text" id="password_display" readonly
                                    class="block w-full border-gray-300 rounded-md shadow-sm bg-gray-100 text-sm pl-4 pr-32"
                                    placeholder="Klik 'Generate' untuk membuat password">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2 gap-1">
                                    <button @click.prevent="generatePassword()" type="button"
                                        class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-1 px-3 rounded-md">Generate</button>
                                    <button @click.prevent="copyToClipboard()" type="button" x-text="copyText"
                                        class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 font-semibold py-1 px-3 rounded-md"></button>
                                </div>
                            </div>
                            <input type="hidden" name="password" x-bind:value="password">
                            <input type="hidden" name="password_confirmation" x-bind:value="password">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end mt-8 border-t pt-6">
                            <a href="{{ route('super-admin.users.index') }}"
                                class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <button type="submit" x-bind:disabled="isSubmitting"
                                class="inline-flex items-center justify-center px-4 py-2 bg-dishub-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dishub-blue-700 active:bg-dishub-blue-900 focus:outline-none focus:ring-2 focus:ring-dishub-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">

                                <svg x-show="isSubmitting" x-transition
                                    class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>

                                <span x-show="!isSubmitting">Simpan Pengguna</span>
                                <span x-show="isSubmitting">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
