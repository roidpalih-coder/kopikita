<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransaksiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kasir = User::where('role', 'kasir')->first() ?? User::first();
        if (!$kasir) {
            return;
        }

        $kopiSusu = Menu::where('sku', 'KK-COF-001')->first();
        $croissant = Menu::where('sku', 'KK-SNK-001')->first();
        $macchiato = Menu::where('sku', 'KK-COF-003')->first();
        $americano = Menu::where('sku', 'KK-COF-002')->first();
        $matcha = Menu::where('sku', 'KK-NCO-001')->first();
        $burger = Menu::where('sku', 'KK-FD-001')->first();

        // 1. Transaksi #TRX-20251024-0088 (exact match from Mockup 5 Detail Struk)
        $trx1 = Transaksi::updateOrCreate(
            ['kode_transaksi' => 'TRX-20251024-0088'],
            [
                'user_id' => $kasir->id,
                'nama_pelanggan' => 'Budi Santoso',
                'nomor_meja' => 'Meja 07',
                'tipe_pesanan' => 'dine-in',
                'subtotal' => 80000,
                'pajak' => 8000,
                'diskon' => 0,
                'total_harga' => 88000,
                'metode_pembayaran' => 'qris',
                'bayar' => 88000,
                'kembalian' => 0,
                'status' => 'selesai',
                'created_at' => Carbon::now()->setHour(14)->setMinute(32),
            ]
        );

        $trx1->items()->delete();
        if ($kopiSusu) {
            TransaksiItem::create([
                'transaksi_id' => $trx1->id,
                'menu_id' => $kopiSusu->id,
                'nama_menu' => $kopiSusu->nama,
                'harga' => 18000,
                'qty' => 2,
                'catatan' => 'Less Sugar, Normal Ice',
                'subtotal' => 36000,
            ]);
        }
        if ($croissant) {
            TransaksiItem::create([
                'transaksi_id' => $trx1->id,
                'menu_id' => $croissant->id,
                'nama_menu' => $croissant->nama,
                'harga' => 20000,
                'qty' => 1,
                'catatan' => 'Hangatkan / Warm',
                'subtotal' => 20000,
            ]);
        }
        if ($macchiato) {
            TransaksiItem::create([
                'transaksi_id' => $trx1->id,
                'menu_id' => $macchiato->id,
                'nama_menu' => $macchiato->nama,
                'harga' => 24000,
                'qty' => 1,
                'catatan' => 'Hot, Regular',
                'subtotal' => 24000,
            ]);
        }

        // 2. Transaksi #TRX-20251024-0087
        $trx2 = Transaksi::updateOrCreate(
            ['kode_transaksi' => 'TRX-20251024-0087'],
            [
                'user_id' => $kasir->id,
                'nama_pelanggan' => 'Ayu Wardani',
                'nomor_meja' => 'Meja 02',
                'tipe_pesanan' => 'dine-in',
                'subtotal' => 49090,
                'pajak' => 4910,
                'diskon' => 0,
                'total_harga' => 54000,
                'metode_pembayaran' => 'tunai',
                'bayar' => 60000,
                'kembalian' => 6000,
                'status' => 'selesai',
                'created_at' => Carbon::now()->setHour(14)->setMinute(15),
            ]
        );
        $trx2->items()->delete();
        if ($americano) {
            TransaksiItem::create([
                'transaksi_id' => $trx2->id,
                'menu_id' => $americano->id,
                'nama_menu' => $americano->nama,
                'harga' => 15000,
                'qty' => 2,
                'catatan' => null,
                'subtotal' => 30000,
            ]);
        }
        if ($croissant) {
            TransaksiItem::create([
                'transaksi_id' => $trx2->id,
                'menu_id' => $croissant->id,
                'nama_menu' => $croissant->nama,
                'harga' => 20000,
                'qty' => 1,
                'catatan' => null,
                'subtotal' => 20000,
            ]);
        }

        // 3. Transaksi #TRX-20251024-0086
        $trx3 = Transaksi::updateOrCreate(
            ['kode_transaksi' => 'TRX-20251024-0086'],
            [
                'user_id' => $kasir->id,
                'nama_pelanggan' => 'Rian Nugroho',
                'nomor_meja' => 'Takeaway',
                'tipe_pesanan' => 'takeaway',
                'subtotal' => 32727,
                'pajak' => 3273,
                'diskon' => 0,
                'total_harga' => 36000,
                'metode_pembayaran' => 'qris',
                'bayar' => 36000,
                'kembalian' => 0,
                'status' => 'selesai',
                'created_at' => Carbon::now()->setHour(13)->setMinute(58),
            ]
        );
        $trx3->items()->delete();
        if ($kopiSusu) {
            TransaksiItem::create([
                'transaksi_id' => $trx3->id,
                'menu_id' => $kopiSusu->id,
                'nama_menu' => $kopiSusu->nama,
                'harga' => 18000,
                'qty' => 2,
                'catatan' => 'Bungkus terpisah',
                'subtotal' => 36000,
            ]);
        }

        // 4. Transaksi #TRX-20251024-0085
        $trx4 = Transaksi::updateOrCreate(
            ['kode_transaksi' => 'TRX-20251024-0085'],
            [
                'user_id' => $kasir->id,
                'nama_pelanggan' => 'PT Mulia Abadi',
                'nomor_meja' => 'Meja 12',
                'tipe_pesanan' => 'dine-in',
                'subtotal' => 190909,
                'pajak' => 19091,
                'diskon' => 0,
                'total_harga' => 210000,
                'metode_pembayaran' => 'debit',
                'bayar' => 210000,
                'kembalian' => 0,
                'status' => 'selesai',
                'created_at' => Carbon::now()->setHour(13)->setMinute(42),
            ]
        );
        $trx4->items()->delete();
        if ($burger) {
            TransaksiItem::create([
                'transaksi_id' => $trx4->id,
                'menu_id' => $burger->id,
                'nama_menu' => $burger->nama,
                'harga' => 32000,
                'qty' => 4,
                'catatan' => null,
                'subtotal' => 128000,
            ]);
        }
        if ($matcha) {
            TransaksiItem::create([
                'transaksi_id' => $trx4->id,
                'menu_id' => $matcha->id,
                'nama_menu' => $matcha->nama,
                'harga' => 22000,
                'qty' => 3,
                'catatan' => null,
                'subtotal' => 66000,
            ]);
        }

        // 5. Transaksi #TRX-20251024-0084
        $trx5 = Transaksi::updateOrCreate(
            ['kode_transaksi' => 'TRX-20251024-0084'],
            [
                'user_id' => $kasir->id,
                'nama_pelanggan' => 'Linda Natalia',
                'nomor_meja' => 'Meja 04',
                'tipe_pesanan' => 'dine-in',
                'subtotal' => 43636,
                'pajak' => 4364,
                'diskon' => 0,
                'total_harga' => 48000,
                'metode_pembayaran' => 'qris',
                'bayar' => 48000,
                'kembalian' => 0,
                'status' => 'selesai',
                'created_at' => Carbon::now()->setHour(13)->setMinute(10),
            ]
        );
        $trx5->items()->delete();
        if ($macchiato) {
            TransaksiItem::create([
                'transaksi_id' => $trx5->id,
                'menu_id' => $macchiato->id,
                'nama_menu' => $macchiato->nama,
                'harga' => 24000,
                'qty' => 2,
                'catatan' => null,
                'subtotal' => 48000,
            ]);
        }
    }
}
