<?php

namespace App\Http\Controllers;

use App\Models\Reminder;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function update(Request $request)
    {
        Reminder::updateOrCreate(
            ['user_id' => auth()->id(), 'key' => $request->key],
            ['time' => $request->time ?? '']
        );
        return redirect()->back();
    }
}
