<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Laporan Penjualan — Kopi Kita</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #333;
            margin: 15mm;
        }
        .header {
            border-bottom: 2px solid #f97316;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #0f172a;
            margin: 0 0 4px 0;
            font-size: 18pt;
        }
        .header p {
            margin: 0;
            color: #64748b;
            font-size: 9pt;
        }
        .stat-grid {
            margin-bottom: 20px;
            width: 100%;
        }
        .stat-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px 14px;
            border-radius: 6px;
        }
        .stat-box .label {
            font-size: 8pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .stat-box .val {
            font-size: 14pt;
            font-weight: bold;
            color: #0f172a;
            margin-top: 4px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 9pt;
        }
        table.data-table th {
            background-color: #0f172a;
            color: #ffffff;
            text-align: left;
            padding: 8px 10px;
            font-size: 8.5pt;
        }
        table.data-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        table.data-table tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .footer {
            margin-top: 30px;
            font-size: 8pt;
            color: #94a3b8;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ strtoupper(config('pos.short_name', 'KOPI KITA')) }} — LAPORAN PENJUALAN</h1>
        <p>Alamat: {{ config('pos.address', 'Jl. Pemuda No. 88, Pati, Jawa Tengah') }} &bull; Telepon: {{ config('pos.phone', '(0295) 381920') }}</p>
        <p style="margin-top: 4px;"><strong>Periode Laporan:</strong> {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</p>
    </div>

    <!-- Metrik Ringkas -->
    <table class="stat-grid">
        <tr>
            <td style="width: 33%; padding-right: 8px;">
                <div class="stat-box">
                    <div class="label">Total Omzet Penjualan</div>
                    <div class="val" style="color: #ea580c;">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
                </div>
            </td>
            <td style="width: 33%; padding: 0 4px;">
                <div class="stat-box">
                    <div class="label">Total Transaksi</div>
                    <div class="val">{{ $transaksis->count() }} Struk</div>
                </div>
            </td>
            <td style="width: 33%; padding-left: 8px;">
                <div class="stat-box">
                    <div class="label">Total Porsi Terjual</div>
                    <div class="val">{{ $totalQty }} Items</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabel Data Transaksi -->
    <h3 style="font-size: 11pt; margin-bottom: 8px; color: #0f172a;">Rincian Transaksi Selesai</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">ID Transaksi</th>
                <th style="width: 15%;">Waktu</th>
                <th style="width: 15%;">Kasir</th>
                <th style="width: 15%;">Pelanggan / Meja</th>
                <th style="width: 15%;" class="text-right">Total (Rp)</th>
                <th style="width: 15%;" class="text-center">Metode</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksis as $idx => $trx)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td class="bold">#{{ $trx->kode_transaksi }}</td>
                <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $trx->user->name ?? '-' }}</td>
                <td>{{ $trx->nomor_meja }} ({{ $trx->nama_pelanggan }})</td>
                <td class="text-right bold">{{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                <td class="text-center">{{ strtoupper($trx->metode_pembayaran) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="5" style="text-align: right; padding: 10px;">TOTAL KESELURUHAN:</td>
                <td class="text-right" style="color: #ea580c; font-size: 10pt;">
                    Rp {{ number_format($totalOmzet, 0, ',', '.') }}
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d F Y, H:i:s') }} WIB &bull; Sistem POS {{ config('pos.short_name', 'Kopi Kita') }} &bull; {{ config('pos.institution', 'SMK Tunas Harapan Pati') }}
    </div>

</body>
</html>
