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
        Schema::create('transaksi_panen', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_transaksi')->unique(); // TRX-202610-0001
            $table->foreignId('petani_id')->constrained('petani')->cascadeOnDelete();
            $table->date('tanggal_transaksi');
            $table->decimal('total_bruto', 15, 2)->default(0); // Total nilai panen
            $table->decimal('potongan_kewajiban', 15, 2)->default(0); // Potongan utang
            $table->decimal('uang_diterima_petani', 15, 2)->default(0); // Uang bersih ke petani
            $table->decimal('sisa_kewajiban_sebelum', 15, 2)->default(0);
            $table->decimal('sisa_kewajiban_sesudah', 15, 2)->default(0);
            $table->enum('status_wa', ['pending', 'terkirim', 'gagal', 'simulasi'])->default('pending');
            $table->text('catatan')->nullable();
            $table->foreignId('pengguna_id')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_panen');
    }
};
