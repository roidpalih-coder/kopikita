<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman Dashboard Utama
     */
    public function index(Request $request): View
    {
        $today = Carbon::today();

        // 1. Metrik Kartu Atas
        $totalPenjualanHariIni = Transaksi::whereDate('created_at', $today)
            ->where('status', 'selesai')
            ->sum('total_harga');

        $penjualanKemarin = Transaksi::whereDate('created_at', Carbon::yesterday())
            ->where('status', 'selesai')
            ->sum('total_harga');

        $persenPerubahan = $penjualanKemarin > 0
            ? round((($totalPenjualanHariIni - $penjualanKemarin) / $penjualanKemarin) * 100, 1)
            : 0;

        $jumlahTransaksiHariIni = Transaksi::whereDate('created_at', $today)
            ->where('status', 'selesai')
            ->count();

        $transaksiKemarin = Transaksi::whereDate('created_at', Carbon::yesterday())
            ->where('status', 'selesai')
            ->count();
        $selisihTransaksi = $jumlahTransaksiHariIni - $transaksiKemarin;

        $menuTerdaftar = Menu::where('is_active', true)->count();
        $kategoriCount = Menu::where('is_active', true)->distinct('kategori')->count('kategori');
        $stokKritisCount = Menu::where('is_active', true)
            ->where('stok', '<=', config('pos.low_stock_threshold', 15))
            ->count();

        $pesananTerlayani = (int) TransaksiItem::whereHas('transaksi', function ($q) use ($today) {
            $q->whereDate('created_at', $today)->where('status', 'selesai');
        })->sum('qty');

        $rataRataItemPerTrx = $jumlahTransaksiHariIni > 0
            ? round($pesananTerlayani / $jumlahTransaksiHariIni, 1)
            : 0;

        // 2. Data Grafik Penjualan Harian per Jam (08:00 - 22:00)
        $hourlyLabels = [];
        $hourlyData = [];
        for ($hour = 8; $hour <= 22; $hour++) {
            $label = sprintf('%02d', $hour);
            $hourlyLabels[] = $label;

            $start = Carbon::parse($today)->setHour($hour)->startOfHour();
            $end = Carbon::parse($today)->setHour($hour)->endOfHour();

            $sum = Transaksi::where('status', 'selesai')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_harga');

            $hourlyData[] = (int) $sum;
        }

        // 2b. Data Grafik Penjualan Mingguan (Senin - Minggu)
        $startOfWeek = Carbon::now()->startOfWeek();
        $weeklyLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $weeklyData = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $sum = Transaksi::whereDate('created_at', $day->format('Y-m-d'))
                ->where('status', 'selesai')
                ->sum('total_harga');
            $weeklyData[] = (int) $sum;
        }

        // 2c. Data Grafik Penjualan Bulanan (Per Minggu)
        $monthlyLabels = ['Mgg 1 (1-7)', 'Mgg 2 (8-14)', 'Mgg 3 (15-21)', 'Mgg 4 (22-28)', 'Mgg 5 (29+)'];
        $monthlyData = [];
        $ranges = [[1, 7], [8, 14], [15, 21], [22, 28], [29, 31]];
        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        $daysInMonth = Carbon::now()->daysInMonth;
        foreach ($ranges as $range) {
            if ($range[0] > $daysInMonth) {
                $monthlyData[] = 0;
                continue;
            }
            $startDay = Carbon::create($year, $month, $range[0])->startOfDay();
            $endDay = Carbon::create($year, $month, min($range[1], $daysInMonth))->endOfDay();
            $sum = Transaksi::whereBetween('created_at', [$startDay, $endDay])
                ->where('status', 'selesai')
                ->sum('total_harga');
            $monthlyData[] = (int) $sum;
        }

        // 3. 5 Menu Terlaris (Favorit Pelanggan)
        $topMenus = TransaksiItem::select('menu_id', 'nama_menu', DB::raw('SUM(qty) as total_terjual'), DB::raw('SUM(subtotal) as total_nominal'))
            ->with('menu')
            ->groupBy('menu_id', 'nama_menu')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        // 4. Ringkasan Pesanan Terbaru
        $recentTransactions = Transaksi::with(['user', 'items'])
            ->latest()
            ->limit(6)
            ->get();

        return view('dashboard.index', compact(
            'totalPenjualanHariIni',
            'penjualanKemarin',
            'persenPerubahan',
            'jumlahTransaksiHariIni',
            'selisihTransaksi',
            'menuTerdaftar',
            'kategoriCount',
            'stokKritisCount',
            'pesananTerlayani',
            'rataRataItemPerTrx',
            'hourlyLabels',
            'hourlyData',
            'weeklyLabels',
            'weeklyData',
            'monthlyLabels',
            'monthlyData',
            'topMenus',
            'recentTransactions'
        ));
    }
}
