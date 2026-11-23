<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pengguna';

    protected $fillable = [
        'nama',
        'username',
        'email',
        'no_hp',
        'peran',
        'password',
        // alias
        'name',
        'phone',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['nama'] ?? null;
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['nama'] = $value;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->attributes['no_hp'] ?? null;
    }

    public function setPhoneAttribute($value): void
    {
        $this->attributes['no_hp'] = $value;
    }

    public function getRoleAttribute(): ?string
    {
        return $this->attributes['peran'] ?? null;
    }

    public function setRoleAttribute($value): void
    {
        $this->attributes['peran'] = $value;
    }

    public function petani(): HasOne
    {
        return $this->hasOne(Petani::class, 'pengguna_id');
    }

    public function scopeRole($query, string $role)
    {
        return $query->where('peran', $role);
    }

    public function isAdmin(): bool
    {
        return in_array($this->peran ?? $this->role, ['admin']);
    }

    public function isPengepul(): bool
    {
        return in_array($this->peran ?? $this->role, ['pengepul']);
    }

    public function isPetani(): bool
    {
        return in_array($this->peran ?? $this->role, ['petani']);
    }
}
