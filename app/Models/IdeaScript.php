<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdeaScript extends Model
{
    protected $guarded = ['id'];

    public const TYPES = ['idea', 'script'];

    public function user() { return $this->belongsTo(User::class); }
}
