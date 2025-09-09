<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Verifikasi & Penugasan Laporan
            </h2>
             <a href="{{ route('leader.team.management') }}" class="mt-2 sm:mt-0 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
             <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

                {{-- KOLOM KIRI: DETAIL LAPORAN SEBAGAI REFERENSI --}}
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-24">
                        <div class="p-6 text-gray-900">
                             <h3 class="text-lg font-bold text-dishub-blue-800 border-b pb-3 mb-4">Detail Laporan #{{ $report->report_code }}</h3>
                            <div class="space-y-3 text-sm">
                                <p><strong class="w-24 inline-block text-gray-500">Judul:</strong> {{ $report->title }}</p>
                                <p><strong class="w-24 inline-block text-gray-500">Pelapor:</strong> {{ $report->resident->name }}</p>
                                <p><strong class="w-24 inline-block text-gray-500">Lokasi:</strong> {{ $report->location_address }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: FORM PENUGASAN --}}
                <div class="lg:col-span-3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <form method="POST" action="{{ route('leader.assignment.store', $report) }}"
                              x-data="{ isSubmitting: false }" @submit="isSubmitting = true">
                            @csrf
                            <div class="p-6 space-y-6">
                                <h3 class="text-lg font-bold text-gray-800">Verifikasi & Tugaskan ke Tim</h3>
                                <p class="text-sm text-gray-500 -mt-4">Pilih petugas yang akan dikirim notifikasi tugas untuk menangani laporan ini. Status laporan akan otomatis berubah menjadi "Terverifikasi".</p>

                                @if ($errors->any())
                                    <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4" role="alert">
                                        <p class="font-bold">Terjadi Kesalahan</p>
                                        <ul class="mt-1 list-disc list-inside text-sm">
                                            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if(session('error'))
                                    <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4" role="alert">
                                        <p class="font-bold">Error Sistem</p> <p>{{ session('error') }}</p>
                                    </div>
                                @endif

                                <div>
                                    <label for="tom-select-officers" class="block mb-2 text-sm font-medium text-gray-700">Petugas yang Ditugaskan</label>
                                    <select name="officer_ids[]" id="tom-select-officers" multiple required placeholder="Ketik untuk mencari petugas...">
                                        @foreach($fieldOfficers as $officer)
                                            <option value="{{ $officer->id }}" {{ in_array($officer->id, old('officer_ids', [])) ? 'selected' : '' }}>
                                                {{ $officer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center justify-end p-6 bg-gray-50 border-t">
                                <a href="{{ route('leader.team.management') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                                    Batal
                                </a>
                                <button type="submit" x-bind:disabled="isSubmitting"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-dishub-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-dishub-blue-700 disabled:opacity-50">
                                    <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-show="!isSubmitting">Verifikasi & Tugaskan</span>
                                    <span x-show="isSubmitting">Memproses...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new TomSelect('#tom-select-officers',{ plugins: ['remove_button'] });
            });
        </script>
    @endpush
</x-app-layout>
