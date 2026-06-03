<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFeature extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['enabled' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
}
