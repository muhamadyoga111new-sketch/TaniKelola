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
        Schema::create('petani', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengguna_id')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->string('kode_petani')->unique(); // e.g. PTN-001
            $table->string('nama_petani');
            $table->string('nik', 30)->nullable();
            $table->string('no_wa', 25);
            $table->text('alamat')->nullable();
            $table->string('luas_lahan')->nullable();
            $table->string('komoditas_utama')->nullable();
            $table->decimal('total_modal', 15, 2)->default(0);
            $table->decimal('total_terbayar', 15, 2)->default(0);
            $table->decimal('sisa_kewajiban', 15, 2)->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petani');
    }
};
