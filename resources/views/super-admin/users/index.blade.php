<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">

                    {{-- Header dengan Tombol Aksi dan Pencarian --}}
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Daftar Pengguna Sistem</h3>
                            <p class="text-sm text-gray-500">Kelola semua akun yang terdaftar di aplikasi.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            {{-- Tombol Tambah Pengguna Baru --}}
                            <a href="#" {{-- Nanti ke route('super-admin.users.create') --}}
                                class="inline-flex items-center px-4 py-2 bg-dishub-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dishub-blue-700 active:bg-dishub-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 whitespace-nowrap">
                                Tambah Pengguna
                            </a>
                        </div>
                    </div>

                    {{-- Notifikasi Sukses --}}
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    {{-- Tabel Pengguna --}}
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full bg-white">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-xs text-slate-600">Nama
                                        & Email</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-xs text-slate-600">
                                        Username</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-xs text-slate-600">Role
                                    </th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-xs text-slate-600">Tgl.
                                        Bergabung</th>
                                    <th class="text-center py-3 px-4 uppercase font-semibold text-xs text-slate-600">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($users as $user)
                                    <tr class="hover:bg-slate-50 border-b border-slate-200">
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <img class="h-10 w-10 rounded-full object-cover"
                                                    src="{{ $user->image ? Storage::url($user->image) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=EBF4FF&color=76A9FA' }}"
                                                    alt="{{ $user->name }}">
                                                <div>
                                                    <p class="font-medium text-slate-800">{{ $user->name }}</p>
                                                    <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                                    <p class="text-xs text-slate-500">+{{ $user->phone_number }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-slate-600">{{ $user->username }}</td>
                                        <td class="py-3 px-4">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 capitalize">
                                                {{ $user->role->name }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-slate-600">
                                            {{ $user->created_at->isoFormat('D MMM YYYY') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                {{-- Tombol Edit --}}
                                                <a href="#" {{-- Nanti ke route('super-admin.users.edit', $user) --}}
                                                    class="text-yellow-600 hover:text-yellow-900" title="Edit Pengguna">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                        <path fill-rule="evenodd"
                                                            d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                {{-- Tombol Hapus --}}
                                                <button type="button" class="text-red-600 hover:text-red-900"
                                                    title="Hapus Pengguna">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-10 px-4 text-slate-500">
                                            <p>Tidak ada data pengguna yang ditemukan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginasi --}}
                    @if ($users->hasPages())
                        <div class="mt-6">
                            {{ $users->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
