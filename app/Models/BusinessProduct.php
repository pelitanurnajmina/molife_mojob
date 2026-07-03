<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessProduct extends Model
{
    protected $guarded = ['id'];

    public function user() { return $this->belongsTo(User::class); }

    public function collaborators() { return $this->hasMany(BusinessCollaborator::class); }
}
