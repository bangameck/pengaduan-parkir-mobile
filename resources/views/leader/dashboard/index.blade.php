<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Leader') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Welcome Message dengan Avatar --}}
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
                    <h3 class="text-2xl font-bold text-gray-800">Selamat Datang, <span
                            class="text-dishub-blue-800">{{ Auth::user()->name }}</span>!</h3>
                    <p class="text-gray-500">Berikut adalah ringkasan strategis dari kinerja tim dan aktivitas laporan.
                    </p>
                </div>
            </div>

            {{-- Kartu Statistik Utama Berwarna --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-gradient-to-br from-dishub-blue-700 to-dishub-blue-900 p-6 rounded-2xl shadow-xl text-white transition-transform hover:scale-105">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 p-3 rounded-xl"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg></div>
                        <div>
                            <h3 class="text-sm font-medium opacity-80">Total Petugas Lapangan</h3>
                            <p class="text-3xl font-bold mt-1">{{ $stats['total_officers'] }}</p>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-gradient-to-br from-green-500 to-green-700 p-6 rounded-2xl shadow-xl text-white transition-transform hover:scale-105">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 p-3 rounded-xl"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg></div>
                        <div>
                            <h3 class="text-sm font-medium opacity-80">Laporan Selesai (Bulan Ini)</h3>
                            <p class="text-3xl font-bold mt-1">{{ $stats['completed_this_month'] }}</p>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-gradient-to-br from-orange-400 to-orange-600 p-6 rounded-2xl shadow-xl text-white transition-transform hover:scale-105">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 p-3 rounded-xl"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg></div>
                        <div>
                            <h3 class="text-sm font-medium opacity-80">Laporan Dalam Proses</h3>
                            <p class="text-3xl font-bold mt-1">{{ $stats['in_progress_now'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafik & Peta --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Grafik Kinerja Tim (Total Laporan Selesai)</h3>
                    <div class="h-80"><canvas id="teamChart"></canvas></div>
                </div>
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Peta Sebaran Laporan Selesai (50 Terbaru)</h3>
                    <div id="map" class="w-full h-80 rounded-lg shadow-inner border"></div>
                </div>
            </div>

            {{-- Aktivitas Terbaru & Daftar Tim --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <div class="lg:col-span-3 bg-white overflow-hidden shadow-lg sm:rounded-2xl">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900">Aktivitas Laporan Terbaru</h3>
                        <div class="mt-4 flow-root">
                            <ul role="list" class="-mb-8">
                                @forelse ($recentReports as $report)
                                    <li>
                                        <div class="relative pb-8">
                                            @if (!$loop->last)
                                                <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"
                                                    aria-hidden="true"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div><span
                                                        class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center ring-8 ring-white">
                                                        @php
                                                            $statusIcon = [
                                                                'pending' =>
                                                                    '<svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>',
                                                                'verified' =>
                                                                    '<svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.707a1 1 0 00-1.414-1.414L9 9.586 7.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" /></svg>',
                                                                'in_progress' =>
                                                                    '<svg class="h-5 w-5 text-orange-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414l-3-3z" clip-rule="evenodd" /></svg>',
                                                                'completed' =>
                                                                    '<svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>',
                                                                'rejected' =>
                                                                    '<svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>',
                                                            ];
                                                        @endphp
                                                        {!! $statusIcon[$report->status] ?? $statusIcon['pending'] !!}
                                                    </span></div>
                                                <div class="min-w-0 flex-1 justify-between space-x-4 pt-1.5 flex">
                                                    <div>
                                                        <p class="text-sm text-gray-500">Laporan <span
                                                                class="font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $report->status) }}</span>
                                                            oleh <span
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
                                    <p class="text-sm text-center text-gray-500 py-4">Belum ada aktivitas laporan
                                        terbaru.</p>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- KINERJA TIM PETUGAS LAPANGAN --}}
                <div class="lg:col-span-2 bg-white overflow-hidden shadow-lg sm:rounded-2xl">
                    <div class="p-6 text-gray-900">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Kinerja Tim</h3>
                                <p class="text-sm text-gray-500">Daftar petugas lapangan.</p>
                            </div>
                            <form action="{{ route('leader.dashboard') }}" method="GET">
                                <div class="relative w-full sm:w-auto">
                                    <input type="text" name="search" placeholder="Cari nama..."
                                        value="{{ $search ?? '' }}"
                                        class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm pl-10">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="mt-4 space-y-3">
                            @forelse ($teamMembers as $member)
                                <div class="p-3 rounded-lg hover:bg-slate-50 border flex justify-between items-center">
                                    <div class="flex items-center gap-3">
                                        <img class="h-10 w-10 rounded-full object-cover"
                                            src="{{ $member->image ? Storage::url($member->image) : 'https://ui-avatars.com/api/?name=' . urlencode($member->name) . '&background=EBF4FF&color=76A9FA' }}"
                                            alt="{{ $member->name }}">
                                        <div>
                                            <p class="font-medium text-slate-800">{{ $member->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $member->month_completed_reports }}
                                                Laporan Bulan Ini</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('leader.team.show', $member) }}"
                                        class="text-slate-400 hover:text-slate-700" title="Lihat Detail Kinerja">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            @empty
                                <p class="text-center text-sm text-slate-500 py-4">Tidak ada Field Officer ditemukan.
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Inisialisasi Grafik Kinerja Tim
                const chartCtx = document.getElementById('teamChart')?.getContext('2d');
                if (chartCtx) {
                    const gradient = chartCtx.createLinearGradient(0, 0, 0, 320);
                    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.6)');
                    gradient.addColorStop(1, 'rgba(251, 191, 36, 0.4)');

                    new Chart(chartCtx, {
                        type: 'bar',
                        data: {
                            labels: @json($chartData['labels']),
                            datasets: [{
                                label: 'Total Laporan Selesai',
                                data: @json($chartData['values']),
                                backgroundColor: gradient,
                                borderColor: 'rgba(30, 64, 175, 1)',
                                borderWidth: 1,
                                borderRadius: 5,
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

                // Inisialisasi Peta
                const mapElement = document.getElementById('map');
                if (mapElement && L) {
                    const map = L.map('map').setView([0.5333, 101.45], 12); // Koordinat Pekanbaru
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    const locations = @json($mapLocations);
                    if (locations.length > 0) {
                        locations.forEach(loc => {
                            let popupContent =
                                `<b>${loc.report.title}</b><br><small>#${loc.report.report_code}</small>`;
                            L.marker([loc.proof_latitude, loc.proof_longitude]).addTo(map).bindPopup(
                                popupContent);
                        });
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
