<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Laporan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">

                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Seluruh Laporan Sistem</h3>
                        <p class="text-sm text-gray-500">Lihat, filter, dan kelola semua laporan yang masuk ke dalam sistem.</p>
                    </div>

                    {{-- TABS FILTER STATUS --}}
                    <div class="border-b border-gray-200 overflow-x-auto">
                        <nav class="-mb-px flex space-x-6 whitespace-nowrap" aria-label="Tabs">
                            @php
                                $tabs = ['all' => 'Semua', 'pending' => 'Pending', 'verified' => 'Terverifikasi', 'in_progress' => 'Dikerjakan', 'completed' => 'Selesai', 'rejected' => 'Ditolak'];
                            @endphp
                            @foreach ($tabs as $statusCode => $label)
                                <a href="#" wire:click.prevent="$set('status', '{{ $statusCode }}')"
                                   class="{{ $status == $statusCode ? 'border-dishub-blue-500 text-dishub-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}
                                          py-4 px-1 border-b-2 font-medium text-sm">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </nav>
                    </div>

                    {{-- PENCARIAN --}}
                    <div class="relative">
                        <input wire:model.live.debounce.350ms="search" type="text"
                               placeholder="Cari berdasarkan kode, judul, atau nama pelapor..."
                               class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm pl-10">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    {{-- GRID KARTU LAPORAN --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @forelse ($reports as $report)
                            <div class="bg-white rounded-lg shadow-md border border-gray-200 flex flex-col overflow-hidden group">
                                <a href="{{ route('public.laporan.show', $report) }}" class="block relative">
                                    <img class="h-40 w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                         src="{{ $report->images->first() ? ($report->images->first()->thumbnail_path ? Storage::url($report->images->first()->thumbnail_path) : Storage::url($report->images->first()->file_path)) : 'https://via.placeholder.com/400x300.png/EBF4FF/76A9FA?text=No+Image' }}"
                                         alt="Dokumentasi Laporan">
                                    @php
                                        $statusClasses = [ 'verified' => 'bg-blue-500', 'in_progress' => 'bg-orange-500', 'completed' => 'bg-green-500', 'rejected' => 'bg-red-500', 'pending' => 'bg-yellow-500' ];
                                    @endphp
                                    <span class="absolute top-2 right-2 text-white text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusClasses[$report->status] ?? 'bg-gray-500' }} capitalize">
                                        {{ str_replace('_', ' ', $report->status) }}
                                    </span>
                                </a>
                                <div class="p-4 flex flex-col flex-grow">
                                    <p class="text-xs text-gray-500">#{{ $report->report_code }}</p>
                                    <h4 class="font-bold text-gray-800 leading-tight truncate mt-1">{{ $report->title }}</h4>
                                    <p class="text-sm text-gray-600 mt-1">oleh {{ $report->resident->name }}</p>
                                    <div class="mt-auto pt-4 flex justify-between items-center">
                                        <p class="text-xs text-gray-400">{{ $report->created_at->diffForHumans() }}</p>
                                        <div class="flex items-center gap-2">
                                           <a href="{{ route('super-admin.users.show', $report->resident) }}" title="Lihat Profil Pelapor {{ $report->resident->name }}">
    <img class="h-6 w-6 rounded-full object-cover transition-transform duration-200 hover:scale-110"
         src="{{ $report->resident->image ? Storage::url($report->resident->image) : 'https://ui-avatars.com/api/?name=' . urlencode($report->resident->name) . '&background=EBF4FF&color=1E40AF&size=64&bold=true' }}"
         alt="Avatar {{ $report->resident->name }}">
</a>
                                            @can('delete-reports')
                                            <button type="button" wire:click="$dispatch('confirm-delete-report', { reportId: {{ $report->id }}, reportCode: '{{ $report->report_code }}' })" class="text-red-400 hover:text-red-600" title="Hapus Laporan">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            </button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="sm:col-span-2 lg:col-span-4 text-center py-10 px-4 text-slate-500">
                                <p>Tidak ada laporan yang cocok dengan filter saat ini.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Tombol "Load More" --}}
                    @if ($hasMorePages)
                        <div class="mt-8 text-center">
                            <button wire:click="loadMore" wire:loading.attr="disabled" class="bg-white text-gray-700 font-semibold py-2 px-6 border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 disabled:opacity-50">
                                <span wire:loading.remove wire:target="loadMore">Muat Lebih Banyak</span>
                                <span wire:loading wire:target="loadMore">Memuat...</span>
                            </button>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        Livewire.on('confirm-delete-report', (event) => {
            Swal.fire({
                title: 'Anda Yakin?',
                html: `Anda akan menghapus permanen laporan <b>#${event.reportCode}</b> dan semua data terkaitnya (gambar, riwayat, tindak lanjut). Aksi ini tidak bisa dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus Permanen!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('delete-report', { reportId: event.reportId });
                }
            })
        });
        Livewire.on('report-deleted-success', (event) => {
             Swal.fire('Berhasil!', event.message, 'success');
        });
        Livewire.on('delete-failed', (event) => {
             Swal.fire('Gagal!', event.message, 'error');
        });
    </script>
    @endpush
</div>
