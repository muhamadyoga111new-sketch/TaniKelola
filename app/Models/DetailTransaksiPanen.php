<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTransaksiPanen extends Model
{
    use HasFactory;

    protected $table = 'detail_transaksi_panen';

    protected $fillable = [
        'transaksi_panen_id',
        'komoditas_id',
        'jumlah_panen',
        'harga_beli',
        'subtotal',
        'keterangan',
    ];

    protected $casts = [
        'jumlah_panen' => 'decimal:2',
        'harga_beli' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(TransaksiPanen::class, 'transaksi_panen_id');
    }

    public function komoditas(): BelongsTo
    {
        return $this->belongsTo(Komoditas::class, 'komoditas_id');
    }
}
