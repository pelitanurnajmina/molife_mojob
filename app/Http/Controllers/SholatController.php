<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class SholatController extends Controller
{
    public function index(Request $request)
    {
        $storage  = UserStorage::fromSession();
        $today    = date('Y-m-d');
        $date     = $request->query('date', $today);

        $sholatData  = $storage->getSholat($date);
        $sholatStats = $storage->getSholatStats($date);
        $streak      = $storage->getSholatStreak();
        $takbirStreak= $storage->getTakbirStreak();
        $monthDates  = UserStorage::getMonthDates();

        return view('pages.sholat', compact('date', 'today', 'sholatData', 'sholatStats', 'streak', 'takbirStreak', 'monthDates'));
    }

    public function toggleWajib(Request $request)
    {
        $storage = UserStorage::fromSession();
        $storage->toggleSholatWajib($request->date, $request->name);
        $storage->save();
        return redirect()->back();
    }

    public function toggleTakbir(Request $request)
    {
        $storage = UserStorage::fromSession();
        $storage->toggleTakbirPertama($request->date, $request->name);
        $storage->save();
        return redirect()->back();
    }

    public function toggleRawatib(Request $request)
    {
        $storage = UserStorage::fromSession();
        $storage->toggleRawatib($request->date, $request->name);
        $storage->save();
        return redirect()->back();
    }

    public function toggleSunnah(Request $request)
    {
        $storage = UserStorage::fromSession();
        $storage->toggleSholatSunnah($request->date, $request->name);
        $storage->save();
        return redirect()->back();
    }
}
