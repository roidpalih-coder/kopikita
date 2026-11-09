<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'sku' => 'KK-COF-001',
                'nama' => 'Kopi Susu Gula Aren',
                'kategori' => 'coffee',
                'harga_jual' => 18000,
                'harga_modal' => 8500,
                'stok' => 45,
                'gambar' => 'https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'sku' => 'KK-COF-002',
                'nama' => 'Americano Ice',
                'kategori' => 'coffee',
                'harga_jual' => 15000,
                'harga_modal' => 6000,
                'stok' => 60,
                'gambar' => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'sku' => 'KK-COF-003',
                'nama' => 'Caramel Macchiato',
                'kategori' => 'coffee',
                'harga_jual' => 24000,
                'harga_modal' => 11000,
                'stok' => 28,
                'gambar' => 'https://images.unsplash.com/photo-1572442388796-11668a67e53d?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'sku' => 'KK-COF-004',
                'nama' => 'Espresso Double Shot',
                'kategori' => 'coffee',
                'harga_jual' => 14000,
                'harga_modal' => 5000,
                'stok' => 50,
                'gambar' => 'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'sku' => 'KK-COF-005',
                'nama' => 'Cafe Latte Roastery',
                'kategori' => 'coffee',
                'harga_jual' => 22000,
                'harga_modal' => 9500,
                'stok' => 35,
                'gambar' => 'https://images.unsplash.com/photo-1570968915860-54d5c301fa9f?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'sku' => 'KK-NCO-001',
                'nama' => 'Matcha Latte Premium',
                'kategori' => 'non-coffee',
                'harga_jual' => 22000,
                'harga_modal' => 10000,
                'stok' => 32,
                'gambar' => 'https://images.unsplash.com/photo-1536256263959-770b48d82b0a?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'sku' => 'KK-NCO-002',
                'nama' => 'Chocolate Cream Signature',
                'kategori' => 'non-coffee',
                'harga_jual' => 20000,
                'harga_modal' => 9000,
                'stok' => 25,
                'gambar' => 'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'sku' => 'KK-NCO-003',
                'nama' => 'Red Velvet Ice Cream',
                'kategori' => 'non-coffee',
                'harga_jual' => 22000,
                'harga_modal' => 9500,
                'stok' => 15,
                'gambar' => 'https://images.unsplash.com/photo-1618160702438-9b02ab6515c9?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'sku' => 'KK-FD-001',
                'nama' => 'Beef Burger Kopi Kita',
                'kategori' => 'makanan',
                'harga_jual' => 32000,
                'harga_modal' => 17500,
                'stok' => 18,
                'gambar' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'sku' => 'KK-FD-002',
                'nama' => 'Nasi Goreng Spesial Kafe',
                'kategori' => 'makanan',
                'harga_jual' => 28000,
                'harga_modal' => 14000,
                'stok' => 20,
                'gambar' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'sku' => 'KK-FD-003',
                'nama' => 'Mie Goreng Roastery Telur',
                'kategori' => 'makanan',
                'harga_jual' => 25000,
                'harga_modal' => 12000,
                'stok' => 20,
                'gambar' => 'https://images.unsplash.com/photo-1612927601601-6638404737ce?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'sku' => 'KK-SNK-001',
                'nama' => 'Butter Croissant Warm',
                'kategori' => 'snack',
                'harga_jual' => 20000,
                'harga_modal' => 9000,
                'stok' => 14,
                'gambar' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'sku' => 'KK-SNK-002',
                'nama' => 'French Fries Crispy',
                'kategori' => 'snack',
                'harga_jual' => 18000,
                'harga_modal' => 8000,
                'stok' => 25,
                'gambar' => 'https://images.unsplash.com/photo-1576107232684-1279f3908594?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
            [
                'sku' => 'KK-SNK-003',
                'nama' => 'Toast Kaya Pandan Butter',
                'kategori' => 'snack',
                'harga_jual' => 16000,
                'harga_modal' => 7000,
                'stok' => 8,
                'gambar' => 'https://images.unsplash.com/photo-1584776296944-ab6fb57b0bdd?auto=format&fit=crop&w=400&q=80',
                'is_active' => true,
            ],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(['sku' => $menu['sku']], $menu);
        }
    }
}
