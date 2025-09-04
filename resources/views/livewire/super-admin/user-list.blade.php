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
                            <a href="{{ route('super-admin.users.create') }}"
                                class="inline-flex items-center px-4 py-2 bg-dishub-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dishub-blue-700 active:bg-dishub-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 whitespace-nowrap">
                                Tambah
                            </a>
                        </div>
                    </div>

                    {{-- Notifikasi Sukses/Error --}}
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    {{-- ======================================================= --}}
                    {{-- == GRID KARTU PENGGUNA (PENGGANTI TABEL) == --}}
                    {{-- ======================================================= --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @forelse ($users as $user)
                            <div
                                class="bg-white rounded-lg shadow-md border border-gray-200 text-center flex flex-col p-6 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
                                {{-- Avatar --}}
                                <img class="h-24 w-24 rounded-full object-cover mx-auto shadow-lg"
                                    src="{{ $user->image ? Storage::url($user->image) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=EBF4FF&color=76A9FA&size=128' }}"
                                    alt="{{ $user->name }}">

                                {{-- Info Pengguna --}}
                                <div class="mt-4 flex-grow">
                                    <h4 class="font-bold text-lg text-gray-800">{{ $user->name }}</h4>
                                    <p class="text-sm text-dishub-blue-700 font-medium capitalize">
                                        {{ str_replace('-', ' ', $user->role->name) }}</p>
                                    <p class="text-xs text-gray-500 mt-2">{{ $user->email }}</p>
                                </div>

                                {{-- Aksi --}}
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex items-center justify-center gap-4">
                                        <a href="{{ route('super-admin.users.show', $user) }}"
                                            class="text-slate-500 hover:text-slate-800" title="Lihat Detail">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd"
                                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        @if ($user->role->name !== 'resident')
                                            <a href="{{ route('super-admin.users.edit', $user) }}"
                                                class="text-yellow-500 hover:text-yellow-700" title="Edit Pengguna">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                </svg>
                                            </a>
                                        @endif
                                        <button type="button"
                                            wire:click="$dispatch('confirm-delete', { userId: {{ $user->id }}, userName: '{{ addslashes($user->name) }}' })"
                                            class="text-red-500 hover:text-red-700" title="Hapus Pengguna">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div
                                class="sm:col-span-2 md:col-span-3 lg:col-span-4 text-center py-10 px-4 text-slate-500">
                                @if (empty($search))
                                    <p>Tidak ada data pengguna yang ditemukan.</p>
                                @else
                                    <p>Tidak ada pengguna yang cocok dengan kata kunci <span
                                            class="font-semibold">"{{ $search }}"</span>.</p>
                                @endif
                            </div>
                        @endforelse
                    </div>

                    {{-- Tombol "Load More" --}}
                    @if ($hasMorePages)
                        <div class="mt-8 text-center">
                            <button wire:click="loadMore" wire:loading.attr="disabled"
                                class="bg-white text-gray-700 font-semibold py-2 px-6 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 disabled:opacity-50">

                                <div wire:loading wire:target="loadMore" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Memuat...</span>
                                </div>

                                <span wire:loading.remove wire:target="loadMore">
                                    Muat Lebih Banyak
                                </span>
                            </button>
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
