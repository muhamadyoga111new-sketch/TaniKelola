<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Modal extends Model
{
    use HasFactory;

    protected $table = 'modal';

    protected $fillable = [
        'kode_modal',
        'petani_id',
        'jenis_modal',
        'kategori_kebutuhan',
        'rincian_barang',
        'nominal',
        'tanggal_pemberian',
        'catatan',
        'pengguna_id',
        'user_id',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'tanggal_pemberian' => 'date',
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

        static::created(function ($modal) {
            $modal->petani->recalculateKewajiban();
        });

        static::updated(function ($modal) {
            $modal->petani->recalculateKewajiban();
        });

        static::deleted(function ($modal) {
            $modal->petani->recalculateKewajiban();
        });
    }
}
