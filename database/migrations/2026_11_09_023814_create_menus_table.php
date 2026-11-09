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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('nama', 100);
            $table->enum('kategori', ['coffee', 'non-coffee', 'makanan', 'snack']);
            $table->decimal('harga_jual', 12, 2);
            $table->decimal('harga_modal', 12, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->string('gambar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
