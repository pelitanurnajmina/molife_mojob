<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['applied_date' => 'date:Y-m-d'];

    public function user() { return $this->belongsTo(User::class); }
    public function interviews() { return $this->hasMany(Interview::class, 'application_id'); }
}
