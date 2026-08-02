<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeBlock extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['date' => 'date:Y-m-d'];

    /** Palet warna blok (key => kelas Tailwind untuk latar & teks). */
    public const COLORS = ['blue', 'green', 'violet', 'amber', 'rose', 'gray'];

    public function user() { return $this->belongsTo(User::class); }
}
