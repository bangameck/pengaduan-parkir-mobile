{{--
    File: resources/views/public/partials/report-card.blade.php
    Deskripsi: Kartu laporan dinamis yang bisa menampilkan berbagai status.
--}}
@php
    $statusText = str_replace('_', ' ', $report->status); // Mengubah 'in_progress' menjadi 'in progress'
@endphp

<a href="{{ route('public.laporan.show', $report) }}">
    <div class="bg-white rounded-lg shadow-md border border-gray-200 flex flex-col overflow-hidden group">
        <a href="{{ route('public.laporan.show', $report) }}" class="block relative">
            <img class="h-40 w-full object-cover transition-transform duration-300 group-hover:scale-105"
                src="{{ $report->images->first() ? ($report->images->first()->thumbnail_path ? Storage::url($report->images->first()->thumbnail_path) : Storage::url($report->images->first()->file_path)) : 'https://via.placeholder.com/400x300.png/EBF4FF/76A9FA?text=No+Image' }}"
                alt="Dokumentasi Laporan">
            @php
                $statusClasses = [
                    'verified' => 'bg-blue-500',
                    'in_progress' => 'bg-orange-500',
                    'completed' => 'bg-green-500',
                    'rejected' => 'bg-red-500',
                    'pending' => 'bg-yellow-500',
                ];
            @endphp
            <span
                class="absolute top-2 right-2 text-white text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusClasses[$report->status] ?? 'bg-gray-500' }} capitalize">
                {{ str_replace('_', ' ', $report->status) }}
            </span>
        </a>
        <div class="p-4 flex flex-col flex-grow">
            <p class="text-xs text-gray-500">#{{ $report->report_code }}</p>
            <h4 class="font-bold text-gray-800 leading-tight truncate mt-1">
                {{ $report->title }}</h4>
            <p class="text-sm text-gray-600 mt-1">oleh
                {{ $report->reportName }} via {{ ucfirst($report->source) }}
            </p>
            <div class="mt-auto pt-4 flex justify-between items-center">
                <p class="text-xs text-gray-400">
                    {{ $report->created_at->diffForHumans() }}</p>
                <div class="flex items-center gap-2">
                    <a href="#" title="Profil Pelapor Resident">
                        <img class="h-6 w-6 rounded-full object-cover transition-transform duration-200 hover:scale-110"
                            src="{{ $report->resident->image ? Storage::url($report->resident->image) : 'https://ui-avatars.com/api/?name=' . urlencode($report->resident->name) . '&background=EBF4FF&color=1E40AF&size=64&bold=true' }}"
                            alt="Avatar Resident">
                    </a>

                    {{-- Hanya tampilkan detail di tab lain --}}
                    <a href="{{ route('public.laporan.show', $report) }}" class="text-slate-600 hover:text-slate-900"
                        title="Lihat Detail">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                    </a>
                    {{-- Form tersembunyi untuk Aksi --}}
                    <form id="verify-form-{{ $report->id }}" action="{{ route('admin.laporan.verify', $report) }}"
                        method="POST" class="hidden">@csrf</form>
                    <form id="reject-form-{{ $report->id }}" action="{{ route('admin.laporan.reject', $report) }}"
                        method="POST" class="hidden">
                        @csrf
                        <input type="hidden" name="rejection_reason" id="rejection-reason-{{ $report->id }}">
                    </form>
                </div>
            </div>
        </div>
    </div>
</a>
