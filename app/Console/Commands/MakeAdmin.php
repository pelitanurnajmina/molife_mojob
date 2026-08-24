<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\Profile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MakeAdmin extends Command
{
    protected $signature = 'admin:make {email : Email akun admin}
                            {--name= : Nama tampilan (opsional)}
                            {--password= : Set password (opsional; kalau kosong & akun baru, digenerate)}
                            {--revoke : Cabut status admin dari akun ini}';

    protected $description = 'Jadikan sebuah akun sebagai super admin (buat akun bila belum ada), atau cabut dengan --revoke';

    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email')));

        // Cabut admin
        if ($this->option('revoke')) {
            $user = User::where('email', $email)->first();
            if (!$user) { $this->error("Akun '{$email}' tidak ditemukan."); return 1; }
            $user->is_admin = false;
            $user->save();
            $this->info("Status admin dicabut dari {$email}.");
            return 0;
        }

        $user     = User::where('email', $email)->first();
        $tempPass = null;

        // Buat akun bila belum ada
        if (!$user) {
            $tempPass = $this->option('password') ?: Str::password(12, symbols: false);
            $user = User::create([
                'email'    => $email,
                'username' => $this->uniqueUsername($email),
                'name'     => $this->option('name') ?: Str::before($email, '@'),
                'password' => Hash::make($tempPass),
            ]);
            // Profil lengkap supaya tidak nyangkut di onboarding kalau membuka aplikasi.
            $p = Profile::model($user->id);
            $p->display_name = $this->option('name') ?: 'Admin';
            $p->setup_done   = true;
            $p->religion     = 'islam';
            $p->save();
        } elseif ($this->option('password')) {
            // Akun sudah ada + minta ganti password
            $user->password = Hash::make($this->option('password'));
            $user->save();
        }

        $user->is_admin = true;
        $user->save();

        $this->info("Akun {$email} sekarang admin (id {$user->id}).");
        if ($tempPass) {
            $this->newLine();
            $this->warn("Password login (simpan, tampil sekali): {$tempPass}");
            $this->line('Bisa diganti nanti lewat "Lupa password".');
        }
        return 0;
    }

    private function uniqueUsername(string $email): string
    {
        $base = Str::slug(Str::before($email, '@'), '') ?: 'admin';
        $username = $base;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $base . $i;
            $i++;
        }
        return $username;
    }
}
