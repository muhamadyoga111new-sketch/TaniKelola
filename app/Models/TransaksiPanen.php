<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransaksiPanen extends Model
{
    use HasFactory;

    protected $table = 'transaksi_panen';

    protected $fillable = [
        'nomor_transaksi',
        'petani_id',
        'tanggal_transaksi',
        'total_bruto',
        'potongan_kewajiban',
        'uang_diterima_petani',
        'sisa_kewajiban_sebelum',
        'sisa_kewajiban_sesudah',
        'status_wa',
        'catatan',
        'pengguna_id',
        'user_id',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'total_bruto' => 'decimal:2',
        'potongan_kewajiban' => 'decimal:2',
        'uang_diterima_petani' => 'decimal:2',
        'sisa_kewajiban_sebelum' => 'decimal:2',
        'sisa_kewajiban_sesudah' => 'decimal:2',
    ];

    public function getUserIdAttribute()
    {
        return $this->attributes['pengguna_id'] ?? null;
    }

    public function setUserIdAttribute($value): void
    {
        $this->attributes['pengguna_id'] = $value;
    }

    public function petani(): BelongsTo
    {
        return $this->belongsTo(Petani::class, 'petani_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailTransaksiPanen::class, 'transaksi_panen_id');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($transaksi) {
            $transaksi->petani->recalculateKewajiban();
        });

        static::deleted(function ($transaksi) {
            $transaksi->petani->recalculateKewajiban();
        });
    }
}
