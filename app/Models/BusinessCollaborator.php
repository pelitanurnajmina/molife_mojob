<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessCollaborator extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['accepted_at' => 'datetime'];

    public function product() { return $this->belongsTo(BusinessProduct::class, 'business_product_id'); }
    public function owner()   { return $this->belongsTo(User::class, 'owner_id'); }
    public function user()    { return $this->belongsTo(User::class); }
}
