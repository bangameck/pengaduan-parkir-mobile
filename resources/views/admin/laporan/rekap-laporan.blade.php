<!DOCTYPE html>
<html>

    <head>
        <title>Rekap Laporan</title>
        <style>
            body {
                font-family: 'Helvetica', sans-serif;
                font-size: 10px;
            }

            .header {
                text-align: center;
                margin-bottom: 20px;
            }

            .header h1 {
                margin: 0;
                font-size: 20px;
            }

            .header p {
                margin: 5px 0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 6px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }
        </style>
    </head>

    <body>
        <div class="header">
            <h1>Rekap Laporan Pengaduan</h1>
            <p>Periode: {{ $period }}</p>
        </div>
        <table class="main-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th>Pelapor</th>
                    <th>Status</th>
                    <th>Tgl. Masuk</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reports as $report)
                    <tr>
                        <td>#{{ $report->report_code }}</td>
                        <td>{{ $report->title }}</td>
                        <td>{{ $report->reportName }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $report->status)) }}</td>
                        <td>{{ $report->created_at->isoFormat('D MMM Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">Tidak ada data untuk periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </body>

</html>
