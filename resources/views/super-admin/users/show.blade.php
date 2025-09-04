<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-bold text-2xl text-blue-900 tracking-tight">
                Profil Pengguna
            </h2>
            <a href="{{ route('super-admin.users.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-yellow-400 text-white rounded-xl font-semibold text-sm shadow-md hover:scale-105 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                {{-- Kartu Profil --}}
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-xl rounded-2xl overflow-hidden hover:shadow-2xl transition">
                        {{-- Header --}}
                        <div class="relative p-6 text-center bg-gradient-to-br from-blue-800 to-blue-600">
                            <img class="h-32 w-32 rounded-full object-cover mx-auto shadow-xl border-4 border-yellow-400 transform hover:scale-110 transition-transform duration-300"
                                src="{{ $user->image ? Storage::url($user->image) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=EBF4FF&color=1E40AF&size=256&bold=true' }}"
                                alt="{{ $user->name }}">
                            <h3 class="mt-4 text-2xl font-bold text-white">{{ $user->name }}</h3>
                            <p class="text-sm text-yellow-300">{{ '@' . $user->username }}</p>
                            <div class="mt-4">
                                <span
                                    class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold bg-yellow-400/90 text-blue-900 shadow">
                                    {{ str_replace('-', ' ', $user->role->name) }}
                                </span>
                            </div>
                        </div>

                        {{-- Detail Kontak --}}
                        <div class="p-6">
                            <h4 class="text-sm font-bold text-blue-600 uppercase tracking-wider mb-4">Detail Kontak
                            </h4>
                            <div class="space-y-4 text-sm">
                                <p class="flex items-start gap-3 text-gray-700">
                                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ $user->email }}
                                </p>
                                <p class="flex items-start gap-3 text-gray-700">
                                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28l1.5 4.5-2.26 1.13a11.04 11.04 0 005.52 5.52l1.13-2.26 4.5 1.5V19a2 2 0 01-2 2H5a2 2 0 01-2-2V5z" />
                                    </svg>
                                    {{ $user->phone_number ?? '-' }}
                                </p>
                                <p class="flex items-start gap-3 text-gray-700">
                                    <svg class="h-5 w-5 text-blue-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Bergabung: {{ $user->created_at->isoFormat('D MMMM YYYY') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Konten Kanan --}}
                <div class="lg:col-span-3 space-y-6">
                    @if (in_array($user->role->name, ['resident', 'admin-officer', 'field-officer']) && $reportCounts['total'] > 0)
                        {{-- Widget Statistik --}}
                        {{-- Contoh: Statistik --}}
                        <div class="bg-white p-6 rounded-2xl shadow-lg border border-blue-100">
                            <h3 class="text-lg font-bold text-blue-900 mb-4">Statistik Kinerja</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                                <div class="bg-blue-50 p-4 rounded-xl">
                                    <p class="text-3xl font-bold text-blue-800">{{ $reportCounts['today'] }}</p>
                                    <p class="text-sm text-blue-600">Hari Ini</p>
                                </div>
                                <div class="bg-green-50 p-4 rounded-xl">
                                    <p class="text-3xl font-bold text-green-800">{{ $reportCounts['this_month'] }}</p>
                                    <p class="text-sm text-green-600">Bulan Ini</p>
                                </div>
                                <div class="bg-yellow-50 p-4 rounded-xl">
                                    <p class="text-3xl font-bold text-yellow-800">{{ $reportCounts['this_year'] }}</p>
                                    <p class="text-sm text-yellow-600">Tahun Ini</p>
                                </div>
                                <div class="bg-indigo-50 p-4 rounded-xl">
                                    <p class="text-3xl font-bold text-indigo-800">{{ $reportCounts['total'] }}</p>
                                    <p class="text-sm text-indigo-600">Total</p>
                                </div>
                            </div>
                        </div>

                        {{-- Grafik Laporan --}}
                        <div class="bg-white shadow-lg rounded-2xl p-6 border border-blue-100">
                            <h4 class="text-lg font-bold text-blue-900 mb-4">Aktivitas Laporan (30 Hari Terakhir)</h4>
                            <div class="h-64"><canvas id="reportChart"></canvas></div>
                        </div>

                        <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl">
                            <div class="p-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-4">Daftar Laporan Terkait</h4>
                                <div class="space-y-3">
                                    @php
                                        $statusClasses = [
                                            'verified' => 'bg-blue-100 text-blue-800',
                                            'in_progress' => 'bg-orange-100 text-orange-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                        ];
                                    @endphp
                                    @forelse ($relatedReports as $report)
                                        <a href="{{ route('public.laporan.show', $report) }}" target="_blank"
                                            class="block p-4 border rounded-lg hover:bg-gray-50 hover:border-blue-300 transition-colors duration-200">
                                            <div class="flex justify-between items-start">
                                                <p class="font-semibold text-gray-800">{{ $report->title }}</p>
                                                <span
                                                    class="text-xs font-medium px-2 py-0.5 rounded-full capitalize {{ $statusClasses[$report->status] ?? 'bg-gray-100 text-gray-700' }}">{{ str_replace('_', ' ', $report->status) }}</span>
                                            </div>
                                            <p class="text-sm text-gray-500 mt-1">#{{ $report->report_code }} -
                                                {{ $report->created_at->diffForHumans() }}</p>
                                        </a>
                                    @empty
                                        <p class="text-sm text-gray-500 text-center py-4">Tidak ada laporan terkait yang
                                            ditemukan.</p>
                                    @endforelse
                                </div>
                                @if ($relatedReports->hasPages())
                                    <div class="mt-4">
                                        {{ $relatedReports->links('vendor.pagination.simple-tailwind') }}</div>
                                @endif
                            </div>
                        </div>
                </div>
            @else
                {{-- Tampilan default jika tidak ada data atau untuk role admin --}}
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl p-12 text-center">
                    <svg class="w-20 h-20 mx-auto text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                        </path>
                    </svg>
                    <h4 class="text-xl font-bold text-gray-800 mt-4">
                        @if (in_array($user->role->name, ['super-admin', 'leader']))
                            Administrator Sistem
                        @else
                            Belum Ada Aktivitas
                        @endif
                    </h4>
                    <p class="text-sm text-gray-500 mt-2 max-w-sm mx-auto">
                        @if (in_array($user->role->name, ['super-admin', 'leader']))
                            Pengguna ini memiliki hak akses administratif dan tidak memiliki riwayat laporan personal.
                        @else
                            Pengguna ini belum memiliki riwayat laporan apa pun di dalam sistem.
                        @endif
                    </p>
                </div>
                @endif

            </div>

        </div>
    </div>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @if (isset($chartData['labels']) && count($chartData['labels']) > 0)
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const ctx = document.getElementById('reportChart')?.getContext('2d');
                    if (ctx) {
                        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.6)'); // Biru
                        gradient.addColorStop(1, 'rgba(251, 191, 36, 0.4)'); // Kuning

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: @json($chartData['labels']),
                                datasets: [{
                                    label: 'Jumlah Laporan',
                                    data: @json($chartData['values']),
                                    backgroundColor: gradient,
                                    borderColor: 'rgba(37, 99, 235, 1)',
                                    borderWidth: 1,
                                    borderRadius: 5,
                                    hoverBackgroundColor: 'rgba(37, 99, 235, 0.8)',
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
        @endif
    @endpush
</x-app-layout>
