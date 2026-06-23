<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['date' => 'date', 'content' => 'array'];

    public function user() { return $this->belongsTo(User::class); }
}
