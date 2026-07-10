<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollabTask extends Model
{
    public const STATUSES = ['todo', 'progress', 'review', 'done'];

    protected $guarded = ['id'];

    public function assignee() { return $this->belongsTo(User::class, 'assignee_id'); }
}
