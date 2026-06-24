<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['starts_at' => 'date', 'ends_at' => 'date', 'paid_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
}
