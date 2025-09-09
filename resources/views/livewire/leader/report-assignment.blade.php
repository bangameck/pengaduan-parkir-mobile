<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verifikasi & Penugasan Tim') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">

                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Antrian Laporan Menunggu Verifikasi</h3>
                        <p class="text-sm text-gray-500">Verifikasi dan tugaskan laporan di bawah ini kepada tim petugas
                            lapangan.</p>
                    </div>

                    <div class="relative">
                        <input wire:model.live.debounce.350ms="search" type="text"
                            placeholder="Cari kode, judul, atau lokasi..."
                            class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm pl-10">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>

                    @if (session()->has('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    @if (session()->has('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    {{-- Daftar Kartu Laporan --}}
                    <div class="space-y-4">
                        @forelse ($reports as $report)
                            <div
                                class="bg-white border border-gray-200 rounded-lg shadow-sm p-4 flex flex-col sm:flex-row items-start gap-4 transition-shadow hover:shadow-md">
                                <img class="w-full sm:w-32 h-32 object-cover rounded-md bg-gray-100 flex-shrink-0"
                                    src="{{ $report->images->first() ? ($report->images->first()->thumbnail_path ? Storage::url($report->images->first()->thumbnail_path) : Storage::url($report->images->first()->file_path)) : 'https://ui-avatars.com/api/?name=N/A&background=EBF4FF&color=76A9FA' }}"
                                    alt="Dokumentasi Laporan">
                                <div class="flex-grow">
                                    <div class="flex justify-between items-start">
                                        <p class="text-sm text-gray-500">#{{ $report->report_code }}</p>
                                        <span
                                            class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-yellow-100 text-yellow-800">
                                            Menunggu Verifikasi
                                        </span>
                                    </div>
                                    <h4 class="font-bold text-gray-800 text-lg leading-tight mt-1">{{ $report->title }}
                                    </h4>
                                    <div class="mt-2 text-sm text-gray-600 space-y-1">
                                        <p class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $report->location_address }}
                                        </p>
                                        <p class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ $report->resident->name }}
                                        </p>
                                    </div>
                                </div>
                                <div class="w-full sm:w-auto flex-shrink-0 mt-4 sm:mt-0">
                                    <a href="{{ route('leader.assignment.create', $report) }}"
                                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-dishub-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dishub-blue-700">
                                        Verifikasi & Tugaskan
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 px-4 text-slate-500">
                                <svg class="w-12 h-12 mx-auto mb-2 text-green-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="font-bold">
                                    @if (empty($search))
                                        Tidak ada laporan yang perlu diverifikasi!
                                    @else
                                        Laporan tidak ditemukan
                                    @endif
                                </p>
                                <p class="text-sm">
                                    @if (empty($search))
                                        Semua laporan sudah ditangani. Kerja bagus!
                                    @else
                                        Tidak ada laporan pending yang cocok dengan kata kunci "{{ $search }}".
                                    @endif
                                </p>
                            </div>
                        @endforelse
                    </div>

                    @if ($reports->hasPages())
                        <div class="mt-6">{{ $reports->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
