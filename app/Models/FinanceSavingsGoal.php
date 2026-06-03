<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceSavingsGoal extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['deadline' => 'date:Y-m-d'];

    public function user() { return $this->belongsTo(User::class); }
}
