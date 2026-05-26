<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class GymController extends Controller
{
    public function index(Request $request)
    {
        $storage    = UserStorage::fromSession();
        $today      = date('Y-m-d');
        $date       = $request->query('date', $today);

        $gymData     = $storage->getGym($date);
        $gymWeekly   = $storage->getGymWeeklyCount();
        $gymMonthly  = $storage->getGymMonthlyCount();
        $caloriesWeek= $storage->getTotalCaloriesThisWeek();
        $weekDates   = UserStorage::getWeekDates();
        $monthDates  = UserStorage::getMonthDates();

        $gymDataAll  = $storage->toArray()['gym'];

        return view('pages.gym', compact('date', 'today', 'gymData', 'gymWeekly', 'gymMonthly', 'caloriesWeek', 'weekDates', 'monthDates', 'gymDataAll'));
    }

    public function toggle(Request $request)
    {
        $storage = UserStorage::fromSession();
        $calories = (int)($request->calories ?? 0);
        $storage->toggleGym($request->date, $calories);
        $storage->save();
        return redirect()->back();
    }

    public function updateCalories(Request $request)
    {
        $storage = UserStorage::fromSession();
        $storage->updateGymCalories($request->date, (int)$request->calories);
        $storage->save();
        return redirect()->back();
    }
}
