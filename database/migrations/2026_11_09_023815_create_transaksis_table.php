<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 50)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama_pelanggan', 100)->nullable();
            $table->string('nomor_meja', 50)->nullable();
            $table->enum('tipe_pesanan', ['dine-in', 'takeaway'])->default('dine-in');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('pajak', 12, 2)->default(0);
            $table->decimal('diskon', 12, 2)->default(0);
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->enum('metode_pembayaran', ['tunai', 'qris', 'debit'])->default('tunai');
            $table->decimal('bayar', 12, 2)->default(0);
            $table->decimal('kembalian', 12, 2)->default(0);
            $table->enum('status', ['selesai', 'diproses', 'batal'])->default('selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
