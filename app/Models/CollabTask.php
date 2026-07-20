<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollabTask extends Model
{
    public const STATUSES = ['todo', 'progress', 'review', 'done'];

    public const PRIORITIES = ['low', 'normal', 'high', 'urgent'];

    protected $guarded = ['id'];

    protected $casts = ['due_date' => 'date'];

    public function assignee() { return $this->belongsTo(User::class, 'assignee_id'); }
    public function product()  { return $this->belongsTo(BusinessProduct::class, 'business_product_id'); }
}
