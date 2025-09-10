<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Laporan Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">

                    {{-- ======================================================= --}}
                    {{-- == AWAL BAGIAN TABS DENGAN UNDERLINE DAN IKON == --}}
                    {{-- ======================================================= --}}
                    <div class="border-b border-gray-200 overflow-x-auto">
                        {{-- 2. Tambahkan whitespace-nowrap agar tab tidak turun ke bawah --}}
                        <nav class="-mb-px flex space-x-6 whitespace-nowrap" aria-label="Tabs">
                            @php
                                $tabs = [
                                    'pending' => [
                                        'label' => 'Menunggu Verifikasi',
                                        'icon' =>
                                            '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>',
                                    ],
                                    'completed' => [
                                        'label' => 'Selesai',
                                        'icon' =>
                                            '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>',
                                    ],
                                    'rejected' => [
                                        'label' => 'Ditolak',
                                        'icon' =>
                                            '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>',
                                    ],
                                    'all' => [
                                        'label' => 'Seluruh Laporan',
                                        'icon' =>
                                            '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>',
                                    ],
                                ];
                            @endphp

                            @foreach ($tabs as $status => $data)
                                <a href="{{ route('admin.laporan.index', ['status' => $status]) }}"
                                    class="{{ $activeStatus == $status
                                        ? 'border-dishub-blue-500 text-dishub-blue-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}
                                          whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                                    {!! $data['icon'] !!}
                                    {{ $data['label'] }}
                                    @if ($reportCounts[$status] > 0)
                                        <span
                                            class="{{ $activeStatus == $status ? 'bg-dishub-blue-100 text-dishub-blue-600' : 'bg-gray-100 text-gray-600' }}
                                                       ml-1 py-0.5 px-2.5 rounded-full text-xs font-medium">
                                            {{ $reportCounts[$status] }}
                                        </span>
                                    @endif
                                </a>
                            @endforeach
                        </nav>
                    </div>
                    {{-- ======================================================= --}}
                    {{-- == AKHIR BAGIAN TABS == --}}
                    {{-- ======================================================= --}}

                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        {{-- Form Pencarian --}}
                        <form action="{{ route('admin.laporan.index') }}" method="GET">
                            <input type="hidden" name="status" value="{{ $activeStatus }}">
                            <div class="relative">
                                <input type="text" name="search" placeholder="Cari kode/judul..."
                                    value="{{ $search ?? '' }}"
                                    class="w-full sm:w-64 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm pl-10">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                    </svg>
                                </div>
                            </div>
                        </form>
                        {{-- Tombol Tambah Laporan --}}
                        <a href="{{ route('admin.laporan.create') }}"
                            class="inline-flex items-center px-4 py-2 bg-dishub-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dishub-blue-700 active:bg-dishub-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 whitespace-nowrap">
                            Tambah Laporan
                        </a>
                    </div>

                    {{-- Notifikasi Sukses --}}
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    {{-- Tabel Laporan --}}
                    <div class="overflow-x-auto border rounded-lg">
                        <!--<table class="min-w-full bg-white">
                            {{-- ... (thead sama seperti sebelumnya) ... --}}
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-xs text-slate-600">Kode
                                        Laporan</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-xs text-slate-600">Judul
                                        & Pelapor</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-xs text-slate-600">
                                        Status</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-xs text-slate-600">
                                        Tanggal Masuk</th>
                                    <th class="text-center py-3 px-4 uppercase font-semibold text-xs text-slate-600">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($reports as $report)
<tr class="hover:bg-slate-50 border-b border-slate-200">
                                        {{-- ... (td sama seperti sebelumnya) ... --}}
                                        <td class="py-3 px-4 font-mono text-sm text-slate-500">
                                            #{{ $report->report_code }}</td>
                                        <td class="py-3 px-4">
                                            <p class="font-medium text-slate-800">{{ Str::limit($report->title, 40) }}
                                            </p>
                                            <p class="text-xs text-slate-500">oleh {{ $report->resident->name }}</p>
                                        </td>
                                        <td class="py-3 px-4">
                                            @php
                                                $statusClasses = [
                                                    'verified' => 'bg-blue-100 text-blue-800',
                                                    'in_progress' => 'bg-orange-100 text-orange-800',
                                                    'completed' => 'bg-green-100 text-green-800',
                                                    'rejected' => 'bg-red-100 text-red-800',
                                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                                ];
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize {{ $statusClasses[$report->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $report->status }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-slate-600">
                                            {{ \Carbon\Carbon::parse($report->created_at)->isoFormat('D MMM Y, HH:mm') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                {{-- LOGIKA TOMBOL AKSI DINAMIS --}}
                                                @if ($activeStatus == 'pending')
{{-- Tampilkan semua aksi jika di tab "Menunggu Verifikasi" --}}
                                                    <a href="{{ route('public.laporan.show', $report) }}"
                                                        class="text-slate-600 hover:text-slate-900"
                                                        title="Lihat Detail">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                    <button type="button"
                                                        class="text-green-600 hover:text-green-900 js-verify-btn"
                                                        title="Verifikasi Laporan" data-report-id="{{ $report->id }}"
                                                        data-report-code="{{ $report->report_code }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </button>
                                                    <button type="button"
                                                        class="text-red-600 hover:text-red-900 js-reject-btn"
                                                        title="Tolak Laporan" data-report-id="{{ $report->id }}"
                                                        data-report-code="{{ $report->report_code }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
@else
{{-- Hanya tampilkan detail di tab lain --}}
                                                    <a href="{{ route('public.laporan.show', $report) }}"
                                                        class="text-slate-600 hover:text-slate-900"
                                                        title="Lihat Detail">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                            </path>
                                                        </svg>
                                                    </a>
@endif
                                                {{-- Form tersembunyi untuk Aksi --}}
                                                <form id="verify-form-{{ $report->id }}"
                                                    action="{{ route('admin.laporan.verify', $report) }}"
                                                    method="POST" class="hidden">@csrf</form>
                                                <form id="reject-form-{{ $report->id }}"
                                                    action="{{ route('admin.laporan.reject', $report) }}"
                                                    method="POST" class="hidden">
                                                    @csrf
                                                    <input type="hidden" name="rejection_reason"
                                                        id="rejection-reason-{{ $report->id }}">
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
@empty
                                    <tr>
                                        <td colspan="5" class="text-center py-10 px-4 text-slate-500">
                                            @if ($search ?? false)
<p class="font-bold">Laporan dengan kata kunci "{{ $search }}"
                                                    tidak ditemukan.</p>
@else
<p class="font-bold">Tidak ada laporan yang perlu diverifikasi saat
                                                    ini.</p>
                                                <p class="text-sm">Kerja bagus! Semua laporan sudah ditangani. 🎉</p>
@endif
                                        </td>
                                    </tr>
@endforelse
                            </tbody> -->

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @forelse ($reports as $report)
                                <div
                                    class="bg-white rounded-lg shadow-md border border-gray-200 flex flex-col overflow-hidden group">
                                    <a href="{{ route('public.laporan.show', $report) }}" class="block relative">
                                        <img class="h-40 w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                            src="{{ $report->images->first() ? ($report->images->first()->thumbnail_path ? Storage::url($report->images->first()->thumbnail_path) : Storage::url($report->images->first()->file_path)) : 'https://via.placeholder.com/400x300.png/EBF4FF/76A9FA?text=No+Image' }}"
                                            alt="Dokumentasi Laporan">
                                        @php
                                            $statusClasses = [
                                                'verified' => 'bg-blue-500',
                                                'in_progress' => 'bg-orange-500',
                                                'completed' => 'bg-green-500',
                                                'rejected' => 'bg-red-500',
                                                'pending' => 'bg-yellow-500',
                                            ];
                                        @endphp
                                        <span
                                            class="absolute top-2 right-2 text-white text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusClasses[$report->status] ?? 'bg-gray-500' }} capitalize">
                                            {{ str_replace('_', ' ', $report->status) }}
                                        </span>
                                    </a>
                                    <div class="p-4 flex flex-col flex-grow">
                                        <p class="text-xs text-gray-500">#{{ $report->report_code }}</p>
                                        <h4 class="font-bold text-gray-800 leading-tight truncate mt-1">
                                            {{ $report->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">oleh
                                            {{ $report->reportName }}
                                        </p>
                                        <div class="mt-auto pt-4 flex justify-between items-center">
                                            <p class="text-xs text-gray-400">
                                                {{ $report->created_at->diffForHumans() }}</p>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('super-admin.users.show', $report->resident) }}"
                                                    title="Lihat Profil Pelapor {{ $report->resident->name }}">
                                                    <img class="h-6 w-6 rounded-full object-cover transition-transform duration-200 hover:scale-110"
                                                        src="{{ $report->resident->image ? Storage::url($report->resident->image) : 'https://ui-avatars.com/api/?name=' . urlencode($report->resident->name) . '&background=EBF4FF&color=1E40AF&size=64&bold=true' }}"
                                                        alt="Avatar {{ $report->resident->name }}">
                                                </a>
                                                @if ($activeStatus == 'pending')
                                                    {{-- Tampilkan semua aksi jika di tab "Menunggu Verifikasi" --}}
                                                    <a href="{{ route('public.laporan.show', $report) }}"
                                                        class="text-slate-600 hover:text-slate-900"
                                                        title="Lihat Detail">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                    <button type="button"
                                                        class="text-green-600 hover:text-green-900 js-verify-btn"
                                                        title="Verifikasi Laporan"
                                                        data-report-id="{{ $report->id }}"
                                                        data-report-code="{{ $report->report_code }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </button>
                                                    <button type="button"
                                                        class="text-red-600 hover:text-red-900 js-reject-btn"
                                                        title="Tolak Laporan" data-report-id="{{ $report->id }}"
                                                        data-report-code="{{ $report->report_code }}">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                @else
                                                    {{-- Hanya tampilkan detail di tab lain --}}
                                                    <a href="{{ route('public.laporan.show', $report) }}"
                                                        class="text-slate-600 hover:text-slate-900"
                                                        title="Lihat Detail">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                            </path>
                                                        </svg>
                                                    </a>
                                                @endif
                                                {{-- Form tersembunyi untuk Aksi --}}
                                                <form id="verify-form-{{ $report->id }}"
                                                    action="{{ route('admin.laporan.verify', $report) }}"
                                                    method="POST" class="hidden">@csrf</form>
                                                <form id="reject-form-{{ $report->id }}"
                                                    action="{{ route('admin.laporan.reject', $report) }}"
                                                    method="POST" class="hidden">
                                                    @csrf
                                                    <input type="hidden" name="rejection_reason"
                                                        id="rejection-reason-{{ $report->id }}">
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-10 px-4 text-slate-500">
                                        @if ($search ?? false)
                                            <p class="font-bold">Laporan dengan kata kunci "{{ $search }}"
                                                tidak ditemukan.</p>
                                        @else
                                            <p class="font-bold">Tidak ada laporan yang perlu diverifikasi saat
                                                ini.</p>
                                            <p class="text-sm">Kerja bagus! Semua laporan sudah ditangani. 🎉</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </div>
                        </table>
                    </div>

                    {{-- Paginasi --}}
                    @if ($reports->hasPages())
                        <div class="mt-6">
                            {{ $reports->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Script SweetAlert2 (DIPERTAHANKAN 100% DARI KODE ANDA) --}}
    @push('scripts')
        <script>
            // Kita butuh CDN SweetAlert2, pastikan sudah ada di layout utama atau tambahkan di sini
            // <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/script>

            document.addEventListener('DOMContentLoaded', function() {
                // === LOGIKA UNTUK TOMBOL VERIFIKASI ===
                document.querySelectorAll('.js-verify-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const reportId = this.dataset.reportId;
                        const reportCode = this.dataset.reportCode;
                        const form = document.getElementById('verify-form-' + reportId);

                        Swal.fire({
                            title: 'Verifikasi Laporan #' + reportCode + '?',
                            text: "Laporan ini akan diteruskan ke petugas lapangan. Anda yakin?",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#16a34a', // green-600
                            cancelButtonColor: '#6b7280', // gray-500
                            confirmButtonText: 'Ya, Verifikasi!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });

                // === LOGIKA UNTUK TOMBOL TOLAK ===
                document.querySelectorAll('.js-reject-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const reportId = this.dataset.reportId;
                        const reportCode = this.dataset.reportCode;
                        const form = document.getElementById('reject-form-' + reportId);
                        const reasonInput = document.getElementById('rejection-reason-' + reportId);

                        Swal.fire({
                            title: 'Tolak Laporan #' + reportCode + '?',
                            input: 'textarea',
                            inputLabel: 'Alasan Penolakan',
                            inputPlaceholder: 'Tuliskan alasan kenapa laporan ini ditolak...',
                            inputAttributes: {
                                'aria-label': 'Tuliskan alasan penolakan'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Tolak Laporan',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#ef4444', // red-500
                            inputValidator: (value) => {
                                if (!value) {
                                    return 'Alasan penolakan wajib diisi!'
                                }
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                reasonInput.value = result.value;
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
