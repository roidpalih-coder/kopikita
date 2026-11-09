<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_view_sales_report(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $transaksi = Transaksi::create([
            'kode_transaksi' => 'TRX-LAP-001',
            'user_id' => $user->id,
            'nama_pelanggan' => 'Pelanggan Laporan',
            'nomor_meja' => 'Meja 1',
            'tipe_pesanan' => 'dine-in',
            'subtotal' => 20000,
            'pajak' => 2000,
            'diskon' => 0,
            'total_harga' => 22000,
            'metode_pembayaran' => 'tunai',
            'bayar' => 30000,
            'kembalian' => 8000,
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($user)->get('/laporan');

        $response->assertStatus(200);
        $response->assertSee('TRX-LAP-001');
        $response->assertSee('Pelanggan Laporan');
        $response->assertSee('Pratinjau Struk Thermal');
    }

    public function test_sales_report_is_sorted_from_newest_to_oldest(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        // Transaksi 1 (lama)
        $t1 = Transaksi::create([
            'kode_transaksi' => 'TRX-OLD-001',
            'user_id' => $user->id,
            'nama_pelanggan' => 'Pelanggan Pertama',
            'nomor_meja' => '01',
            'tipe_pesanan' => 'dine-in',
            'subtotal' => 10000,
            'pajak' => 1000,
            'diskon' => 0,
            'total_harga' => 11000,
            'metode_pembayaran' => 'qris',
            'bayar' => 11000,
            'kembalian' => 0,
            'status' => 'selesai',
            'created_at' => Carbon::now()->subHours(2),
        ]);

        // Transaksi 2 (terbaru)
        $t2 = Transaksi::create([
            'kode_transaksi' => 'TRX-NEW-002',
            'user_id' => $user->id,
            'nama_pelanggan' => 'Pelanggan Terbaru',
            'nomor_meja' => '02',
            'tipe_pesanan' => 'dine-in',
            'subtotal' => 20000,
            'pajak' => 2000,
            'diskon' => 0,
            'total_harga' => 22000,
            'metode_pembayaran' => 'tunai',
            'bayar' => 50000,
            'kembalian' => 28000,
            'status' => 'selesai',
            'created_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)->get('/laporan');

        $response->assertStatus(200);
        $transaksis = $response->viewData('transaksis');

        // Indeks pertama haruslah transaksi terbaru
        $this->assertEquals($t2->id, $transaksis->first()->id);
        $this->assertEquals('TRX-NEW-002', $transaksis->first()->kode_transaksi);
    }

    public function test_cashier_can_stream_thermal_receipt_pdf(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $transaksi = Transaksi::create([
            'kode_transaksi' => 'TRX-RECEIPT-001',
            'user_id' => $user->id,
            'nama_pelanggan' => 'Doni',
            'nomor_meja' => '03',
            'tipe_pesanan' => 'dine-in',
            'subtotal' => 30000,
            'pajak' => 3000,
            'diskon' => 0,
            'total_harga' => 33000,
            'metode_pembayaran' => 'debit',
            'bayar' => 33000,
            'kembalian' => 0,
            'status' => 'selesai',
        ]);

        $response = $this->actingAs($user)->get("/laporan/{$transaksi->id}/struk");

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_cashier_can_export_sales_report_pdf(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $response = $this->actingAs($user)->get('/laporan/export/pdf');

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_cashier_can_export_sales_report_csv(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $response = $this->actingAs($user)->get('/laporan/export/csv');

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
    }
}
