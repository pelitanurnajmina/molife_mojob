<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrepQa extends Model
{
    protected $table = 'prep_qa';
    protected $guarded = ['id'];

    public function user() { return $this->belongsTo(User::class); }
}
