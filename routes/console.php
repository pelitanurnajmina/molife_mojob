<?php
use Illuminate\Support\Facades\Schedule;

// Email pengingat langganan (H-3 dan hari-H), tiap pagi WIB.
Schedule::command('sub:remind')->dailyAt('08:00')->timezone('Asia/Jakarta');
