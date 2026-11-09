<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiPosTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_view_pos_terminal(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        Menu::create([
            'sku' => 'KK-COF-001',
            'nama' => 'Caramel Latte',
            'kategori' => 'coffee',
            'harga_jual' => 25000,
            'harga_modal' => 10000,
            'stok' => 20,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/pos');

        $response->assertStatus(200);
        $response->assertSee('Caramel Latte');
        $response->assertSee('Pesanan Aktif');
    }

    public function test_pos_checkout_via_ajax_decrements_stock_and_returns_json(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $menu = Menu::create([
            'sku' => 'KK-COF-002',
            'nama' => 'Americano',
            'kategori' => 'coffee',
            'harga_jual' => 20000,
            'harga_modal' => 8000,
            'stok' => 15,
            'is_active' => true,
        ]);

        $payload = [
            'items' => json_encode([
                [
                    'id' => $menu->id,
                    'name' => $menu->nama,
                    'price' => $menu->harga_jual,
                    'qty' => 3,
                    'notes' => 'Less ice',
                ]
            ]),
            'nama_pelanggan' => 'Andi',
            'nomor_meja' => 'Meja 04',
            'tipe_pesanan' => 'dine-in',
            'metode_pembayaran' => 'qris',
            'bayar' => 66000,
        ];

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->post('/pos', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonPath('transaksi.nama_pelanggan', 'Andi');
        $response->assertJsonPath('transaksi.nomor_meja', 'Meja 04');
        $response->assertJsonPath('transaksi.subtotal', '60000.00');
        $response->assertJsonPath('transaksi.pajak', '6000.00');
        $response->assertJsonPath('transaksi.total_harga', '66000.00');

        // Pastikan stok terpotong: 15 - 3 = 12
        $this->assertEquals(12, $menu->fresh()->stok);

        // Pastikan record tersimpan di DB
        $this->assertDatabaseHas('transaksis', [
            'nama_pelanggan' => 'Andi',
            'total_harga' => 66000,
            'status' => 'selesai',
        ]);
        $this->assertDatabaseHas('transaksi_items', [
            'menu_id' => $menu->id,
            'qty' => 3,
            'subtotal' => 60000,
        ]);
    }

    public function test_pos_checkout_fails_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $menu = Menu::create([
            'sku' => 'KK-COF-003',
            'nama' => 'Cold Brew Special',
            'kategori' => 'coffee',
            'harga_jual' => 30000,
            'harga_modal' => 12000,
            'stok' => 2,
            'is_active' => true,
        ]);

        $payload = [
            'items' => json_encode([
                [
                    'id' => $menu->id,
                    'name' => $menu->nama,
                    'price' => $menu->harga_jual,
                    'qty' => 5, // Lebih dari stok 2
                ]
            ]),
            'nama_pelanggan' => 'Budi',
            'nomor_meja' => 'Takeaway',
            'tipe_pesanan' => 'takeaway',
            'metode_pembayaran' => 'tunai',
            'bayar' => 200000,
        ];

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->post('/pos', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);

        // Stok tidak berkurang
        $this->assertEquals(2, $menu->fresh()->stok);
    }

    public function test_pos_checkout_fails_when_cash_payment_is_underpaid(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $menu = Menu::create([
            'sku' => 'KK-COF-004',
            'nama' => 'Affogato',
            'kategori' => 'coffee',
            'harga_jual' => 25000,
            'harga_modal' => 10000,
            'stok' => 10,
            'is_active' => true,
        ]);

        $payload = [
            'items' => json_encode([
                [
                    'id' => $menu->id,
                    'name' => $menu->nama,
                    'price' => $menu->harga_jual,
                    'qty' => 1,
                ]
            ]),
            'nama_pelanggan' => 'Citra',
            'nomor_meja' => '05',
            'tipe_pesanan' => 'dine-in',
            'metode_pembayaran' => 'tunai',
            'bayar' => 10000, // Total 27500 (25000 + 2500 PB1), bayar cuma 10000
        ];

        $response = $this->actingAs($user)
            ->withHeaders([
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->post('/pos', $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
        $this->assertEquals(10, $menu->fresh()->stok);
    }
}
