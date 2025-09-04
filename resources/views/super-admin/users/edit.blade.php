<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Pengguna: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    <h3 class="text-lg font-bold text-dishub-blue-800">Formulir Edit Pengguna</h3>
                    <p class="mt-1 text-sm text-gray-500 mb-8">Perbarui detail akun untuk staf atau administrator.</p>

                    <form method="POST" action="{{ route('super-admin.users.update', $user) }}" class="space-y-8"
                        x-data="{
                            showPasswordField: false,
                            passwordVisible: false,
                            isSubmitting: false
                        }" @submit="isSubmitting = true">
                        @csrf
                        @method('PATCH')

                        <div class="relative">
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                                class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                placeholder=" " required autofocus />
                            <label for="name"
                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Nama
                                Lengkap</label>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="relative">
                                {{-- PERBAIKAN 1: Filter input username real-time --}}
                                <input
                                    @input="$event.target.value = $event.target.value.toLowerCase().replace(/[^a-z0-9_]/g, '')"
                                    type="text" id="username" name="username"
                                    value="{{ old('username', $user->username) }}"
                                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " required />
                                <label for="username"
                                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Username</label>
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>
                            <div class="relative">
                                <input type="email" id="email" name="email"
                                    value="{{ old('email', $user->email) }}"
                                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " required />
                                <label for="email"
                                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Email</label>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="relative">
                                {{-- PERBAIKAN 2: Validasi input nomor telepon --}}
                                <input type="tel" id="phone_number" name="phone_number"
                                    value="{{ old('phone_number', $user->phone_number) }}" maxlength="20"
                                    pattern="^62\d*$" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    title="Nomor harus diawali dengan 62 dan hanya berisi angka."
                                    class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " />
                                <label for="phone_number"
                                    class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Nomor
                                    Telepon (Opsional)</label>
                                <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                            </div>
                            <div>
                                <label for="role_id" class="block mb-2 text-sm font-medium text-gray-700">Role /
                                    Jabatan</label>
                                <select id="role_id" name="role_id"
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full text-sm"
                                    required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                            {{ ucwords(str_replace('-', ' ', $role->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="border-t pt-6">
                            <button @click.prevent="showPasswordField = !showPasswordField" type="button"
                                class="text-sm font-semibold text-dishub-blue-700 hover:text-dishub-blue-900">
                                Ubah Password (Opsional)
                            </button>
                            <div x-show="showPasswordField" x-transition class="mt-6 space-y-6">
                                <p class="text-sm text-gray-500">Kosongkan jika Anda tidak ingin mengubah password.</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    {{-- PERBAIKAN 3: Fitur "intip" password & blok spasi --}}
                                    <div>
                                        <div class="relative">
                                            <input :type="passwordVisible ? 'text' : 'password'" @keydown.space.prevent
                                                id="password" name="password"
                                                class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                placeholder=" " />
                                            <label for="password"
                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Password
                                                Baru</label>
                                            <button @click.prevent="passwordVisible = !passwordVisible" type="button"
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                                <svg x-show="!passwordVisible" class="w-5 h-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <svg x-show="passwordVisible" x-cloak class="w-5 h-5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 1.274-4.057 5.064-7 9.542-7 .847 0 1.67.127 2.452.368m4.438 4.438A9.953 9.953 0 0117 12c0 .94-.132 1.85-.369 2.712m-2.121-5.321c.49-.603.84-1.295 1.126-2.032M3.75 4.5L21.25 22.5" />
                                                </svg>
                                            </button>
                                        </div>
                                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                    </div>
                                    <div>
                                        <div class="relative">
                                            <input :type="passwordVisible ? 'text' : 'password'" @keydown.space.prevent
                                                id="password_confirmation" name="password_confirmation"
                                                class="block px-3.5 pb-2.5 pt-4 w-full text-sm text-gray-900 bg-transparent rounded-lg border-1 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                placeholder=" " />
                                            <label for="password_confirmation"
                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-6 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 start-1">Konfirmasi
                                                Password</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 border-t pt-6">
                            <a href="{{ route('super-admin.users.index') }}"
                                class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            {{-- PERBAIKAN 4: Animasi loading pada tombol submit --}}
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

                                <span x-show="!isSubmitting">Perbarui Pengguna</span>
                                <span x-show="isSubmitting">Memperbarui...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
