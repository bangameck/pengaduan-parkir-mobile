 @php
     $notificationCount = $notificationCount ?? 0;
     $notifications = $notifications ?? collect();
 @endphp

 {{-- Header Publik --}}
 <header class="sticky top-0 z-50 backdrop-blur-lg bg-white/10 shadow-md border-b border-white/10">
     <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
         <div class="flex items-center justify-between h-16">

             {{-- Kiri: Logo + Text --}}
             <div class="flex items-center gap-3">
                 <a href="{{ route('home') }}" class="flex items-center gap-3">
                     <img src="{{ asset('logo-parkir.png') }}" alt="Logo ParkirPKU" class="h-10 sm:h-12 drop-shadow-md">
                     <span class="font-bold text-dishub-blue-800 tracking-wide text-lg">
                         SiParkir<span class="text-yellow-400">Kita</span>
                     </span>
                 </a>
             </div>

             {{-- Kanan: Avatar + Dropdown --}}
             <div class="relative" x-data="{ open: false }">
                 <button @click="open = !open" class="relative flex items-center focus:outline-none">
                     <img class="w-11 h-11 rounded-full border-2 border-white/30 shadow-md"
                         src="{{ Auth::user()->image ? Storage::url(Auth::user()->image) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=1E3A8A&color=fff' }}"
                         alt="Profile Picture">

                     {{-- 🔵 Badge Notif hanya untuk resident --}}
                     @if (Auth::user()->role->name === 'resident' && $notificationCount > 0)
                         <span
                             class="absolute -top-1 -right-1 flex items-center justify-center
                                     w-5 h-5 text-xs font-bold text-white
                                     bg-blue-600 rounded-full shadow-md animate-bounce">
                             {{ $notificationCount }}
                         </span>
                     @endif
                 </button>

                 {{-- Dropdown Menu --}}
                 <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="absolute right-0 mt-3 w-56 rounded-xl shadow-xl overflow-hidden
                            bg-white/95 backdrop-blur-lg border border-gray-200 z-50">

                     <div class="px-4 py-3 border-b">
                         <p class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</p>
                         <p class="text-xs text-gray-500">{{ '@' . Auth::user()->username }}</p>
                         <p class="text-xs text-gray-500">+{{ Auth::user()->phone_number }}</p>
                     </div>

                     <a href="{{ route('profile.edit') }}"
                         class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100">
                         ✏️ Edit Profil
                     </a>
                     <a href="{{ route('logout') }}" class="block px-4 py-3 text-sm text-red-600 hover:bg-red-100">
                         🚪 Logout
                     </a>
                 </div>
             </div>
         </div>
     </div>
 </header>
