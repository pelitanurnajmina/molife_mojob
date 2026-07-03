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
use App\Http\Controllers\PomodoroController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\LamaranController;
use App\Http\Controllers\PersiapanController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\StatistikKarirController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\BisnisController;
use App\Http\Controllers\BisnisDocController;

/* ── Language switcher ── */
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');

/* ── Public landing page ── */
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : view('landing');
})->name('landing');

/* ── Blog (public, SEO) ── */
Route::get('/blog',          [\App\Http\Controllers\BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}',   [\App\Http\Controllers\BlogController::class, 'show'])->name('blog.show');
Route::get('/sitemap.xml',   [\App\Http\Controllers\BlogController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', function () {
    $body = "User-agent: *\nAllow: /\n\nSitemap: " . route('sitemap') . "\n";
    return response($body, 200)->header('Content-Type', 'text/plain');
});

Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');

// Lupa password
Route::get('/forgot-password',        [\App\Http\Controllers\PasswordResetController::class, 'request'])->name('password.request');
Route::post('/forgot-password',       [\App\Http\Controllers\PasswordResetController::class, 'email'])->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\PasswordResetController::class, 'reset'])->name('password.reset');
Route::post('/reset-password',        [\App\Http\Controllers\PasswordResetController::class, 'update'])->name('password.update');

// Login with Google (Socialite)
Route::get('/auth/google',          [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Onboarding (auth required, but no onboarding check here)
Route::middleware('auth.simple')->group(function () {
    Route::get('/onboarding',  [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

// Subscription paywall + activation (auth + onboarded, but NOT behind the paywall itself)
Route::middleware(['auth.simple', 'require.onboarding'])->group(function () {
    Route::get('/subscribe',            [SubscriptionController::class, 'page'])->name('subscribe');
    Route::get('/subscription/status',  [SubscriptionController::class, 'status'])->name('subscription.status');
    Route::post('/subscription/charge', [SubscriptionController::class, 'charge'])->name('subscription.charge');
});

// Midtrans payment notification (public webhook — verified by signature, no CSRF)
Route::post('/subscription/webhook', [SubscriptionController::class, 'webhook'])->name('subscription.webhook');

// Kolaborasi bisnis (sisi kolaborator) — TANPA paywall langganan: undangan hanya
// membuka workspace produk tertentu, bukan seluruh aplikasi.
Route::get('/kolaborasi/terima/{token}', [\App\Http\Controllers\BisnisCollabController::class, 'accept'])->name('kolaborasi.terima');
Route::middleware(['auth.simple', 'require.onboarding'])->prefix('kolaborasi')->name('kolaborasi.')->group(function () {
    Route::get('/',        [\App\Http\Controllers\BisnisCollabController::class, 'index'])->name('index');
    Route::get('/{productId}', [\App\Http\Controllers\BisnisCollabController::class, 'workspace'])->whereNumber('productId')->name('workspace');
    Route::post('/{productId}/proposal',          [\App\Http\Controllers\BisnisCollabController::class, 'storeDeal'])->name('deal.store');
    Route::post('/{productId}/proposal/{id}',     [\App\Http\Controllers\BisnisCollabController::class, 'updateDeal'])->name('deal.update');
    Route::delete('/{productId}/proposal/{id}',   [\App\Http\Controllers\BisnisCollabController::class, 'destroyDeal'])->name('deal.destroy');
    Route::post('/{productId}/template',          [\App\Http\Controllers\BisnisCollabController::class, 'storeTemplate'])->name('template.store');
    Route::post('/{productId}/template/{id}',     [\App\Http\Controllers\BisnisCollabController::class, 'updateTemplate'])->name('template.update');
    Route::delete('/{productId}/template/{id}',   [\App\Http\Controllers\BisnisCollabController::class, 'destroyTemplate'])->name('template.destroy');
});

Route::middleware(['auth.simple', 'require.onboarding', 'require.subscription'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/tour/done', [DashboardController::class, 'completeTour'])->name('tour.done');
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
    Route::post('/sholat/toggle-excused', [SholatController::class, 'toggleExcused'])->name('sholat.toggle-excused');

    // Spiritual (non-Islam)
    Route::get('/spiritual',        [SpiritualController::class, 'index'])->name('spiritual');
    Route::post('/spiritual/toggle',[SpiritualController::class, 'toggle'])->name('spiritual.toggle');

    // Quit trackers (stop porn / kurangi sosmed)
    Route::get('/quit/{type}',          [QuitController::class, 'index'])->name('quit');
    Route::post('/quit/{type}/relapse', [QuitController::class, 'relapse'])->name('quit.relapse');

    // Motivasi (quote, afirmasi, vision board, alasan besar)
    Route::get('/motivasi',            [MotivasiController::class, 'index'])->name('motivasi');
    Route::post('/motivasi/favorite',  [MotivasiController::class, 'toggleFavorite'])->name('motivasi.favorite');
    Route::delete('/motivasi/favorite/{id}', [MotivasiController::class, 'deleteFavorite'])->name('motivasi.favorite.delete');
    Route::post('/motivasi/why',       [MotivasiController::class, 'saveWhy'])->name('motivasi.why');
    Route::post('/motivasi/vision',    [MotivasiController::class, 'addVision'])->name('motivasi.vision.add');
    Route::delete('/motivasi/vision/{id}', [MotivasiController::class, 'deleteVision'])->name('motivasi.vision.delete');

    // Pomodoro focus timer
    Route::get('/pomodoro',  [PomodoroController::class, 'index'])->name('pomodoro');
    Route::post('/pomodoro', [PomodoroController::class, 'store'])->name('pomodoro.store');

    // Journal (Law of Attraction, guided)
    Route::get('/journal',  [JournalController::class, 'index'])->name('journal');
    Route::post('/journal', [JournalController::class, 'store'])->name('journal.store');
    Route::delete('/journal/{id}', [JournalController::class, 'destroy'])->name('journal.destroy');

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
    Route::get('/tasks/history', [TaskController::class, 'history'])->name('tasks.history');

    // Link Penting
    Route::get('/links',        [LinkController::class, 'index'])->name('links');
    Route::post('/links',       [LinkController::class, 'store'])->name('links.store');
    Route::post('/links/{id}',  [LinkController::class, 'update'])->name('links.update');
    Route::delete('/links/{id}',[LinkController::class, 'destroy'])->name('links.destroy');
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
    Route::get('/karir/lowongan', [StatistikKarirController::class, 'lowongan'])->name('karir.lowongan');
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

    // Bisnis (proposal/klien + dokumen + analitik)
    Route::prefix('bisnis')->name('bisnis.')->group(function () {
        Route::get('/',              [BisnisController::class, 'index'])->name('index');
        Route::get('/proposal',      [BisnisController::class, 'deals'])->name('deals');
        Route::post('/proposal',     [BisnisController::class, 'store'])->name('store');
        Route::post('/proposal/{id}',[BisnisController::class, 'update'])->name('update');
        Route::delete('/proposal/{id}', [BisnisController::class, 'destroy'])->name('destroy');
        // Products
        Route::post('/produk',        [BisnisController::class, 'storeProduct'])->name('product.store');
        Route::delete('/produk/{id}', [BisnisController::class, 'destroyProduct'])->name('product.destroy');
        // Kolaborasi per produk (sisi owner, dikelola dari folder produk /kolaborasi/{id})
        Route::post('/produk/{id}/kolaborator',  [\App\Http\Controllers\BisnisCollabController::class, 'invite'])->name('collab.invite');
        Route::delete('/kolaborator/{collabId}', [\App\Http\Controllers\BisnisCollabController::class, 'removeMember'])->name('collab.remove');
        // Documents
        Route::get('/dokumen',          [BisnisDocController::class, 'index'])->name('docs');
        Route::post('/dokumen/link',    [BisnisDocController::class, 'storeLink'])->name('docs.link');
        Route::post('/dokumen/file',    [BisnisDocController::class, 'storeFile'])->name('docs.file');
        Route::get('/dokumen/{id}/unduh', [BisnisDocController::class, 'downloadFile'])->name('docs.download');
        Route::post('/dokumen/template',      [BisnisDocController::class, 'storeTemplate'])->name('docs.template.store');
        Route::post('/dokumen/template/{id}', [BisnisDocController::class, 'updateTemplate'])->name('docs.template.update');
        Route::delete('/dokumen/{id}',  [BisnisDocController::class, 'destroy'])->name('docs.destroy');
    });

    // Finance
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/',                  [FinanceController::class, 'index'])->name('index');
        Route::get('/transaksi',         [FinanceController::class, 'transaksi'])->name('transaksi');
        Route::post('/transaksi',        [FinanceController::class, 'addTransaction'])->name('transaksi.add');
        Route::post('/scan-struk',       [FinanceController::class, 'scanReceipt'])->name('scan-struk');
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
