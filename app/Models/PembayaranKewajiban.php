<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranKewajiban extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_kewajiban';

    protected $fillable = [
        'kode_pembayaran',
        'petani_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'metode_bayar',
        'sisa_kewajiban_sebelum',
        'sisa_kewajiban_sesudah',
        'status_wa',
        'catatan',
        'pengguna_id',
        'user_id',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_bayar' => 'decimal:2',
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

        static::saved(function ($bayar) {
            $bayar->petani->recalculateKewajiban();
        });

        static::deleted(function ($bayar) {
            $bayar->petani->recalculateKewajiban();
        });
    }
}
