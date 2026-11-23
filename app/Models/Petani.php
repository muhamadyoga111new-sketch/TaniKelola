<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Petani extends Model
{
    use HasFactory;

    protected $table = 'petani';

    protected $fillable = [
        'pengguna_id',
        'user_id',
        'kode_petani',
        'nama_petani',
        'nik',
        'no_wa',
        'alamat',
        'luas_lahan',
        'komoditas_utama',
        'total_modal',
        'total_terbayar',
        'sisa_kewajiban',
        'status',
    ];

    protected $casts = [
        'total_modal' => 'decimal:2',
        'total_terbayar' => 'decimal:2',
        'sisa_kewajiban' => 'decimal:2',
    ];

    public function getUserIdAttribute()
    {
        return $this->attributes['pengguna_id'] ?? null;
    }

    public function setUserIdAttribute($value): void
    {
        $this->attributes['pengguna_id'] = $value;
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function modals(): HasMany
    {
        return $this->hasMany(Modal::class)->latest('tanggal_pemberian');
    }

    public function transaksiPanens(): HasMany
    {
        return $this->hasMany(TransaksiPanen::class)->latest('tanggal_transaksi');
    }

    public function pembayaranKewajibans(): HasMany
    {
        return $this->hasMany(PembayaranKewajiban::class)->latest('tanggal_bayar');
    }

    /**
     * Hitung ulang dan sinkronisasi total kewajiban petani
     */
    public function recalculateKewajiban(): void
    {
        $totalModal = $this->modals()->sum('nominal');
        $potonganPanen = $this->transaksiPanens()->sum('potongan_kewajiban');
        $bayarTunai = $this->pembayaranKewajibans()->sum('jumlah_bayar');
        $totalTerbayar = $potonganPanen + $bayarTunai;
        $sisa = max(0, $totalModal - $totalTerbayar);

        $this->update([
            'total_modal' => $totalModal,
            'total_terbayar' => $totalTerbayar,
            'sisa_kewajiban' => $sisa,
        ]);
    }
}
