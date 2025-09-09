<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Petugas Lapangan') }}
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
                    <h3 class="text-2xl font-bold text-gray-800">Siap Bertugas, <span
                            class="text-dishub-blue-800">{{ Auth::user()->name }}</span>!</h3>
                    <p class="text-gray-500">Berikut adalah ringkasan tugas dan kinerja Anda.</p>
                </div>
            </div>

            {{-- Kartu Statistik Kinerja Berwarna --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-2xl shadow-xl text-white transition-transform hover:scale-105">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/30 p-3 rounded-xl"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg></div>
                        <div>
                            <h3 class="text-sm font-medium opacity-80">Tugas Baru Tersedia</h3>
                            <p class="text-3xl font-bold mt-1">{{ $stats['new_tasks_available'] }}</p>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-gradient-to-br from-orange-400 to-orange-500 p-6 rounded-2xl shadow-xl text-white transition-transform hover:scale-105">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/30 p-3 rounded-xl"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg></div>
                        <div>
                            <h3 class="text-sm font-medium opacity-80">Sedang Saya Kerjakan</h3>
                            <p class="text-3xl font-bold mt-1">{{ $stats['in_progress_by_user'] }}</p>
                        </div>
                    </div>
                </div>
                <div
                    class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-2xl shadow-xl text-white transition-transform hover:scale-105">
                    <div class="flex items-center gap-4">
                        <div class="bg-white/30 p-3 rounded-xl"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg></div>
                        <div>
                            <h3 class="text-sm font-medium opacity-80">Selesai Bulan Ini</h3>
                            <p class="text-3xl font-bold mt-1">{{ $stats['completed_by_user_month'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Grafik & Daftar Tugas --}}
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <div class="lg:col-span-3 bg-white overflow-hidden shadow-lg sm:rounded-2xl p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Grafik Kinerja Saya (7 Hari Terakhir)</h3>
                    <div class="h-80"><canvas id="performanceChart"></canvas></div>
                </div>

                <div class="lg:col-span-2 bg-white overflow-hidden shadow-lg sm:rounded-2xl">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Antrian Tugas Baru</h3>
                                <p class="text-sm text-gray-500">5 tugas terbaru.</p>
                            </div>
                            <a href="{{ route('petugas.tugas.index') }}"
                                class="text-sm font-semibold text-dishub-blue-700 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="mt-4 flow-root">
                            <ul role="list" class="-mb-8">
                                @forelse ($recentTasks as $report)
                                    <li>
                                        <div class="relative pb-8">
                                            @if (!$loop->last)
                                                <span
                                                    class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div><span
                                                        class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center ring-8 ring-white"><svg
                                                            class="h-5 w-5 text-blue-600" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M17.293 4.293a1 1 0 011.414 0l.707.707a1 1 0 010 1.414l-9 9a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586l8.293-8.293a1 1 0 010-1.414z" />
                                                        </svg></span></div>
                                                <div class="min-w-0 flex-1 justify-between space-x-4 pt-1.5 flex">
                                                    <div>
                                                        <a href="{{ route('petugas.tugas.createFollowUp', $report) }}"
                                                            class="font-semibold text-dishub-blue-700 hover:underline">{{ $report->title }}</a>
                                                        <p class="text-sm text-gray-500">
                                                            #{{ $report->report_code }}
                                                        </p>
                                                    </div>
                                                    <div class="text-right text-xs whitespace-nowrap text-gray-500">
                                                        <time>{{ $report->verified_at ? $report->verified_at->diffForHumans() : $report->created_at->diffForHumans() }}</time>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @empty
                                    <div class="text-center py-10 text-gray-500">
                                        <p>Tidak ada tugas baru tersedia.</p>
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
                const perfCtx = document.getElementById('performanceChart')?.getContext('2d');
                if (perfCtx) {
                    const gradient = perfCtx.createLinearGradient(0, 0, 0, 320);
                    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.6)'); // Biru
                    gradient.addColorStop(1, 'rgba(251, 191, 36, 0.4)'); // Kuning
                    new Chart(perfCtx, {
                        type: 'bar',
                        data: {
                            labels: @json($performanceChart['labels']),
                            datasets: [{
                                label: 'Laporan Selesai',
                                data: @json($performanceChart['values']),
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
            });
        </script>
    @endpush
</x-app-layout>
