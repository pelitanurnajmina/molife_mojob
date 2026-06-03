<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CyclingLog extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['date' => 'date:Y-m-d', 'done' => 'boolean', 'km' => 'float'];

    public function user() { return $this->belongsTo(User::class); }
}
