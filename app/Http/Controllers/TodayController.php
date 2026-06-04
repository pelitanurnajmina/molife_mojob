<?php

namespace App\Http\Controllers;

class TodayController extends Controller
{
    public function index()
    {
        return redirect()->route('dashboard');
    }
}
