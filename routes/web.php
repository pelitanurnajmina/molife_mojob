<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SholatController;
use App\Http\Controllers\GymController;
use App\Http\Controllers\RunController;
use App\Http\Controllers\IntimacyController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\LamaranController;
use App\Http\Controllers\PersiapanController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StatistikKarirController;

/* ── Language switcher ── */
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth.simple')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/sholat', [SholatController::class, 'index'])->name('sholat');
    Route::post('/sholat/toggle-wajib', [SholatController::class, 'toggleWajib'])->name('sholat.toggle-wajib');
    Route::post('/sholat/toggle-takbir', [SholatController::class, 'toggleTakbir'])->name('sholat.toggle-takbir');
    Route::post('/sholat/toggle-rawatib', [SholatController::class, 'toggleRawatib'])->name('sholat.toggle-rawatib');
    Route::post('/sholat/toggle-sunnah', [SholatController::class, 'toggleSunnah'])->name('sholat.toggle-sunnah');

    Route::get('/gym', [GymController::class, 'index'])->name('gym');
    Route::post('/gym/toggle', [GymController::class, 'toggle'])->name('gym.toggle');
    Route::post('/gym/calories', [GymController::class, 'updateCalories'])->name('gym.calories');

    Route::get('/run', [RunController::class, 'index'])->name('run');
    Route::post('/run/toggle', [RunController::class, 'toggle'])->name('run.toggle');
    Route::post('/run/update', [RunController::class, 'update'])->name('run.update');

    Route::get('/intimasi', [IntimacyController::class, 'index'])->name('intimasi');
    Route::post('/intimasi/change', [IntimacyController::class, 'change'])->name('intimasi.change');

    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks');
    Route::post('/tasks/daily', [TaskController::class, 'addDaily'])->name('tasks.daily.add');
    Route::post('/tasks/weekly', [TaskController::class, 'addWeekly'])->name('tasks.weekly.add');
    Route::post('/tasks/daily/{id}/toggle', [TaskController::class, 'toggleDaily'])->name('tasks.daily.toggle');
    Route::post('/tasks/weekly/{id}/toggle', [TaskController::class, 'toggleWeekly'])->name('tasks.weekly.toggle');
    Route::delete('/tasks/daily/{id}', [TaskController::class, 'deleteDaily'])->name('tasks.daily.delete');
    Route::delete('/tasks/weekly/{id}', [TaskController::class, 'deleteWeekly'])->name('tasks.weekly.delete');
    Route::post('/tasks/note', [TaskController::class, 'updateNote'])->name('tasks.note.update');
    Route::post('/tasks/reflection', [TaskController::class, 'updateReflection'])->name('tasks.reflection.update');

    Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik');
    Route::get('/goals', [GoalController::class, 'index'])->name('goals');
    Route::post('/goals/update', [GoalController::class, 'update'])->name('goals.update');
    Route::post('/reminders/update', [ReminderController::class, 'update'])->name('reminders.update');

    // Statistik Karir
    Route::get('/karir', [StatistikKarirController::class, 'index'])->name('karir');

    // Lamaran Kerja (Job Applications)
    Route::prefix('lamaran')->name('lamaran.')->group(function () {
        Route::get('/',              [LamaranController::class, 'index'])->name('index');
        Route::post('/',             [LamaranController::class, 'store'])->name('store');
        Route::post('/{id}',         [LamaranController::class, 'update'])->name('update');
        Route::delete('/{id}',       [LamaranController::class, 'destroy'])->name('destroy');
        Route::get('/ekspor',        [LamaranController::class, 'export'])->name('export');
    });

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/toggle-feature', [SettingsController::class, 'toggleFeature'])->name('settings.toggle-feature');

    // Persiapan Melamar (Links, Files, Templates)
    Route::prefix('persiapan')->name('persiapan.')->group(function () {
        Route::get('/',                        [PersiapanController::class, 'index'])->name('index');
        // Links
        Route::post('/link',                   [PersiapanController::class, 'storeLink'])->name('link.store');
        Route::delete('/link/{id}',            [PersiapanController::class, 'destroyLink'])->name('link.destroy');
        // Files
        Route::post('/file',                   [PersiapanController::class, 'storeFile'])->name('file.store');
        Route::get('/file/{id}/unduh',         [PersiapanController::class, 'downloadFile'])->name('file.download');
        Route::delete('/file/{id}',            [PersiapanController::class, 'destroyFile'])->name('file.destroy');
        // Templates
        Route::post('/template',               [PersiapanController::class, 'storeTemplate'])->name('template.store');
        Route::post('/template/{id}',          [PersiapanController::class, 'updateTemplate'])->name('template.update');
        Route::delete('/template/{id}',        [PersiapanController::class, 'destroyTemplate'])->name('template.destroy');
    });
});
