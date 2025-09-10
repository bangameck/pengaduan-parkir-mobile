{{--
    File: resources/views/layouts/navigation.blade.php
    Deskripsi: Navigasi sidebar dinamis berdasarkan role pengguna.
--}}
<nav class="mt-10 flex-1 px-2 space-y-1">

    {{-- MENU UMUM (Untuk semua admin) --}}
    @can('view-internal-dashboard')
        <x-nav-link-side :href="route('dashboard')" :active="request()->routeIs(['dashboard', 'leader.dashboard'])">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
            </svg>
            <span class="mx-3">Dashboard</span>
        </x-nav-link-side>
    @endcan

    {{-- MENU ADMIN OFFICER --}}
    @can('view-admin-officer-menu')
        <div class="px-2 pt-4">
            <div class="px-4 py-2 mt-2 text-xs font-semibold uppercase text-gray-400">
                Tugas Staf
            </div>
            <x-nav-link-side href="{{ route('admin.laporan.index') }}" :active="request()->routeIs(['admin.laporan.index', 'admin.laporan.create'])">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span class="mx-3">Manajemen Laporan</span>
            </x-nav-link-side>
            <x-nav-link-side href="{{ route('admin.laporan.rekap') }}" :active="request()->routeIs('admin.laporan.rekap')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
                <span class="mx-3">Rekap Laporan</span>
            </x-nav-link-side>
        </div>
    @endcan

    {{-- MENU FIELD OFFICER --}}
    @can('view-field-officer-menu')
        <div class="px-2 pt-4">
            <div class="px-4 py-2 mt-2 text-xs font-semibold uppercase text-gray-400">
                Tugas Lapangan
            </div>
            <x-nav-link-side href="{{ route('petugas.tugas.index') }}" :active="request()->routeIs('petugas.tugas.*')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 16.382V5.618a1 1 0 00-1.447-.894L15 7m-6 10h6M9 7h6">
                    </path>
                </svg>
                <span class="mx-3">Daftar Tugas</span>
            </x-nav-link-side>
            <x-nav-link-side href="{{ route('petugas.kinerja.index') }}" :active="request()->routeIs('petugas.kinerja.*')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                <span class="mx-3">Laporan Kinerja</span>
            </x-nav-link-side>
        </div>
    @endcan

    {{-- MENU LEADER --}}
    @can('view-leader-menu')
        <div class="px-2 pt-4">
            <div class="px-4 py-2 mt-2 text-xs font-semibold uppercase text-gray-400">
                Kepemimpinan
            </div>
            <x-nav-link-side :href="route('leader.team.assignment')" :active="request()->routeIs('leader.team.assignment')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                </svg>

                <span class="mx-3">Penugasan</span>
            </x-nav-link-side>
            <x-nav-link-side :href="route('leader.team.management')" :active="request()->routeIs(['leader.team.management', 'leader.team.show'])">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                <span class="mx-3">Manajemen Tim</span>
            </x-nav-link-side>
        </div>
    @endcan

    {{-- MENU SUPER ADMIN --}}
    @can('view-super-admin-menu')
        <div class="px-2 pt-4">
            <div class="px-4 py-2 mt-2 text-xs font-semibold uppercase text-gray-400">
                Administrasi Sistem
            </div>
            <x-nav-link-side href="{{ route('super-admin.reports.index') }}" :active="request()->routeIs('super-admin.reports.*')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                    </path>
                </svg>
                <span class="mx-3">Manajemen Laporan</span>
            </x-nav-link-side>
            <x-nav-link-side :href="route('super-admin.users.index')" :active="request()->routeIs('super-admin.users.*')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197m0 0A10.99 10.99 0 0112 10c2.25 0 4.33.78 6 2.083M12 12a4 4 0 110-8 4 4 0 010 8z">
                    </path>
                </svg>
                <span class="mx-3">Manajemen Pengguna</span>
            </x-nav-link-side>
            <x-nav-link-side :href="route('super-admin.settings.index')" :active="request()->routeIs('super-admin.settings.*')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.096 2.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <span class="mx-3">Pengaturan Aplikasi</span>
            </x-nav-link-side>
        </div>
    @endcan

</nav>
