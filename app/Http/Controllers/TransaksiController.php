<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TransaksiController extends Controller
{
    /**
     * Tampilkan antarmuka POS Transaksi Kasir
     */
    public function index(): View
    {
        $menus = Menu::where('is_active', true)->get();

        $categoryCounts = [
            'all' => $menus->count(),
            'coffee' => $menus->where('kategori', 'coffee')->count(),
            'non-coffee' => $menus->where('kategori', 'non-coffee')->count(),
            'makanan' => $menus->where('kategori', 'makanan')->count(),
            'snack' => $menus->where('kategori', 'snack')->count(),
        ];

        // Next auto order number
        $todayCount = Transaksi::whereDate('created_at', Carbon::today())->count() + 1;
        $orderCode = sprintf('#ORD-%04d', $todayCount);

        return view('transaksi.index', compact('menus', 'categoryCounts', 'orderCode'));
    }

    /**
     * Simpan transaksi baru dari kasir
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'string'], // JSON payload dari cart JS
            'nama_pelanggan' => ['nullable', 'string', 'max:100'],
            'nomor_meja' => ['nullable', 'string', 'max:50'],
            'tipe_pesanan' => ['required', 'in:dine-in,takeaway'],
            'metode_pembayaran' => ['required', 'in:tunai,qris,debit'],
            'bayar' => ['required', 'numeric', 'min:0'],
        ]);

        $cartItems = json_decode($validated['items'], true);

        if (!is_array($cartItems) || empty($cartItems)) {
            return back()->with('error', 'Keranjang belanja masih kosong! Silakan pilih menu terlebih dahulu.');
        }

        DB::beginTransaction();
        try {
            // Hitung subtotal aktual dari database (mencegah manipulasi harga di sisi klien)
            $subtotal = 0;
            $itemsToInsert = [];

            foreach ($cartItems as $item) {
                $menu = Menu::lockForUpdate()->find($item['id']);
                if (!$menu) {
                    throw new \Exception("Menu ID {$item['id']} tidak ditemukan.");
                }

                $qty = (int) $item['qty'];
                if ($qty <= 0) continue;

                if ($menu->stok < $qty) {
                    throw new \Exception("Stok untuk \"{$menu->nama}\" tidak mencukupi (tersedia: {$menu->stok}, diminta: {$qty}).");
                }

                $itemSubtotal = $menu->harga_jual * $qty;
                $subtotal += $itemSubtotal;

                // Potong stok
                $menu->decrement('stok', $qty);

                $itemsToInsert[] = [
                    'menu_id' => $menu->id,
                    'nama_menu' => $menu->nama,
                    'harga' => $menu->harga_jual,
                    'qty' => $qty,
                    'catatan' => $item['catatan'] ?? null,
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Hitung Pajak Restoran PB1 dari config
            $taxRate = (float) config('pos.tax_rate', 0.10);
            $pajak = round($subtotal * $taxRate, 2);
            $diskon = 0;
            $totalHarga = $subtotal + $pajak - $diskon;

            $bayar = (float) $validated['bayar'];
            if ($validated['metode_pembayaran'] === 'tunai' && $bayar < $totalHarga) {
                throw new \Exception("Uang pembayaran kurang! Total: Rp " . number_format($totalHarga, 0, ',', '.') . ", Diterima: Rp " . number_format($bayar, 0, ',', '.'));
            }

            // Jika non-tunai, jumlah bayar disesuaikan dengan total
            if ($validated['metode_pembayaran'] !== 'tunai') {
                $bayar = $totalHarga;
            }

            $kembalian = max(0, $bayar - $totalHarga);

            // Generate Kode Transaksi Unik: TRX-YYYYMMDD-XXXX
            $datePrefix = date('Ymd');
            $dailySequence = Transaksi::whereDate('created_at', Carbon::today())->count() + 1;
            $kodeTransaksi = sprintf('TRX-%s-%04d', $datePrefix, $dailySequence);

            $transaksi = Transaksi::create([
                'kode_transaksi' => $kodeTransaksi,
                'user_id' => Auth::id() ?? 1,
                'nama_pelanggan' => $validated['nama_pelanggan'] ?: 'Pelanggan Umum',
                'nomor_meja' => $validated['nomor_meja'] ?: ($validated['tipe_pesanan'] === 'takeaway' ? 'Takeaway' : 'Meja Bebas'),
                'tipe_pesanan' => $validated['tipe_pesanan'],
                'subtotal' => $subtotal,
                'pajak' => $pajak,
                'diskon' => $diskon,
                'total_harga' => $totalHarga,
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'bayar' => $bayar,
                'kembalian' => $kembalian,
                'status' => 'selesai',
            ]);

            foreach ($itemsToInsert as $itemData) {
                $itemData['transaksi_id'] = $transaksi->id;
                TransaksiItem::create($itemData);
            }

            DB::commit();

            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                $updatedMenus = Menu::where('is_active', true)->get();
                return response()->json([
                    'success' => true,
                    'message' => "Transaksi {$kodeTransaksi} berhasil diproses!",
                    'transaksi' => [
                        'id' => $transaksi->id,
                        'kode_transaksi' => $kodeTransaksi,
                        'nama_pelanggan' => $transaksi->nama_pelanggan,
                        'nomor_meja' => $transaksi->nomor_meja,
                        'tipe_pesanan' => $transaksi->tipe_pesanan,
                        'metode_pembayaran' => strtoupper($transaksi->metode_pembayaran),
                        'subtotal' => $transaksi->subtotal,
                        'pajak' => $transaksi->pajak,
                        'total_harga' => $transaksi->total_harga,
                        'bayar' => $transaksi->bayar,
                        'kembalian' => $transaksi->kembalian,
                        'waktu' => $transaksi->created_at->format('d/m/Y H:i:s'),
                        'struk_url' => route('laporan.struk', $transaksi->id),
                        'laporan_url' => route('laporan.index', ['highlight' => $transaksi->id]),
                    ],
                    'menus' => $updatedMenus,
                ]);
            }

            return redirect()->route('transaksi.index')
                ->with('transaksi_sukses', [
                    'id' => $transaksi->id,
                    'kode' => $kodeTransaksi,
                    'pelanggan' => $transaksi->nama_pelanggan,
                    'meja' => $transaksi->nomor_meja,
                    'metode' => strtoupper($transaksi->metode_pembayaran),
                    'total' => $transaksi->total_harga,
                    'bayar' => $transaksi->bayar,
                    'kembalian' => $transaksi->kembalian,
                    'struk_url' => route('laporan.struk', $transaksi->id),
                    'laporan_url' => route('laporan.index', ['highlight' => $transaksi->id]),
                ])
                ->with('success', "Transaksi {$kodeTransaksi} berhasil diproses! Struk siap dicetak.");
        } catch (\Throwable $e) {
            DB::rollBack();
            if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
