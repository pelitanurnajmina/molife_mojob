<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class IntimacyController extends Controller
{
    public function index(Request $request)
    {
        $storage   = UserStorage::fromSession();
        $today     = date('Y-m-d');
        $date      = $request->query('date', $today);

        $count        = $storage->getIntimacy($date);
        $todayCount   = $storage->getIntimacy($today);
        $monthlyCount = $storage->getIntimacyMonthlyCount();
        $monthDates   = UserStorage::getMonthDates();
        $intimacyAll  = $storage->toArray()['intimacy'];

        return view('pages.intimasi', compact('date', 'today', 'count', 'todayCount', 'monthlyCount', 'monthDates', 'intimacyAll'));
    }

    public function change(Request $request)
    {
        $storage = UserStorage::fromSession();
        $storage->changeIntimacy($request->date, (int)$request->delta);
        $storage->save();
        return redirect()->back();
    }
}
