<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    /**
     * Tampilkan halaman Laporan Penjualan & Riwayat Transaksi
     */
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $kasirId = $request->input('kasir_id');
        $search = $request->input('search');

        $query = Transaksi::with(['user', 'items.menu'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('status', 'selesai');

        if ($kasirId && $kasirId !== 'all') {
            $query->where('user_id', $kasirId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('nomor_meja', 'like', "%{$search}%");
            });
        }

        // Clone query untuk kalkulasi metrik agregat
        $metrikQuery = clone $query;
        $totalOmzet = $metrikQuery->sum('total_harga');
        $totalTransaksi = $metrikQuery->count();
        $rataRataNilaiTrx = $totalTransaksi > 0 ? round($totalOmzet / $totalTransaksi) : 0;

        $produkTerjual = TransaksiItem::whereHas('transaksi', function ($q) use ($startDate, $endDate, $kasirId) {
            $q->whereDate('created_at', '>=', $startDate)
              ->whereDate('created_at', '<=', $endDate)
              ->where('status', 'selesai');
            if ($kasirId && $kasirId !== 'all') {
                $q->where('user_id', $kasirId);
            }
        })->sum('qty');

        // Komposisi Metode Pembayaran
        $metodeTunai = (clone $query)->where('metode_pembayaran', 'tunai')->sum('total_harga');
        $metodeQris = (clone $query)->where('metode_pembayaran', 'qris')->sum('total_harga');
        $metodeDebit = (clone $query)->where('metode_pembayaran', 'debit')->sum('total_harga');

        // Tren Penjualan Harian (7 hari terakhir dalam rentang)
        $trendLabels = [];
        $trendData = [];
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $diffDays = $start->diffInDays($end);

        // Ambil maksimal 14 titik hari
        $step = max(1, (int) ceil($diffDays / 14));
        $current = $start->copy();
        while ($current->lte($end)) {
            $dayStr = $current->format('Y-m-d');
            $trendLabels[] = $current->format('d M');
            $sumDay = Transaksi::whereDate('created_at', $dayStr)->where('status', 'selesai')->sum('total_harga');
            $trendData[] = (int) $sumDay;
            $current->addDays($step);
        }

        // List Transaksi dengan Pagination: urut dari transaksi terbaru ke terlama
        $transaksis = $query->orderByDesc('created_at')->orderByDesc('id')->paginate(10)->withQueryString();

        // Struk yang aktif ditampilkan di pratinjau kanan
        $highlightId = $request->input('highlight');
        $selectedTransaksi = null;
        if ($highlightId) {
            $selectedTransaksi = Transaksi::with(['user', 'items.menu'])->find($highlightId);
        }
        if (!$selectedTransaksi) {
            $selectedTransaksi = $transaksis->first() ?? Transaksi::with(['user', 'items.menu'])->orderByDesc('created_at')->orderByDesc('id')->first();
        }

        $kasirList = User::all();

        return view('laporan.index', compact(
            'transaksis',
            'selectedTransaksi',
            'totalOmzet',
            'totalTransaksi',
            'produkTerjual',
            'rataRataNilaiTrx',
            'metodeTunai',
            'metodeQris',
            'metodeDebit',
            'trendLabels',
            'trendData',
            'startDate',
            'endDate',
            'kasirId',
            'kasirList',
            'search'
        ));
    }

    /**
     * Cetak Struk Kasir Thermal (Ukuran Kertas 80mm / 58mm) via DomPDF
     */
    public function cetakStruk(Transaksi $transaksi): Response
    {
        $transaksi->load(['user', 'items.menu']);

        $pdf = Pdf::loadView('laporan.struk_pdf', compact('transaksi'))
            ->setPaper([0, 0, 226.77, 650], 'portrait'); // 80mm width thermal roll

        return $pdf->stream('Struk-' . $transaksi->kode_transaksi . '.pdf');
    }

    /**
     * Export Rekap Laporan Penjualan ke PDF
     */
    public function exportPdf(Request $request): Response
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $transaksis = Transaksi::with(['user', 'items'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('status', 'selesai')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $totalOmzet = $transaksis->sum('total_harga');
        $totalQty = $transaksis->flatMap->items->sum('qty');

        $pdf = Pdf::loadView('laporan.rekap_pdf', compact(
            'transaksis',
            'totalOmzet',
            'totalQty',
            'startDate',
            'endDate'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Penjualan-Kopi-Kita-' . $startDate . '-sd-' . $endDate . '.pdf');
    }

    /**
     * Export Riwayat Transaksi ke CSV
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $transaksis = Transaksi::with(['user'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where('status', 'selesai')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $fileName = 'transaksi_kopi_kita_' . $startDate . '_' . $endDate . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        return response()->stream(function () use ($transaksis) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Kode Transaksi', 'Tanggal & Waktu', 'Kasir', 'Pelanggan', 'Meja', 'Tipe', 'Subtotal', 'Pajak (10%)', 'Total', 'Metode Bayar']);

            foreach ($transaksis as $idx => $t) {
                fputcsv($handle, [
                    $idx + 1,
                    $t->kode_transaksi,
                    $t->created_at->format('Y-m-d H:i:s'),
                    $t->user->name ?? '-',
                    $t->nama_pelanggan ?? '-',
                    $t->nomor_meja ?? '-',
                    strtoupper($t->tipe_pesanan),
                    $t->subtotal,
                    $t->pajak,
                    $t->total_harga,
                    strtoupper($t->metode_pembayaran),
                ]);
            }
            fclose($handle);
        }, 200, $headers);
    }
}
