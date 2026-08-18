<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'active'     => 'boolean',
        'expires_at' => 'date',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    /** Bisa dipakai untuk daftar sekarang? (aktif, belum kedaluwarsa, kuota belum habis) */
    public function isRedeemable(): bool
    {
        if (!$this->active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) return false;
        return true;
    }
}
