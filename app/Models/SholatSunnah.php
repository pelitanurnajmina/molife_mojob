<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SholatSunnah extends Model
{
    protected $table = 'sholat_sunnah';
    protected $guarded = ['id'];
    protected $casts = ['date' => 'date:Y-m-d'];

    public function user() { return $this->belongsTo(User::class); }
}
