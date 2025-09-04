<div>
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
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            {{-- Input Pencarian LIVE --}}
                            <div class="relative w-full sm:w-80">
                                <input wire:model.live.debounce.350ms="search" type="text"
                                    placeholder="Cari nama, email, atau username..."
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm pl-10">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            {{-- Tombol Tambah Pengguna --}}
                            <a href="{{ route('super-admin.users.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-dishub-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dishub-blue-700 active:bg-dishub-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 whitespace-nowrap">
                                Tambah
                            </a>
                        </div>
                    </div>

                    {{-- Tabel Pengguna --}}
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full bg-white">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-xs text-slate-600">Nama
                                        & Kontak</th>
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
                                                    @if ($user->phone_number)
                                                        <p class="text-xs text-slate-500">+{{ $user->phone_number }}</p>
                                                    @endif
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
                                            <div class="flex items-center justify-center gap-3">
                                                {{-- ICON BARU: DETAIL --}}
                                                <a href="#" class="text-slate-500 hover:text-slate-800"
                                                    title="Lihat Detail">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                        <path fill-rule="evenodd"
                                                            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </a>
                                                {{-- ICON BARU: EDIT --}}
                                                <a href="{{ route('super-admin.users.edit', $user) }}"
                                                    class="text-yellow-500 hover:text-yellow-700" title="Edit Pengguna">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                                        viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                </a>
                                                {{-- ICON BARU: HAPUS --}}
                                                <button type="button"
                                                    wire:click="$dispatch('confirm-delete', { userId: {{ $user->id }}, userName: '{{ $user->name }}' })"
                                                    class="text-red-500 hover:text-red-700" title="Hapus Pengguna">
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
                                            @if (empty($search))
                                                <p>Tidak ada data pengguna yang ditemukan.</p>
                                            @else
                                                <p>Tidak ada pengguna yang cocok dengan kata kunci <span
                                                        class="font-semibold">"{{ $search }}"</span>.</p>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginasi Livewire --}}
                    @if ($users->hasPages())
                        <div class="mt-6">
                            {{ $users->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            // Pastikan listener ini hanya didaftarkan sekali
            // Kita gunakan 'DOMContentLoaded' untuk memastikan semua elemen siap
            document.addEventListener('DOMContentLoaded', function() {

                // Dengarkan event 'confirm-delete' dari tombol Livewire
                Livewire.on('confirm-delete', (event) => {
                    console.log('Event "confirm-delete" diterima oleh JavaScript:', event); // Debug #1

                    Swal.fire({
                        title: 'Anda Yakin?',
                        html: `Anda akan menghapus pengguna: <br><b>${event.userName}</b>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        // Cek apakah tombol "Ya, Hapus!" yang diklik
                        if (result.isConfirmed) {
                            console.log(
                                'Dikonfirmasi! Mengirim event "delete-user" ke server...'); // Debug #2

                            // Kirim event 'delete-user' kembali ke komponen Livewire di backend
                            Livewire.dispatch('delete-user', {
                                userId: event.userId
                            });
                        } else {
                            console.log('Penghapusan dibatalkan oleh pengguna.'); // Debug #3
                        }
                    });
                });

                // Tambahan: Listener untuk pesan sukses/error dari server setelah aksi
                Livewire.on('user-deleted', (event) => {
                    Swal.fire(
                        'Berhasil!',
                        event.message,
                        'success'
                    );
                });

                Livewire.on('delete-failed', (event) => {
                    Swal.fire(
                        'Gagal!',
                        event.message,
                        'error'
                    );
                });
            });
        </script>
    @endpush
</div>
