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
        Schema::create('pembayaran_kewajiban', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pembayaran')->unique(); // BYR-202610-0001
            $table->foreignId('petani_id')->constrained('petani')->cascadeOnDelete();
            $table->date('tanggal_bayar');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->string('metode_bayar')->default('Tunai / Cash');
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
        Schema::dropIfExists('pembayaran_kewajiban');
    }
};
