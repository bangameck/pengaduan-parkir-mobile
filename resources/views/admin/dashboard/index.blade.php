<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin Officer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Welcome Message --}}

            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200 flex items-center gap-5">
                <div class="relative flex-shrink-0">
                    {{-- Avatar dengan border gradien biru-kuning --}}
                    <div class="w-20 h-20 rounded-full p-1 bg-gradient-to-tr from-dishub-yellow-400 to-dishub-blue-600">
                        <img class="h-full w-full rounded-full object-cover border-4 border-white"
                            src="{{ Auth::user()->image ? Storage::url(Auth::user()->image) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=EBF4FF&color=1E40AF&size=128&bold=true' }}"
                            alt="Avatar">
                    </div>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Selamat Datang Kembali, <span
                            class="text-dishub-blue-800">{{ Auth::user()->name }}</span>!</h3>
                    <p class="text-gray-500">Berikut adalah ringkasan aktivitas verifikasi laporan Anda.</p>
                </div>
            </div>

            {{-- Kartu Statistik Utama Berwarna --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-yellow-400 to-yellow-500 p-6 rounded-2xl shadow-xl text-white">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/30 p-3 rounded-xl"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg></div>
                        <div>
                            <h3 class="text-sm font-medium opacity-80">Perlu Diverifikasi</h3>
                            <p class="text-3xl font-bold mt-1">{{ $stats['pending'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-2xl shadow-xl text-white">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/30 p-3 rounded-xl"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg></div>
                        <div>
                            <h3 class="text-sm font-medium opacity-80">Diverifikasi Hari Ini</h3>
                            <p class="text-3xl font-bold mt-1">{{ $stats['verified_today'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-red-500 to-red-600 p-6 rounded-2xl shadow-xl text-white">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/30 p-3 rounded-xl"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg></div>
                        <div>
                            <h3 class="text-sm font-medium opacity-80">Ditolak Hari Ini</h3>
                            <p class="text-3xl font-bold mt-1">{{ $stats['rejected_today'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafik & Daftar Laporan --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <div class="lg:col-span-3 space-y-6">
                    {{-- Grafik Tren Laporan --}}
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl p-6 border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Tren Laporan Masuk (7 Hari Terakhir)</h3>
                        <div class="h-64"><canvas id="trendChart"></canvas></div>
                    </div>
                    {{-- Grafik Sumber Laporan --}}
                    <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl p-6 border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Proporsi Sumber Laporan</h3>
                        <div class="h-64"><canvas id="sourceChart"></canvas></div>
                    </div>
                </div>

                {{-- Daftar Laporan Terbaru yang Perlu Diverifikasi --}}
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-lg sm:rounded-2xl">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Antrian Verifikasi</h3>
                                <p class="text-sm text-gray-500">5 laporan terbaru.</p>
                            </div>
                            <a href="{{ route('admin.laporan.index') }}"
                                class="text-sm font-semibold text-dishub-blue-700 hover:underline">
                                Lihat Semua
                            </a>
                        </div>
                        <div class="mt-4 flow-root">
                            <ul role="list" class="-mb-8">
                                @forelse ($recentPendingReports as $report)
                                    <li>
                                        <div class="relative pb-8">
                                            @if (!$loop->last)
                                                <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"
                                                    aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div><span
                                                        class="h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center ring-8 ring-white"><svg
                                                            class="h-5 w-5 text-yellow-600" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z"
                                                                clip-rule="evenodd" />
                                                        </svg></span></div>
                                                <div class="min-w-0 flex-1 justify-between space-x-4 pt-1.5 flex">
                                                    <div>
                                                        <p class="text-sm text-gray-500">
                                                            Laporan dari <span
                                                                class="font-medium text-gray-900">{{ $report->resident->name }}</span>
                                                        </p>
                                                        <a href="{{ route('public.laporan.show', $report) }}"
                                                            class="font-semibold text-dishub-blue-700 hover:underline">{{ $report->title }}</a>
                                                    </div>
                                                    <div class="text-right text-xs whitespace-nowrap text-gray-500">
                                                        <time
                                                            datetime="{{ $report->created_at }}">{{ $report->created_at->diffForHumans() }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <div class="text-center py-10 text-gray-500">
                                        <svg class="w-12 h-12 mx-auto text-green-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="mt-2 font-semibold">Kerja Bagus!</p>
                                        <p class="text-sm">Tidak ada laporan yang perlu diverifikasi.</p>
                                    </div>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Grafik Tren
                const trendCtx = document.getElementById('trendChart')?.getContext('2d');
                if (trendCtx) {
                    new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: @json($trendChart['labels']),
                            datasets: [{
                                label: 'Laporan Masuk',
                                data: @json($trendChart['values']),
                                borderColor: '#FBBF24', // Kuning
                                backgroundColor: 'rgba(251, 191, 36, 0.1)',
                                fill: true,
                                tension: 0.3
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            }
                        }
                    });
                }

                // Grafik Sumber
                const sourceCtx = document.getElementById('sourceChart')?.getContext('2d');
                if (sourceCtx) {
                    new Chart(sourceCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($sourceChart['labels']),
                            datasets: [{
                                data: @json($sourceChart['values']),
                                backgroundColor: ['#1E40AF', '#FBBF24', '#10B981', '#EF4444',
                                    '#6B7280'
                                ], // Biru, Kuning, Hijau, Merah, Abu-abu
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
