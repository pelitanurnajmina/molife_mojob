<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['connected_at' => 'date:Y-m-d'];

    public function user() { return $this->belongsTo(User::class); }
}
