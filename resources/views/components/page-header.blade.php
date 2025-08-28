{{--
    Component ini berfungsi sebagai sub-header untuk setiap halaman.
    Isi judul akan diambil dari slot default.
--}}
<div class="p-4 bg-white shadow-sm sticky top-0 z-10">
    <div class="flex items-center">
        {{-- Tombol ini akan mengarahkan user ke halaman yang mereka kunjungi sebelumnya --}}
        <a href="{{ url()->previous() }}"
            class="text-gray-500 p-2 -ml-2 rounded-full hover:bg-gray-100 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>

        {{-- Judul Halaman --}}
        <h1 class="text-lg font-bold text-gray-800 ml-2">
            {{ $slot }}
        </h1>
    </div>
</div>
