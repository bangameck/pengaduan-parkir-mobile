<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verifikasi Laporan Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">

                    {{-- Header dengan Tombol Aksi dan Pencarian --}}
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-dishub-blue-800">Laporan Menunggu Verifikasi</h3>
                            <p class="mt-1 text-sm text-gray-500">Daftar laporan yang dikirim oleh warga dan perlu
                                ditinjau.</p>
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            {{-- Tombol Tambah Laporan --}}
                            <a href="#" {{-- Ganti # dengan route('admin.laporan.create') jika sudah ada --}}
                                class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 bg-dishub-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dishub-blue-700 focus:outline-none focus:ring-2 focus:ring-dishub-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 whitespace-nowrap">
                                Tambah Laporan
                            </a>
                        </div>
                    </div>

                    {{-- Form Pencarian --}}
                    <form action="{{ route('admin.laporan.index') }}" method="GET">
                        <div class="relative">
                            <input type="text" name="search"
                                placeholder="Cari berdasarkan kode atau judul laporan..." value="{{ $search ?? '' }}"
                                class="w-full border-gray-300 focus:border-dishub-blue-500 focus:ring-dishub-blue-500 rounded-md shadow-sm text-sm pl-10">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                </svg>
                            </div>
                        </div>
                    </form>


                    {{-- Notifikasi Sukses/Error (dipertahankan dari kode Anda) --}}
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

                    {{-- Tabel Laporan Modern --}}
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full bg-white">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-xs text-slate-600">Kode
                                    </th>
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
                                        <td class="py-3 px-4 font-mono text-sm text-slate-500">
                                            #{{ $report->report_code }}</td>
                                        <td class="py-3 px-4">
                                            <p class="font-medium text-slate-800">{{ Str::limit($report->title, 40) }}
                                            </p>
                                            <p class="text-xs text-slate-500">oleh {{ $report->resident->name }}</p>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 capitalize">
                                                {{ $report->status }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-slate-600">
                                            {{ \Carbon\Carbon::parse($report->created_at)->isoFormat('D MMM Y, HH:mm') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                {{-- Tombol Aksi (divisualisasikan ulang, tapi fungsionalitas SAMA) --}}
                                                <a href="{{ route('laporan.show', $report) }}"
                                                    class="text-slate-600 hover:text-slate-900" title="Lihat Detail">
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

                                                {{-- Form tersembunyi untuk Aksi (SAMA SEPERTI KODE ANDA, TIDAK DIUBAH) --}}
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
                            </tbody>
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
