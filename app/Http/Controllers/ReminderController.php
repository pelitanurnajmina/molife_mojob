<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function update(Request $request)
    {
        $storage = UserStorage::fromSession();
        $storage->updateReminder($request->key, $request->time ?? '');
        $storage->save();
        return redirect()->back();
    }
}
