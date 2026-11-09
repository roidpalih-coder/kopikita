<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'nama_pelanggan',
        'nomor_meja',
        'tipe_pesanan',
        'subtotal',
        'pajak',
        'diskon',
        'total_harga',
        'metode_pembayaran',
        'bayar',
        'kembalian',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'pajak' => 'decimal:2',
            'diskon' => 'decimal:2',
            'total_harga' => 'decimal:2',
            'bayar' => 'decimal:2',
            'kembalian' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransaksiItem::class);
    }
}
