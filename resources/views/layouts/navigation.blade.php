<nav class="mt-10">
    <x-nav-link-side :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
        </svg>
        <span class="mx-3">Dashboard</span>
    </x-nav-link-side>

    {{-- Menggunakan route 'laporan.index' dan aktif jika route diawali 'laporan.' --}}
    <x-nav-link-side href="{{ route('admin.laporan.index') }}" :active="request()->routeIs('admin.laporan.*')">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
            </path>
        </svg>
        <span class="mx-3">Laporan Masuk</span>
    </x-nav-link-side>

    @can('manage-teams')
        {{-- Menggunakan route 'tim.index' dan aktif jika route diawali 'tim.' --}}
        <x-nav-link-side href="#" :active="request()->routeIs('tim.*')">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
            </svg>
            <span class="mx-3">Manajemen Tim</span>
        </x-nav-link-side>
    @endcan

    @can('view-super-admin-dashboard')
        <div class="px-6 py-2 mt-4 text-xs uppercase text-gray-400">
            Administrasi Sistem
        </div>

        <x-nav-link-side :href="route('super-admin.users.index')" :active="request()->routeIs('super-admin.users.*')">
            <span class="mx-3">Manajemen Pengguna</span>
        </x-nav-link-side>

        <x-nav-link-side href="#"> {{-- Nanti ke route('super-admin.settings.index') --}}
            <span class="mx-3">Pengaturan Aplikasi</span>
        </x-nav-link-side>
    @endcan

</nav>
