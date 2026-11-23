<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaLog extends Model
{
    use HasFactory;

    protected $table = 'log_wa';

    protected $fillable = [
        'petani_id',
        'tujuan_nomor',
        'penerima_nama',
        'tipe_notifikasi',
        'pesan',
        'status',
        'response_api',
    ];

    public function petani(): BelongsTo
    {
        return $this->belongsTo(Petani::class);
    }
}
