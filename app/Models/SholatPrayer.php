<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SholatPrayer extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'date'           => 'date:Y-m-d',
        'done'           => 'boolean',
        'takbir_pertama' => 'boolean',
        'rawatib'        => 'boolean',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
