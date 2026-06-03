<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reflection extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['date' => 'date:Y-m-d'];

    public function user() { return $this->belongsTo(User::class); }
}
