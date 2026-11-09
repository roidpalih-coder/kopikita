<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'nama',
        'kategori',
        'harga_jual',
        'harga_modal',
        'stok',
        'gambar',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'harga_jual' => 'decimal:2',
            'harga_modal' => 'decimal:2',
            'stok' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function transaksiItems(): HasMany
    {
        return $this->hasMany(TransaksiItem::class);
    }

    public function getStatusStokAttribute(): string
    {
        if ($this->stok <= 0) {
            return 'Habis';
        }
        $threshold = config('pos.low_stock_threshold', 15);
        if ($this->stok <= $threshold) {
            return 'Stok Menipis';
        }
        return 'Tersedia';
    }

    public function getGambarUrlAttribute(): string
    {
        if ($this->gambar && file_exists(public_path('storage/' . $this->gambar))) {
            return asset('storage/' . $this->gambar);
        }
        if ($this->gambar && (str_starts_with($this->gambar, 'http') || str_starts_with($this->gambar, 'data:'))) {
            return $this->gambar;
        }
        return 'https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=400&q=80';
    }
}
