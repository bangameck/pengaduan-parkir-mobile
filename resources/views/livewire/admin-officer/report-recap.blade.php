<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Rekapitulasi Laporan Pengaduan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- KONTROL FILTER & EKSPOR --}}
            <div
                class="bg-white p-4 rounded-lg shadow-sm border flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4 flex-wrap">
                    <select wire:model.live="filterType" class="border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="this_month">Bulan Ini</option>
                        <option value="this_year">Tahun Ini</option>
                        <option value="all">Semua Waktu</option>
                        <option value="custom">Pilih Tanggal</option>
                    </select>
                    @if ($filterType === 'custom')
                        <div class="flex items-center gap-2 text-sm" x-data>
                            <input type="date" wire:model.live="startDate"
                                class="border-gray-300 rounded-md shadow-sm">
                            <span class="text-gray-500">hingga</span>
                            <input type="date" wire:model.live="endDate"
                                class="border-gray-300 rounded-md shadow-sm">
                        </div>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="exportPdf" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-red-700 disabled:opacity-50">
                        <svg wire:loading.remove wire:target="exportPdf" class="w-5 h-5 mr-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        <span wire:loading.remove wire:target="exportPdf">PDF</span>
                        <span wire:loading wire:target="exportPdf">Mencetak...</span>
                    </button>
                    <button wire:click="exportExcel" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-green-700 disabled:opacity-50">
                        <svg wire:loading.remove wire:target="exportExcel" class="w-5 h-5 mr-2" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span wire:loading.remove wire:target="exportExcel">Excel</span>
                        <span wire:loading wire:target="exportExcel">Mengekspor...</span>
                    </button>
                </div>
            </div>

            {{-- KARTU STATISTIK --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-xl shadow-lg border">
                    <h3 class="text-sm text-gray-500">Total Laporan</h3>
                    <p class="text-3xl font-bold mt-1 text-dishub-blue-800">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg border">
                    <h3 class="text-sm text-green-600">Selesai</h3>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['completed'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg border">
                    <h3 class="text-sm text-red-600">Ditolak</h3>
                    <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['rejected'] }}</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg border">
                    <h3 class="text-sm text-orange-600">Dalam Proses</h3>
                    <p class="text-3xl font-bold text-orange-600 mt-1">{{ $stats['in_progress'] }}</p>
                </div>
            </div>

            {{-- GRAFIK --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg border">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Tren Laporan Masuk</h3>
                    <div wire:ignore class="h-80"><canvas id="reportsTrendChart"></canvas></div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg border">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Proporsi Status</h3>
                    <div wire:ignore class="h-80"><canvas id="statusPieChart"></canvas></div>
                </div>
            </div>

            {{-- TABEL DETAIL LAPORAN --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Detail Laporan</h3>
                    <div wire:loading.class.delay="opacity-50" class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full bg-white">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-semibold uppercase text-slate-600">Kode
                                        & Judul</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold uppercase text-slate-600">
                                        Pelapor</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold uppercase text-slate-600">
                                        Status</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold uppercase text-slate-600">
                                        Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @php
                                    $statusClasses = [
                                        'verified' => 'bg-blue-100 text-blue-800',
                                        'in_progress' => 'bg-orange-100 text-orange-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                    ];
                                @endphp
                                @forelse ($reports as $report)
                                    <tr class="hover:bg-slate-50 border-b">
                                        <td class="py-3 px-4 text-sm">
                                            <a href="{{ route('admin.laporan.index', ['search' => $report->report_code]) }}"
                                                class="font-semibold text-dishub-blue-700 hover:underline"
                                                title="Klik untuk mencari laporan ini di Manajemen Laporan">{{ $report->title }}</a>
                                            <p class="font-mono text-xs text-gray-500">#{{ $report->report_code }}</p>
                                        </td>
                                        <td class="py-3 px-4 text-sm">{{ $report->resident->name }}</td>
                                        <td class="py-3 px-4 text-sm"><span
                                                class="px-2 py-1 text-xs font-medium rounded-full capitalize {{ $statusClasses[$report->status] ?? 'bg-gray-100 text-gray-700' }}">{{ str_replace('_', ' ', $report->status) }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-sm">
                                            {{ $report->created_at->isoFormat('D MMM Y, HH:mm') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-10">Tidak ada laporan yang cocok dengan
                                            filter saat ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($reports->hasPages())
                        <div class="mt-4">{{ $reports->links() }}</div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                const trendCtx = document.getElementById('reportsTrendChart')?.getContext('2d');
                const pieCtx = document.getElementById('statusPieChart')?.getContext('2d');
                if (!trendCtx || !pieCtx) return;

                const trendChart = new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: [],
                        datasets: [{
                            data: []
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

                const pieChart = new Chart(pieCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Selesai', 'Ditolak', 'Dalam Proses', 'Pending'],
                        datasets: [{
                            data: [],
                            backgroundColor: ['#10B981', '#EF4444', '#F59E0B', '#FBBF24'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

                // Dengarkan event 'updateCharts' dari komponen Livewire
                Livewire.on('updateCharts', (event) => {
                    // Pastikan eventData tidak null
                    if (!event || !event[0]) return;
                    const eventData = event[0];

                    // Perbarui grafik tren
                    if (eventData.trendData) {
                        trendChart.data.labels = eventData.trendData.labels;
                        trendChart.data.datasets[0].data = eventData.trendData.values;
                        trendChart.data.datasets[0].label = 'Laporan Masuk';
                        trendChart.data.datasets[0].borderColor = '#1E40AF';
                        trendChart.data.datasets[0].backgroundColor = 'rgba(30, 64, 175, 0.1)';
                        trendChart.data.datasets[0].fill = true;
                        trendChart.data.datasets[0].tension = 0.3;
                        trendChart.update();
                    }

                    // Perbarui grafik pie
                    if (eventData.pieChartData) {
                        pieChart.data.datasets[0].data = eventData.pieChartData.values;
                        pieChart.update();
                    }
                });
            });
        </script>
    @endpush
</div>
