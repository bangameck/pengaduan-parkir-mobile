<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tugas Laporan Lapangan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{-- Form Pencarian bisa di-copy dari view admin --}}
                    {{-- ... --}}

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Kode Laporan</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Judul</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Lokasi</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Tgl. Verifikasi</th>
                                    <th class="text-center py-3 px-4 uppercase font-semibold text-sm">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($reports as $report)
                                    <tr class="hover:bg-gray-50 border-b">
                                        <td class="py-3 px-4 font-mono text-xs">{{ $report->report_code }}</td>
                                        <td class="py-3 px-4">{{ Str::limit($report->title, 35) }}</td>
                                        <td class="py-3 px-4">{{ Str::limit($report->location_address, 40) }}</td>
                                        <td class="py-3 px-4">
                                            {{ $report->verified_at ? \Carbon\Carbon::parse($report->verified_at)->isoFormat('D MMM Y, HH:mm') : '-' }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            {{-- Tombol ini akan menuju form tindak lanjut --}}
                                            <a href="{{ route('admin.tugas.createFollowUp', $report) }}"
                                                class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-2 px-3 rounded">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-6 px-4">
                                            Tidak ada tugas laporan yang tersedia saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginasi --}}
                    <div class="mt-6">
                        {{ $reports->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
