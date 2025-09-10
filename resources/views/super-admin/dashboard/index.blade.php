<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Super Admin') }}
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
                    <h3 class="text-2xl font-bold text-gray-800">Selamat Datang Kembali, <span
                            class="text-dishub-blue-800">{{ Auth::user()->name }}</span>!</h3>
                    <p class="text-gray-500">Ini adalah ringkasan utama dari seluruh aktivitas sistem.
                    </p>
                </div>
            </div>

            {{-- Quick Access Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('super-admin.users.index') }}"
                    class="bg-gradient-to-br from-dishub-blue-700 to-dishub-blue-900 p-6 rounded-2xl shadow-xl text-white transition-transform hover:scale-105 flex items-center gap-4">
                    <div class="bg-white/20 p-3 rounded-xl"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg></div>
                    <div>
                        <h3 class="font-bold text-lg">Manajemen Pengguna</h3>
                        <p class="text-sm opacity-80">Kelola semua akun staf</p>
                    </div>
                </a>
                <a href="{{ route('super-admin.reports.index') }}"
                    class="bg-gradient-to-br from-gray-700 to-gray-900 p-6 rounded-2xl shadow-xl text-white transition-transform hover:scale-105 flex items-center gap-4">
                    <div class="bg-white/20 p-3 rounded-xl"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg></div>
                    <div>
                        <h3 class="font-bold text-lg">Manajemen Laporan</h3>
                        <p class="text-sm opacity-80">Lihat & kelola semua laporan</p>
                    </div>
                </a>
                <a href="{{ route('super-admin.settings.index') }}"
                    class="bg-gradient-to-br from-dishub-yellow-400 to-dishub-yellow-600 p-6 rounded-2xl shadow-xl text-white transition-transform hover:scale-105 flex items-center gap-4">
                    <div class="bg-white/20 p-3 rounded-xl"><svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg></div>
                    <div>
                        <h3 class="font-bold text-lg">Pengaturan Aplikasi</h3>
                        <p class="text-sm opacity-80">Konfigurasi sistem & API</p>
                    </div>
                </a>
            </div>

            {{-- Grafik --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Distribusi Role Pengguna</h3>
                    <div class="h-80"><canvas id="userRoleChart"></canvas></div>
                </div>
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Distribusi Status Laporan</h3>
                    <div class="h-80"><canvas id="reportStatusChart"></canvas></div>
                </div>
            </div>

            {{-- Aktivitas Terbaru --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900">Aktivitas Laporan Terbaru</h3>
                        <div class="mt-4 flow-root">
                            <ul role="list" class="-mb-8">
                                @forelse ($latestReports as $report)
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
                                    <p class="text-sm text-center text-gray-500 py-4">Belum ada aktivitas
                                        laporan
                                        terbaru.</p>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-2xl">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900">Pengguna Baru Terdaftar</h3>
                        <div class="mt-4 space-y-3">
                            @forelse ($latestUsers as $newUser)
                                <div class="p-3 rounded-lg hover:bg-slate-50 border flex justify-between items-center">
                                    <div class="flex items-center gap-3">
                                        <img class="h-10 w-10 rounded-full object-cover"
                                            src="{{ $newUser->image ? Storage::url($newUser->image) : 'https://ui-avatars.com/api/?name=' . urlencode($newUser->name) . '&background=EBF4FF&color=76A9FA' }}"
                                            alt="{{ $newUser->name }}">
                                        <div>
                                            <p class="font-medium text-slate-800">{{ $newUser->name }}</p>
                                            <p class="text-xs text-slate-500 capitalize">{{ $newUser->role->name }} -
                                                {{ $newUser->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('super-admin.users.show', $newUser) }}"
                                        class="text-slate-400 hover:text-slate-700" title="Lihat Detail">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                            @empty
                                <p class="text-center text-sm text-slate-500 py-4">Tidak ada pengguna baru.</p>
                            @endforelse
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
                // Grafik Role Pengguna
                const userRoleCtx = document.getElementById('userRoleChart')?.getContext('2d');
                if (userRoleCtx) {
                    new Chart(userRoleCtx, {
                        type: 'doughnut',
                        data: {
                            labels: @json($userRoleChart['labels']),
                            datasets: [{
                                data: @json($userRoleChart['values']),
                                backgroundColor: ['#1E3A8A', '#3B82F6', '#F59E0B', '#10B981',
                                    '#6B7280'
                                ], // Biru Tua, Biru, Oranye, Hijau, Abu-abu
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }

                // Grafik Status Laporan
                const reportStatusCtx = document.getElementById('reportStatusChart')?.getContext('2d');
                if (reportStatusCtx) {
                    new Chart(reportStatusCtx, {
                        type: 'bar',
                        data: {
                            labels: @json($reportStatusChart['labels']),
                            datasets: [{
                                label: 'Jumlah Laporan',
                                data: @json($reportStatusChart['values']),
                                backgroundColor: ['#FBBF24', '#10B981', '#F59E0B', '#3B82F6',
                                    '#EF4444'
                                ], // Kuning, Hijau, Oranye, Biru, Merah
                                borderRadius: 5,
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
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
