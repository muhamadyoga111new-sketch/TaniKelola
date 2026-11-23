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
        Schema::create('detail_transaksi_panen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_panen_id')->constrained('transaksi_panen')->cascadeOnDelete();
            $table->foreignId('komoditas_id')->constrained('komoditas')->cascadeOnDelete();
            $table->decimal('jumlah_panen', 12, 2); // dalam kg atau ikat
            $table->decimal('harga_beli', 15, 2); // harga per kg saat transaksi
            $table->decimal('subtotal', 15, 2); // jumlah_panen * harga_beli
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_transaksi_panen');
    }
};
