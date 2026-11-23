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
        Schema::create('modal', function (Blueprint $table) {
            $table->id();
            $table->string('kode_modal')->unique(); // e.g. MDL-202610-0001
            $table->foreignId('petani_id')->constrained('petani')->cascadeOnDelete();
            $table->enum('jenis_modal', ['barang', 'uang'])->default('barang');
            $table->string('kategori_kebutuhan'); // e.g. Pupuk, Obat/Pestisida, Olah Lahan, Upah Kerja, Bibit, Lainnya
            $table->string('rincian_barang')->nullable(); // misal: "NPK Mutiara 50kg, Insektisida 2 botol"
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal_pemberian');
            $table->text('catatan')->nullable();
            $table->foreignId('pengguna_id')->nullable()->constrained('pengguna')->nullOnDelete(); // created by
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modal');
    }
};
