# Molife — Laravel Version

Personal life tracker: Sholat, Gym, Intimasi, Tasks, Statistik, Goals & Reminder.

## Stack
- **PHP** 8.2+
- **Laravel** 11
- **Storage**: PHP Session (no database required for data — replaces localStorage)
- **Frontend**: Blade templates + Tailwind CSS CDN + Chart.js CDN

---

## Cara Install & Jalankan

### 1. Clone / extract project

```bash
cd molife-laravel
```

### 2. Install dependencies

```bash
composer install
```

### 3. Setup environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Jalankan server lokal

```bash
php artisan serve
```

Buka http://localhost:8000 di browser.

---

## Cara Deploy ke Production (Apache/Nginx)

1. Upload ke server
2. Set document root ke folder `public/`
3. Pastikan `storage/` dan `bootstrap/cache/` writable:
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```
4. Copy `.env.example` ke `.env`, isi `APP_KEY`:
   ```bash
   php artisan key:generate
   ```

---

## Struktur Data

Semua data user disimpan di **Laravel Session** (file-based, di `storage/framework/sessions/`). Ini menggantikan localStorage dari versi React.

Data shape (identik dengan versi original):
```json
{
  "sholat":      { "YYYY-MM-DD": { "wajib": {...}, "sunnah": [...] } },
  "gym":         { "YYYY-MM-DD": { "done": true, "calories": 300 } },
  "intimacy":    { "YYYY-MM-DD": 2 },
  "todos":       { "daily": { "YYYY-MM-DD": [...] }, "weekly": { "YYYY-MM-DD": [...] } },
  "notes":       { "YYYY-MM-DD": "..." },
  "reflections": { "YYYY-MM-DD": { "good": "...", "improve": "..." } },
  "goals":       { "YYYY-MM": { "sholat": 25, "gym": 16, "intimacy": 12 } },
  "reminders":   { "Subuh": "04:30", ... }
}
```

> Untuk multi-user atau persistent storage, engineer dapat migrasi `UserStorage` ke database (Eloquent + migrations) dengan mengubah `fromSession()` dan `save()`.

---

## Autentikasi

Saat ini menggunakan **simple session auth** — siapapun bisa login (cocok untuk aplikasi pribadi single user). Untuk menambahkan password protection, edit `AuthController::login()`:

```php
public function login(Request $request)
{
    if ($request->password !== config('app.molife_password', 'your_secret')) {
        return back()->withErrors(['password' => 'Password salah.']);
    }
    session(['molife_logged_in' => true]);
    return redirect()->route('dashboard');
}
```

Dan tambahkan di `.env`:
```
MOLIFE_PASSWORD=your_secret_password
```

---

## Fitur

| Halaman     | URL           | Fitur |
|-------------|---------------|-------|
| Dashboard   | `/`           | Overview, streak, weekly chart |
| Sholat      | `/sholat`     | Log wajib + takbir + rawatib + sunnah, calendar, backfill |
| Gym         | `/gym`        | Toggle sesi, kalori, kalender mingguan |
| Intimasi    | `/intimasi`   | Counter per tanggal, kalender bulanan |
| Tasks       | `/tasks`      | Daily & weekly todos, refleksi, notes |
| Statistik   | `/statistik`  | 30-day heatmap, doughnut chart |
| Goals       | `/goals`      | Target bulanan, reminder scheduler |

---

## File Penting

```
app/
  Models/UserStorage.php       ← Semua logic data (ganti localStorage)
  Http/Controllers/            ← 8 controllers (1 per fitur)
  Http/Middleware/SimpleAuth.php

resources/views/
  layouts/app.blade.php        ← Layout utama (sidebar, mobile nav)
  pages/
    login.blade.php
    dashboard.blade.php
    sholat.blade.php
    gym.blade.php
    intimasi.blade.php
    tasks.blade.php
    statistik.blade.php
    goals.blade.php

routes/web.php                 ← Semua route
```
