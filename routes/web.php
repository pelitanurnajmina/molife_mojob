<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TodayController;
use App\Http\Controllers\MentalController;
use App\Http\Controllers\InsightsController;
use App\Http\Controllers\SholatController;
use App\Http\Controllers\SpiritualController;
use App\Http\Controllers\GymController;
use App\Http\Controllers\RunController;
use App\Http\Controllers\CyclingController;
use App\Http\Controllers\SwimmingController;
use App\Http\Controllers\RacketController;
use App\Http\Controllers\CustomSportController;
use App\Http\Controllers\IntimacyController;
use App\Http\Controllers\QuitController;
use App\Http\Controllers\MotivasiController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\LamaranController;
use App\Http\Controllers\PersiapanController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StatistikKarirController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\FinanceController;

/* ── Language switcher ── */
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');

// Onboarding (auth required, but no onboarding check here)
Route::middleware('auth.simple')->group(function () {
    Route::get('/onboarding',  [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

Route::middleware(['auth.simple', 'require.onboarding'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/today', fn() => redirect()->route('dashboard'))->name('today');
    Route::get('/mental', [MentalController::class, 'index'])->name('mental');
    Route::post('/mental/mood', [MentalController::class, 'storeMood'])->name('mental.mood');
    Route::delete('/mental/reflection', [MentalController::class, 'deleteReflection'])->name('mental.reflection.delete');
    // Insights merged into the main Dashboard (kept as redirect for old links)
    Route::get('/insights', fn() => redirect()->route('dashboard'))->name('insights');

    // Spiritual (Islam)
    Route::get('/sholat', [SholatController::class, 'index'])->name('sholat');
    Route::post('/sholat/toggle-wajib',   [SholatController::class, 'toggleWajib'])->name('sholat.toggle-wajib');
    Route::post('/sholat/toggle-takbir',  [SholatController::class, 'toggleTakbir'])->name('sholat.toggle-takbir');
    Route::post('/sholat/toggle-rawatib', [SholatController::class, 'toggleRawatib'])->name('sholat.toggle-rawatib');
    Route::post('/sholat/toggle-sunnah',  [SholatController::class, 'toggleSunnah'])->name('sholat.toggle-sunnah');

    // Spiritual (non-Islam)
    Route::get('/spiritual',        [SpiritualController::class, 'index'])->name('spiritual');
    Route::post('/spiritual/toggle',[SpiritualController::class, 'toggle'])->name('spiritual.toggle');

    // Quit trackers (stop porn / kurangi sosmed)
    Route::get('/quit/{type}',          [QuitController::class, 'index'])->name('quit');
    Route::post('/quit/{type}/relapse', [QuitController::class, 'relapse'])->name('quit.relapse');

    // Motivasi (quote + impact)
    Route::get('/motivasi', [MotivasiController::class, 'index'])->name('motivasi');

    // Sports
    Route::get('/gym', [GymController::class, 'index'])->name('gym');
    Route::post('/gym/toggle',   [GymController::class, 'toggle'])->name('gym.toggle');
    Route::post('/gym/calories', [GymController::class, 'updateCalories'])->name('gym.calories');

    Route::get('/run',          [RunController::class, 'index'])->name('run');
    Route::post('/run/toggle',  [RunController::class, 'toggle'])->name('run.toggle');
    Route::post('/run/update',  [RunController::class, 'update'])->name('run.update');

    Route::get('/bersepeda',         [CyclingController::class, 'index'])->name('cycling');
    Route::post('/bersepeda/update', [CyclingController::class, 'update'])->name('cycling.update');
    Route::post('/bersepeda/reset',  [CyclingController::class, 'reset'])->name('cycling.reset');

    Route::get('/renang',         [SwimmingController::class, 'index'])->name('swimming');
    Route::post('/renang/update', [SwimmingController::class, 'update'])->name('swimming.update');
    Route::post('/renang/reset',  [SwimmingController::class, 'reset'])->name('swimming.reset');

    Route::get('/tenis',         [RacketController::class, 'index'])->name('racket');
    Route::post('/tenis/update', [RacketController::class, 'update'])->name('racket.update');
    Route::post('/tenis/reset',  [RacketController::class, 'reset'])->name('racket.reset');

    Route::get('/olahraga',         [CustomSportController::class, 'index'])->name('custom_sport');
    Route::post('/olahraga/update', [CustomSportController::class, 'update'])->name('custom_sport.update');
    Route::post('/olahraga/reset',  [CustomSportController::class, 'reset'])->name('custom_sport.reset');

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
    Route::post('/reminders/prayer-city',   [ReminderController::class, 'setPrayerCity'])->name('reminders.prayer.city');
    Route::post('/reminders/prayer-toggle', [ReminderController::class, 'togglePrayer'])->name('reminders.prayer.toggle');

    // Career Hub
    Route::get('/karir', [StatistikKarirController::class, 'index'])->name('karir');
    Route::post('/karir/goals', [StatistikKarirController::class, 'updateGoals'])->name('karir.goals');
    Route::post('/karir/contact', [StatistikKarirController::class, 'storeContact'])->name('karir.contact.store');
    Route::delete('/karir/contact/{id}', [StatistikKarirController::class, 'destroyContact'])->name('karir.contact.destroy');

    // Lamaran Kerja (Job Applications)
    Route::prefix('lamaran')->name('lamaran.')->group(function () {
        Route::get('/',              [LamaranController::class, 'index'])->name('index');
        Route::post('/',             [LamaranController::class, 'store'])->name('store');
        Route::post('/{id}',         [LamaranController::class, 'update'])->name('update');
        Route::delete('/{id}',       [LamaranController::class, 'destroy'])->name('destroy');
        Route::get('/ekspor',        [LamaranController::class, 'export'])->name('export');
    });

    // Finance
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/',                  [FinanceController::class, 'index'])->name('index');
        Route::get('/transaksi',         [FinanceController::class, 'transaksi'])->name('transaksi');
        Route::post('/transaksi',        [FinanceController::class, 'addTransaction'])->name('transaksi.add');
        Route::delete('/transaksi/{id}', [FinanceController::class, 'deleteTransaction'])->name('transaksi.delete');
        Route::get('/anggaran',          [FinanceController::class, 'anggaran'])->name('anggaran');
        Route::post('/anggaran',         [FinanceController::class, 'setBudget'])->name('anggaran.set');
        Route::get('/tabungan',          [FinanceController::class, 'tabungan'])->name('tabungan');
        Route::post('/tabungan',         [FinanceController::class, 'saveGoal'])->name('tabungan.save');
        Route::delete('/tabungan/{id}',  [FinanceController::class, 'deleteGoal'])->name('tabungan.delete');
    });

    // Settings
    // Settings — sub-pages
    Route::get('/settings',             fn() => redirect()->route('settings.profil'))->name('settings');
    Route::get('/settings/profil',      [SettingsController::class, 'profil'])->name('settings.profil');
    Route::get('/settings/tampilan',    [SettingsController::class, 'tampilan'])->name('settings.tampilan');
    Route::get('/settings/langganan',   [SettingsController::class, 'langganan'])->name('settings.langganan');
    Route::get('/settings/referral',    [SettingsController::class, 'referral'])->name('settings.referral');
    Route::post('/settings/referral/payout', [SettingsController::class, 'requestPayout'])->name('settings.referral.payout');
    // Settings — POST actions
    Route::post('/settings/profile',        [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::post('/settings/password',       [SettingsController::class, 'updatePassword'])->name('settings.password');
    Route::post('/settings/toggle-feature', [SettingsController::class, 'toggleFeature'])->name('settings.toggle-feature');
    Route::post('/settings/features',        [SettingsController::class, 'saveFeatures'])->name('settings.features.save');
    Route::post('/settings/onboarding',     [SettingsController::class, 'updateOnboarding'])->name('settings.onboarding');

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
        // Practice Q&A
        Route::post('/qa',                     [PersiapanController::class, 'storeQA'])->name('qa.store');
        Route::post('/qa/{id}',                [PersiapanController::class, 'updateQA'])->name('qa.update');
        Route::delete('/qa/{id}',              [PersiapanController::class, 'destroyQA'])->name('qa.destroy');
    });
});
