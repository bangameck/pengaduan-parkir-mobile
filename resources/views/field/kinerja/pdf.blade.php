<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Laporan Kinerja - {{ $officer->name }}</title>
        <style>
            @page {
                margin: 25px;
            }

            body {
                font-family: 'Helvetica', 'Arial', sans-serif;
                color: #333;
                font-size: 11px;
            }

            .header-container {
                border-bottom: 2px solid #FBBF24;
                /* Kuning */
                padding-bottom: 15px;
                margin-bottom: 20px;
            }

            .header-container table {
                width: 100%;
            }

            .header-container .logo {
                width: 70px;
                height: 70px;
            }

            .header-container .app-title {
                text-align: right;
            }

            .header-container h1 {
                margin: 0;
                font-size: 24px;
                color: #1E3A8A;
                /* Biru Tua */
                font-weight: bold;
            }

            .header-container p {
                margin: 5px 0 0;
                color: #666;
                font-size: 12px;
            }

            .profile-card {
                background-color: #F9FAFB;
                border: 1px solid #E5E7EB;
                border-radius: 12px;
                padding: 20px;
                margin-bottom: 25px;
                page-break-inside: avoid;
            }

            .profile-card table {
                width: 100%;
            }

            .profile-card .avatar {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                border: 3px solid #DBEAFE;
                /* Biru Muda */
                vertical-align: top;
            }

            .profile-card .details {
                padding-left: 20px;
            }

            .profile-card h2 {
                margin: 0 0 5px;
                font-size: 20px;
                color: #1E3A8A;
            }

            .profile-card .role {
                display: inline-block;
                background-color: #DBEAFE;
                color: #1E40AF;
                padding: 4px 10px;
                border-radius: 9999px;
                font-size: 10px;
                font-weight: bold;
                text-transform: capitalize;
            }

            .report-summary {
                margin-bottom: 20px;
            }

            .report-summary h3 {
                font-size: 16px;
                border-bottom: 1px solid #ddd;
                padding-bottom: 5px;
                margin-bottom: 10px;
            }

            .main-table {
                width: 100%;
                border-collapse: collapse;
            }

            .main-table th,
            .main-table td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: left;
            }

            .main-table th {
                background-color: #1E3A8A;
                color: white;
                font-size: 10px;
                text-transform: uppercase;
            }

            .footer {
                position: fixed;
                bottom: 0;
                left: 25px;
                right: 25px;
                text-align: center;
                font-size: 9px;
                color: #999;
            }

            .total-summary {
                margin-top: 20px;
                text-align: right;
                font-size: 14px;
                font-weight: bold;
                background-color: #F3F4F6;
                padding: 10px;
                border-radius: 8px;
            }
        </style>
    </head>

    <body>

        @php
            // Mengubah gambar menjadi base64 agar pasti ter-render di PDF
            $logoPath = public_path('logo-parkir.png');
            $logoData = '';
            if (file_exists($logoPath)) {
                $logoData = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
            }

            $avatarPath = $officer->image ? storage_path('app/public/' . $officer->image) : null;
            $avatarData = '';
            if ($avatarPath && file_exists($avatarPath)) {
                $type = pathinfo($avatarPath, PATHINFO_EXTENSION);
                $avatarData = 'data:image/' . $type . ';base64,' . base64_encode(file_get_contents($avatarPath));
            } else {
                // Fallback jika tidak ada foto profil
                $avatarData =
                    'https://ui-avatars.com/api/?name=' .
                    urlencode($officer->name) .
                    '&background=1E3A8A&color=FFFFFF&size=128';
            }
        @endphp

        <div class="header-container">
            <table>
                <tr>
                    <td>
                        @if ($logoData)
                            <img src="{{ $logoData }}" alt="Logo" class="logo">
                        @endif
                    </td>
                    <td class="app-title">
                        <h1>Laporan Kinerja Petugas</h1>
                        <p>{{ config('app_name', 'Aplikasi Pengaduan Parkir') }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="profile-card">
            <table>
                <tr>
                    <td style="width: 90px;">
                        <img src="{{ $avatarData }}" alt="Avatar" class="avatar">
                    </td>
                    <td class="details">
                        <h2>{{ $officer->name }}</h2>
                        <span class="role">{{ str_replace('-', ' ', $officer->role->name) }}</span>
                        <p style="margin-top: 10px; color: #666;">
                            {{ $officer->username }} | {{ $officer->email }} |
                            {{ $officer->phone_number ?? 'No. Telepon tidak tersedia' }}
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="report-summary">
            <h3>Detail Kinerja Periode: {{ $selectedPeriod }}</h3>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 20%;">Kode Laporan</th>
                    <th>Judul Laporan</th>
                    <th style="width: 25%;">Tanggal Selesai</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($followUps as $index => $followUp)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>#{{ $followUp->report->report_code }}</td>
                        <td>{{ $followUp->report->title }}</td>
                        <td>{{ $followUp->created_at->isoFormat('dddd, D MMMM YYYY - HH:mm') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px;">
                            Tidak ada data laporan yang ditangani pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="total-summary">
            Total Laporan Ditangani: {{ $totalReports }}
        </div>

        <div class="footer">
            Dokumen ini digenerate secara otomatis oleh sistem pada {{ now()->isoFormat('D MMMM YYYY, HH:mm:ss') }}
        </div>

    </body>

</html>
