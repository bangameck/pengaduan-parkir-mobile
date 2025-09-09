<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $reports;
    protected $period;

    public function __construct($reports, $period)
    {
        $this->reports = $reports;
        $this->period  = $period;
    }

    public function collection()
    {
        return $this->reports;
    }

    public function headings(): array
    {
        return [
            'Kode Laporan',
            'Judul',
            'Pelapor',
            'Kontak Pelapor',
            'Lokasi',
            'Status',
            'Tanggal Masuk',
            'Tanggal Selesai',
        ];
    }

    public function map($report): array
    {
        return [
            $report->report_code,
            $report->title,
            $report->resident->name,
            $report->source_contact,
            $report->location_address,
            ucfirst(str_replace('_', ' ', $report->status)),
            $report->created_at->isoFormat('D MMM YYYY, HH:mm'),
            $report->completed_at ? $report->completed_at->isoFormat('D MMM YYYY, HH:mm') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Membuat baris header menjadi tebal (bold)
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
    }
}
