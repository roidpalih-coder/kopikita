<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_view_dashboard_with_all_metrics(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $menu = Menu::create([
            'sku' => 'KK-COF-001',
            'nama' => 'Espresso Test',
            'kategori' => 'coffee',
            'harga_jual' => 20000,
            'harga_modal' => 8000,
            'stok' => 50,
            'is_active' => true,
        ]);

        $transaksi = Transaksi::create([
            'kode_transaksi' => 'TRX-TEST-001',
            'user_id' => $user->id,
            'nama_pelanggan' => 'Pelanggan Uji',
            'nomor_meja' => '01',
            'tipe_pesanan' => 'dine-in',
            'subtotal' => 20000,
            'pajak' => 2000,
            'diskon' => 0,
            'total_harga' => 22000,
            'metode_pembayaran' => 'qris',
            'bayar' => 22000,
            'kembalian' => 0,
            'status' => 'selesai',
        ]);

        TransaksiItem::create([
            'transaksi_id' => $transaksi->id,
            'menu_id' => $menu->id,
            'nama_menu' => $menu->nama,
            'harga' => $menu->harga_jual,
            'qty' => 1,
            'subtotal' => 20000,
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('totalPenjualanHariIni', 22000.0);
        $response->assertViewHas('jumlahTransaksiHariIni', 1);
        $response->assertViewHas('pesananTerlayani', 1);
        $response->assertViewHas('hourlyLabels');
        $response->assertViewHas('weeklyLabels');
        $response->assertViewHas('monthlyLabels');
        $response->assertSee('Performa Arus Kas');
        $response->assertSee('Grafik Penjualan Harian');
    }
}
