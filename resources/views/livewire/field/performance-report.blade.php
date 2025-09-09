<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Laporan Kinerja Bulanan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Kinerja Anda sebagai Petugas Lapangan</h3>
                        <p class="text-sm text-gray-500">Pilih periode bulan untuk melihat dan mencetak laporan kinerja Anda.</p>
                    </div>
                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <select wire:model.live="selectedMonth" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full sm:w-auto">
                            @foreach($months as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <button wire:click="exportPdf" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 disabled:opacity-50">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span wire:loading.remove>Cetak PDF</span>
                            <span wire:loading>Mencetak...</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800">Ringkasan Kinerja - {{ $selectedPeriod }}</h3>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <p class="text-sm text-gray-600">Total Laporan Ditangani</p>
                            <p class="text-2xl font-bold">{{ $totalReports }}</p>
                        </div>
                    </div>

                    <h3 class="text-lg font-bold text-gray-800 mt-8 border-t pt-6">Detail Laporan yang Ditangani</h3>
                    <div class="mt-4 overflow-x-auto border rounded-lg">
                        <table class="min-w-full bg-white">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-semibold uppercase text-slate-600">Kode Laporan</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold uppercase text-slate-600">Judul</th>
                                    <th class="py-3 px-4 text-left text-xs font-semibold uppercase text-slate-600">Tanggal Selesai</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($followUps as $followUp)
                                    <tr class="hover:bg-slate-50 border-b">
                                        <td class="py-3 px-4 text-sm font-mono">#{{ $followUp->report->report_code }}</td>
                                        <td class="py-3 px-4 text-sm">{{ $followUp->report->title }}</td>
                                        <td class="py-3 px-4 text-sm">{{ $followUp->created_at->isoFormat('D MMM YYYY, HH:mm') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-6">Tidak ada data kinerja untuk periode ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
