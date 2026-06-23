<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessDeal extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['proposal_date' => 'date', 'value' => 'integer'];

    public function user() { return $this->belongsTo(User::class); }
}
