<?php

namespace Database\Seeders;

use App\Models\BusinessDeal;
use App\Models\BusinessDoc;
use App\Models\BusinessProduct;
use App\Models\CareerGoal;
use App\Models\FinanceBudget;
use App\Models\FinanceSavingsGoal;
use App\Models\FinanceTransaction;
use App\Models\GymLog;
use App\Models\JobApplication;
use App\Models\JournalEntry;
use App\Models\MoodLog;
use App\Models\PomodoroSession;
use App\Models\SholatPrayer;
use App\Models\Subscription;
use App\Models\Todo;
use App\Models\User;
use App\Support\Features;
use App\Support\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun demo lengkap: langganan aktif 1 tahun + data dummy di Life, Karir,
 * Bisnis, dan Finance. Aman dijalankan berulang (data lama demo dihapus dulu).
 *
 * Jalankan: php artisan db:seed --class=DemoUserSeeder
 * Login:    demo@molife.space / demo1234
 */
class DemoUserSeeder extends Seeder
{
    public const EMAIL    = 'demo@molife.space';
    public const PASSWORD = 'demo1234';

    public function run(): void
    {
        /* ── User + profil ── */
        $user = User::updateOrCreate(
            ['email' => self::EMAIL],
            ['username' => 'demo', 'password' => Hash::make(self::PASSWORD)]
        );
        $uid = $user->id;

        $profile = Profile::model($uid);
        $profile->fill([
            'display_name' => 'Demo Molife',
            'religion'     => 'islam',
            'gender'       => 'male',
            'prayer_city'  => 'Jakarta',
            'setup_done'   => true,
            'tour_done'    => true,
            'plan'         => 'pro',
        ])->save();

        Features::set($uid, 'sholat', true);
        Features::set($uid, 'gym', true);
        Features::set($uid, 'run', true);

        /* ── Bersihkan data demo lama (idempotent) ── */
        foreach ([
            Subscription::class, SholatPrayer::class, MoodLog::class, Todo::class,
            PomodoroSession::class, GymLog::class, JournalEntry::class,
            FinanceTransaction::class, FinanceBudget::class, FinanceSavingsGoal::class,
            CareerGoal::class, JobApplication::class,
            BusinessDeal::class, BusinessDoc::class, BusinessProduct::class,
        ] as $model) {
            $model::where('user_id', $uid)->delete();
        }

        /* ── Langganan aktif 1 tahun ── */
        Subscription::create([
            'user_id'   => $uid,
            'plan'      => '12',
            'months'    => 12,
            'price'     => 89000,
            'status'    => 'active',
            'ref'       => 'DEMO-SEED-' . now()->format('YmdHis'),
            'starts_at' => now()->toDateString(),
            'ends_at'   => now()->addYear()->toDateString(),
            'paid_at'   => now(),
        ]);

        /* ── Life: sholat 14 hari terakhir ── */
        foreach (range(0, 13) as $i) {
            $date = now()->subDays($i)->toDateString();
            foreach (['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'] as $name) {
                SholatPrayer::create([
                    'user_id'        => $uid,
                    'date'           => $date,
                    'name'           => $name,
                    'done'           => true,
                    'takbir_pertama' => rand(0, 100) < 60,
                    'rawatib'        => rand(0, 100) < 40,
                ]);
            }
        }

        /* ── Life: mood 7 hari ── */
        $moodNotes = ['Produktif banget hari ini', 'Agak capek tapi lancar', 'Alhamdulillah tenang', 'Banyak meeting', 'Fokus kerja', 'Santai sama keluarga', 'Semangat pagi'];
        foreach (range(0, 6) as $i) {
            MoodLog::create([
                'user_id' => $uid,
                'date'    => now()->subDays($i)->toDateString(),
                'score'   => rand(3, 5),
                'energy'  => rand(3, 5),
                'note'    => $moodNotes[$i],
            ]);
        }

        /* ── Life: tugas harian + pomodoro + gym ── */
        foreach ([
            ['Follow up klien PT Maju Jaya', 1, true],
            ['Kirim proposal Camemo ke CV Sinar', 1, true],
            ['Review laporan keuangan bulanan', 2, false],
            ['Update konten landing page', 3, false],
        ] as [$text, $prio, $done]) {
            Todo::create([
                'user_id' => $uid, 'scope' => 'daily', 'period_key' => now()->toDateString(),
                'text' => $text, 'priority' => $prio, 'done' => $done,
            ]);
        }
        foreach (range(0, 4) as $i) {
            PomodoroSession::create([
                'user_id' => $uid, 'date' => now()->subDays($i)->toDateString(),
                'focus_minutes' => [25, 50, 25, 75, 50][$i], 'label' => ['Coding', 'Proposal', 'Riset', 'Desain', 'Laporan'][$i],
            ]);
        }
        foreach ([1, 3, 5] as $i) {
            GymLog::create(['user_id' => $uid, 'date' => now()->subDays($i)->toDateString(), 'done' => true, 'calories' => rand(250, 450)]);
        }

        /* ── Life: jurnal ── */
        JournalEntry::create([
            'user_id' => $uid, 'date' => now()->subDay()->toDateString(), 'template' => 'harian',
            'content' => "Hari ini berhasil closing satu klien baru untuk Camemo. Besok mau fokus follow up dua prospek yang masih negosiasi.",
        ]);
        JournalEntry::create([
            'user_id' => $uid, 'date' => now()->subDays(3)->toDateString(), 'template' => 'harian',
            'content' => "Latihan gym rutin mulai konsisten. Target bulan ini: sholat takbir pertama minimal 80%.",
        ]);

        /* ── Karir ── */
        CareerGoal::create([
            'user_id' => $uid, 'target_role' => 'Product Manager', 'target_company' => 'Startup Teknologi',
            'target_salary' => 15000000, 'target_date' => now()->addMonths(6)->toDateString(),
            'notes' => 'Fokus perkuat portofolio produk & studi kasus.',
        ]);
        foreach ([
            ['Tokopedia', 'Product Manager', 'Jakarta', 15000000, 12, 'interview', 'full-time', 'LinkedIn'],
            ['Gojek', 'Associate PM', 'Jakarta', 13000000, 8, 'applied', 'full-time', 'Website'],
            ['Startup Lokal', 'Product Owner', 'Remote', 12000000, 20, 'review', 'remote', 'Referral'],
        ] as [$company, $pos, $loc, $sal, $days, $status, $type, $channel]) {
            JobApplication::create([
                'user_id' => $uid, 'company' => $company, 'position' => $pos, 'location' => $loc,
                'salary' => $sal, 'applied_date' => now()->subDays($days)->toDateString(),
                'status' => $status, 'job_type' => $type, 'channel' => $channel,
            ]);
        }

        /* ── Bisnis: produk + proposal + template ── */
        $camemo = BusinessProduct::create(['user_id' => $uid, 'name' => 'Camemo']);
        $molife = BusinessProduct::create(['user_id' => $uid, 'name' => 'Molife']);

        foreach ([
            ['PT Maju Jaya', 'F&B', 'Camemo', 7500000, 'won', 15, 'Deal! Mulai pengerjaan minggu depan.'],
            ['CV Sinar Abadi', 'Retail', 'Camemo', 5000000, 'negotiation', 5, 'Minta revisi harga, follow up Kamis.'],
            ['Wedding Organizer Bahagia', 'Event', 'Camemo', 3500000, 'sent', 2, null],
            ['PT Teknologi Nusantara', 'Tech', 'Molife', 12000000, 'lead', 1, 'Kenalan dari event startup.'],
            ['Klinik Sehat Sentosa', 'Kesehatan', 'Molife', 8000000, 'won', 30, null],
            ['Toko Berkah', 'Retail', 'Camemo', 2500000, 'lost', 40, 'Budget belum ada, coba lagi kuartal depan.'],
        ] as [$client, $industry, $product, $value, $status, $days, $notes]) {
            BusinessDeal::create([
                'user_id' => $uid, 'client_name' => $client, 'industry' => $industry,
                'product' => $product, 'value' => $value, 'status' => $status,
                'proposal_date' => now()->subDays($days)->toDateString(), 'notes' => $notes,
                'contact' => '08123456789',
            ]);
        }

        BusinessDoc::create([
            'user_id' => $uid, 'business_product_id' => $camemo->id, 'kind' => 'template',
            'title' => 'Penawaran Awal WA', 'category' => 'whatsapp',
            'content' => "Halo kak! Perkenalkan, saya dari Camemo, platform undangan digital + guestbook QR untuk acara spesial kakak. Boleh saya kirimkan contoh demo dan pricelist-nya? Terima kasih kak!",
        ]);
        BusinessDoc::create([
            'user_id' => $uid, 'business_product_id' => $camemo->id, 'kind' => 'template',
            'title' => 'Follow Up Penawaran', 'category' => 'followup',
            'content' => "Halo kak, menindaklanjuti penawaran Camemo beberapa hari lalu. Apakah ada pertanyaan yang bisa saya bantu jelaskan? Kami ada promo khusus bulan ini.",
        ]);
        BusinessDoc::create([
            'user_id' => $uid, 'kind' => 'template',
            'title' => 'Email Proposal Umum', 'category' => 'email',
            'content' => "Yth. Bapak/Ibu,\n\nBersama email ini kami lampirkan proposal penawaran kerja sama. Kami terbuka untuk diskusi lebih lanjut.\n\nHormat kami,\nDemo Molife",
        ]);

        /* ── Finance: transaksi 30 hari + anggaran + tabungan ── */
        FinanceTransaction::create([
            'user_id' => $uid, 'type' => 'income', 'date' => now()->startOfMonth()->toDateString(),
            'category' => 'Gaji', 'amount' => 12000000, 'note' => 'Gaji bulanan',
        ]);
        FinanceTransaction::create([
            'user_id' => $uid, 'type' => 'income', 'date' => now()->subDays(10)->toDateString(),
            'category' => 'Bisnis', 'amount' => 7500000, 'note' => 'Pembayaran klien PT Maju Jaya',
        ]);
        foreach ([
            ['Makanan', 45000, 1, 'Makan siang'], ['Transportasi', 25000, 1, 'Bensin'],
            ['Makanan', 120000, 2, 'Belanja mingguan'], ['Tagihan', 350000, 3, 'Listrik & internet'],
            ['Hiburan', 50000, 5, 'Nonton'], ['Makanan', 38000, 6, 'Makan malam keluarga'],
            ['Kesehatan', 150000, 8, 'Vitamin'], ['Belanja', 275000, 12, 'Kemeja kerja'],
            ['Transportasi', 90000, 15, 'Grab ke meeting klien'], ['Sewa', 1500000, 20, 'Kos bulanan'],
        ] as [$cat, $amount, $days, $note]) {
            FinanceTransaction::create([
                'user_id' => $uid, 'type' => 'expense', 'date' => now()->subDays($days)->toDateString(),
                'category' => $cat, 'amount' => $amount, 'note' => $note,
            ]);
        }

        $monthKey = now()->format('Y-m');
        foreach ([['Makanan', 2000000], ['Transportasi', 500000], ['Hiburan', 300000], ['Tagihan', 600000]] as [$cat, $amount]) {
            FinanceBudget::create(['user_id' => $uid, 'month_key' => $monthKey, 'category' => $cat, 'amount' => $amount]);
        }

        FinanceSavingsGoal::create([
            'user_id' => $uid, 'name' => 'Dana Darurat', 'target' => 30000000,
            'current' => 12500000, 'deadline' => now()->addMonths(8)->toDateString(), 'color' => 'emerald',
        ]);
        FinanceSavingsGoal::create([
            'user_id' => $uid, 'name' => 'Laptop Baru', 'target' => 20000000,
            'current' => 6000000, 'deadline' => now()->addMonths(5)->toDateString(), 'color' => 'violet',
        ]);

        $this->command?->info('Akun demo siap: ' . self::EMAIL . ' / ' . self::PASSWORD . ' (langganan aktif s.d. ' . now()->addYear()->toDateString() . ')');
    }
}
