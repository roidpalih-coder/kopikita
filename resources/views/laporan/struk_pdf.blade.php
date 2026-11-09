<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #{{ $transaksi->kode_transaksi }}</title>
    <style>
        @page {
            margin: 5mm;
            size: 80mm 200mm;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 10pt;
            color: #111;
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        .dashed-line {
            border-bottom: 1px dashed #666;
            margin: 6px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 2px 0;
            vertical-align: top;
        }
        .store-title {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .small {
            font-size: 8.5pt;
            color: #444;
        }
    </style>
</head>
<body>

    <!-- Header Cafe -->
    <div class="text-center">
        <div class="store-title">{{ strtoupper(config('pos.name', 'KOPI KITA CAFE & ROASTERY')) }}</div>
        <div class="small">{{ config('pos.address', 'Jl. Pemuda No. 88, Pati, Jawa Tengah') }}</div>
        <div class="small">Telp: {{ config('pos.phone', '(0295) 381920') }}</div>
    </div>

    <div class="dashed-line"></div>

    <!-- Metadata Transaksi -->
    <table class="small">
        <tr>
            <td style="width: 35%;">No. Trx</td>
            <td>: #{{ $transaksi->kode_transaksi }}</td>
        </tr>
        <tr>
            <td>Waktu</td>
            <td>: {{ $transaksi->created_at->format('d/m/Y H:i') }} WIB</td>
        </tr>
        <tr>
            <td>Kasir</td>
            <td>: {{ $transaksi->user->name ?? 'Kasir Utama' }}</td>
        </tr>
        <tr>
            <td>Tipe / Meja</td>
            <td>: {{ ucfirst($transaksi->tipe_pesanan) }} / {{ $transaksi->nomor_meja }}</td>
        </tr>
        <tr>
            <td>Pelanggan</td>
            <td>: {{ $transaksi->nama_pelanggan }}</td>
        </tr>
    </table>

    <div class="dashed-line"></div>

    <!-- Daftar Item -->
    <table>
        @foreach($transaksi->items as $item)
        <tr>
            <td colspan="2" class="bold" style="font-size: 9pt;">
                {{ $item->qty }}x {{ $item->nama_menu }}
            </td>
            <td class="text-right bold" style="font-size: 9pt;">
                {{ number_format($item->subtotal, 0, ',', '.') }}
            </td>
        </tr>
        <tr class="small">
            <td colspan="3" style="color: #666; padding-left: 10px;">
                @ {{ number_format($item->harga, 0, ',', '.') }}
                @if($item->catatan)
                    &bull; {{ $item->catatan }}
                @endif
            </td>
        </tr>
        @endforeach
    </table>

    <div class="dashed-line"></div>

    <!-- Rincian Pembayaran -->
    <table style="font-size: 9.5pt;">
        <tr>
            <td>Subtotal</td>
            <td class="text-right">Rp {{ number_format($transaksi->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>PB1 Cafe ({{ (int)(config('pos.tax_rate', 0.10) * 100) }}%)</td>
            <td class="text-right">Rp {{ number_format($transaksi->pajak, 0, ',', '.') }}</td>
        </tr>
        <tr class="bold" style="font-size: 10.5pt;">
            <td style="padding-top: 4px;">TOTAL BAYAR</td>
            <td class="text-right" style="padding-top: 4px;">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Bayar ({{ strtoupper($transaksi->metode_pembayaran) }})</td>
            <td class="text-right">Rp {{ number_format($transaksi->bayar, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembalian</td>
            <td class="text-right">Rp {{ number_format($transaksi->kembalian, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="dashed-line"></div>

    <!-- Footer -->
    <div class="text-center small" style="margin-top: 8px;">
        <div class="bold">Terima kasih atas kunjungan Anda!</div>
        <div style="font-size: 7.5pt; color: #555; margin-top: 2px;">Nikmati aroma kopi asli pilihan Nusantara.</div>
        <div style="font-size: 7.5pt; margin-top: 2px;">Instagram: @kopikita.id</div>
        <div style="margin-top: 6px; letter-spacing: 2px; font-size: 8pt;">
            * * * {{ $transaksi->kode_transaksi }} * * *
        </div>
    </div>

</body>
</html>
