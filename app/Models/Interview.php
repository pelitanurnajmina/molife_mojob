<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['date' => 'date:Y-m-d', 'completed' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
    public function application() { return $this->belongsTo(JobApplication::class, 'application_id'); }
}
