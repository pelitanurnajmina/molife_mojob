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

        // ── Range filter ──
        $range  = $request->query('range', 'month');
        $months = UserStorage::rangeToMonths($range);

        $stripRows = []; $rangeActive = 0; $rangeTitle = '';
        if ($months !== null) {
            $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            $result = UserStorage::buildStripRows($months, function ($d) use ($gymDataAll, $monthShort) {
                $done = !empty($gymDataAll[$d]['done']);
                $dt   = new \DateTime($d);
                return [
                    'active' => $done,
                    'value'  => $done ? 1 : 0,
                    'title'  => $dt->format('j') . ' ' . $monthShort[(int)$dt->format('n')] . ': ' . ($done ? 'Gym' : 'Rest'),
                ];
            });
            $stripRows   = $result['rows'];
            $rangeActive = $result['activeDays'];
            $rangeTitle  = $result['title'];
        }

        return view('pages.gym', compact(
            'date', 'today', 'gymData', 'gymWeekly', 'gymMonthly', 'caloriesWeek',
            'weekDates', 'monthDates', 'gymDataAll',
            'range', 'months', 'stripRows', 'rangeActive', 'rangeTitle'
        ));
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
