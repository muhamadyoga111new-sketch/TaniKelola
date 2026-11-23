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
        Schema::create('log_wa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petani_id')->nullable()->constrained('petani')->nullOnDelete();
            $table->string('tujuan_nomor', 30);
            $table->string('penerima_nama');
            $table->string('tipe_notifikasi'); // transaksi_panen, pemberian_modal, pembayaran_tunai, manual
            $table->text('pesan');
            $table->string('status', 20)->default('pending'); // sukses, gagal, simulasi
            $table->text('response_api')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_wa');
    }
};
