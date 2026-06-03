<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrepLink extends Model
{
    protected $guarded = ['id'];

    public function user() { return $this->belongsTo(User::class); }
}
