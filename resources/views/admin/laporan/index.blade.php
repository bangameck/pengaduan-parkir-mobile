<x-app-layout>
    {{-- Bagian Header Halaman (Sesuai Pola Breeze) --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verifikasi Laporan Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- Menampilkan Pesan Sukses/Error dari Controller --}}
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                            <p class="font-bold">Sukses</p>
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                            <p class="font-bold">Gagal</p>
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    {{-- Form Pencarian Laporan --}}
                    <div class="mb-6">
                        <form action="{{ route('admin.laporan.index') }}" method="GET">
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </span>
                                <input type="text" name="search" placeholder="Cari kode atau judul laporan..."
                                    value="{{ $search ?? '' }}"
                                    class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-150">
                            </div>
                        </form>
                    </div>

                    {{-- Tabel Daftar Laporan --}}
                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Kode Laporan</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Pelapor</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Judul</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Tanggal</th>
                                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($reports as $report)
                                    <tr class="hover:bg-gray-50 border-b">
                                        <td class="py-3 px-4 font-mono text-xs">{{ $report->report_code }}</td>
                                        <td class="py-3 px-4">{{ $report->resident->name }}</td>
                                        <td class="py-3 px-4">{{ Str::limit($report->title, 40) }}</td>
                                        <td class="py-3 px-4">{{ $report->created_at->isoFormat('D MMM YYYY, HH:mm') }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="flex justify-center items-center space-x-2">

                                                {{-- Tombol Detail --}}
                                                <a href="{{ route('laporan.show', $report) }}" title="Lihat Detail"
                                                    class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition duration-200">
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

                                                {{-- Tombol Verifikasi dengan SweetAlert2 --}}
                                                <button type="button" title="Verifikasi Laporan"
                                                    class="p-2 bg-green-100 text-green-600 rounded-full hover:bg-green-200 transition duration-200 js-verify-btn"
                                                    data-report-id="{{ $report->id }}"
                                                    data-report-code="{{ $report->report_code }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </button>

                                                {{-- Tombol Tolak dengan SweetAlert2 --}}
                                                <button type="button" title="Tolak Laporan"
                                                    class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200 transition duration-200 js-reject-btn"
                                                    data-report-id="{{ $report->id }}"
                                                    data-report-code="{{ $report->report_code }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>

                                                {{-- Form tersembunyi untuk Aksi --}}
                                                <form id="verify-form-{{ $report->id }}"
                                                    action="{{ route('admin.laporan.verify', $report) }}"
                                                    method="POST" class="hidden"> @csrf @method('PATCH') </form>
                                                <form id="reject-form-{{ $report->id }}"
                                                    action="{{ route('admin.laporan.reject', $report) }}"
                                                    method="POST" class="hidden">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="rejection_reason"
                                                        id="rejection-reason-{{ $report->id }}">
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-6 px-4">
                                            @if ($search ?? false)
                                                Laporan dengan kata kunci "{{ $search }}" tidak ditemukan.
                                            @else
                                                Tidak ada laporan yang perlu diverifikasi saat ini. Hebat!
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Link Paginasi --}}
                    <div class="mt-6">
                        {{ $reports->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // === LOGIKA UNTUK TOMBOL VERIFIKASI ===
                const verifyButtons = document.querySelectorAll('.js-verify-btn');
                verifyButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const reportId = this.dataset.reportId;
                        const reportCode = this.dataset.reportCode;
                        const form = document.getElementById('verify-form-' + reportId);

                        Swal.fire({
                            title: 'Verifikasi Laporan ' + reportCode + '?',
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
                const rejectButtons = document.querySelectorAll('.js-reject-btn');
                rejectButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const reportId = this.dataset.reportId;
                        const reportCode = this.dataset.reportCode;
                        const form = document.getElementById('reject-form-' + reportId);
                        const reasonInput = document.getElementById('rejection-reason-' + reportId);

                        Swal.fire({
                            title: 'Tolak Laporan ' + reportCode + '?',
                            input: 'textarea',
                            inputLabel: 'Alasan Penolakan',
                            inputPlaceholder: 'Tuliskan alasan kenapa laporan ini ditolak...',
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
