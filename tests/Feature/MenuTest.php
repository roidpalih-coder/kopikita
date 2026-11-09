<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_view_menu_list(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        Menu::create([
            'sku' => 'KK-COF-010',
            'nama' => 'Mocha Ice Cream',
            'kategori' => 'coffee',
            'harga_jual' => 28000,
            'harga_modal' => 12000,
            'stok' => 25,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/menu');

        $response->assertStatus(200);
        $response->assertSee('Mocha Ice Cream');
        $response->assertSee('KK-COF-010');
    }

    public function test_cashier_can_create_new_menu(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $response = $this->actingAs($user)->post('/menu', [
            'nama' => 'Matcha Latte Premium',
            'sku' => 'KK-NCO-001',
            'kategori' => 'non-coffee',
            'harga_jual' => 24000,
            'harga_modal' => 11000,
            'stok' => 30,
        ]);

        $response->assertRedirect(route('menu.index'));
        $this->assertDatabaseHas('menus', [
            'sku' => 'KK-NCO-001',
            'nama' => 'Matcha Latte Premium',
            'kategori' => 'non-coffee',
            'stok' => 30,
        ]);
    }

    public function test_cashier_can_update_menu(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $menu = Menu::create([
            'sku' => 'KK-FD-001',
            'nama' => 'Croissant Butter',
            'kategori' => 'makanan',
            'harga_jual' => 18000,
            'harga_modal' => 9000,
            'stok' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->put("/menu/{$menu->id}", [
            'nama' => 'Croissant Butter Almond',
            'sku' => 'KK-FD-001',
            'kategori' => 'makanan',
            'harga_jual' => 22000,
            'harga_modal' => 10000,
            'stok' => 15,
        ]);

        $response->assertRedirect(route('menu.index'));
        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'nama' => 'Croissant Butter Almond',
            'harga_jual' => 22000,
            'stok' => 15,
        ]);
    }

    public function test_cashier_can_delete_menu(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        $menu = Menu::create([
            'sku' => 'KK-SNK-001',
            'nama' => 'French Fries',
            'kategori' => 'snack',
            'harga_jual' => 15000,
            'harga_modal' => 7000,
            'stok' => 20,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->delete("/menu/{$menu->id}");

        $response->assertRedirect(route('menu.index'));
        $this->assertDatabaseMissing('menus', [
            'id' => $menu->id,
        ]);
    }

    public function test_cashier_can_export_menu_to_csv(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        Menu::create([
            'sku' => 'KK-COF-020',
            'nama' => 'Piccolo Latte',
            'kategori' => 'coffee',
            'harga_jual' => 19000,
            'harga_modal' => 8000,
            'stok' => 20,
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/menu/export');

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-type'), 'text/csv'));
    }

    public function test_cashier_can_import_menu_from_csv(): void
    {
        $user = User::factory()->create(['role' => 'kasir']);

        // CSV content with 1 new menu and 1 existing menu update
        $existing = Menu::create([
            'sku' => 'KK-COF-099',
            'nama' => 'Old Coffee',
            'kategori' => 'coffee',
            'harga_jual' => 15000,
            'harga_modal' => 6000,
            'stok' => 5,
            'is_active' => true,
        ]);

        $csvContent = "SKU,Nama Menu,Kategori,Harga Jual,Harga Modal,Stok\n"
                    . "KK-COF-099,Updated Coffee,coffee,18000,7000,25\n"
                    . "KK-NCO-050,Taro Ice Blend,non-coffee,22000,9000,40\n";

        $file = UploadedFile::fake()->createWithContent('import.csv', $csvContent);

        $response = $this->actingAs($user)->post('/menu/import', [
            'csv_file' => $file,
        ]);

        $response->assertRedirect(route('menu.index'));

        // Cek item lama terupdate
        $this->assertDatabaseHas('menus', [
            'sku' => 'KK-COF-099',
            'nama' => 'Updated Coffee',
            'harga_jual' => 18000,
            'stok' => 25,
        ]);

        // Cek item baru ditambahkan
        $this->assertDatabaseHas('menus', [
            'sku' => 'KK-NCO-050',
            'nama' => 'Taro Ice Blend',
            'stok' => 40,
        ]);
    }
}
