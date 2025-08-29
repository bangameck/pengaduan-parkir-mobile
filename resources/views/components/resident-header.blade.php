<div class="p-4 sm:p-6 bg-white shadow-sm">
    <div class="flex justify-between items-center">
        {{-- Logo Aplikasi --}}
        <div>
            <a href="{{ route('dashboard') }}">
                <img src="{{ asset('logo-parkir.png') }}" alt="Logo ParkirPKU" class="h-10">
            </a>
        </div>

        {{-- Link Foto Profil (dengan Logika Dinamis) --}}
        <div>
            <a href="{{ route('profile.edit') }}" title="Edit Profil">
                <img class="w-12 h-12 rounded-full object-cover border-2 border-gray-200 hover:border-blue-500 transition"
                    src="{{ Auth::user()->image ? Storage::url(Auth::user()->image) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=EBF4FF&color=76A9FA' }}"
                    alt="Profile Picture">
            </a>
        </div>
    </div>
</div>
