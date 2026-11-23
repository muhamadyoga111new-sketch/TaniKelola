<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Komoditas extends Model
{
    use HasFactory;

    protected $table = 'komoditas';

    protected $fillable = [
        'nama_komoditas',
        'kategori',
        'satuan',
        'harga_pasar_default',
        'deskripsi',
    ];

    protected $casts = [
        'harga_pasar_default' => 'decimal:2',
    ];

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksiPanen::class, 'komoditas_id');
    }
}
