<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuitTracker extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['start_date' => 'date:Y-m-d'];

    public function user() { return $this->belongsTo(User::class); }
}
