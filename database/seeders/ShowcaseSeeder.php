<?php

namespace Database\Seeders;

use App\Models\BusinessDeal;
use App\Models\BusinessDoc;
use App\Models\BusinessProduct;
use App\Models\CareerGoal;
use App\Models\CollabTask;
use App\Models\Contact;
use App\Models\FinanceBudget;
use App\Models\FinanceSavingsGoal;
use App\Models\FinanceTransaction;
use App\Models\Goal;
use App\Models\GymLog;
use App\Models\IdeaScript;
use App\Models\ImportantLink;
use App\Models\Interview;
use App\Models\IntimacyLog;
use App\Models\JobApplication;
use App\Models\JournalEntry;
use App\Models\MeditationSession;
use App\Models\MoodLog;
use App\Models\Note;
use App\Models\PomodoroSession;
use App\Models\PrepLink;
use App\Models\PrepQa;
use App\Models\PrepTemplate;
use App\Models\QuitRelapse;
use App\Models\QuitTracker;
use App\Models\QuoteFavorite;
use App\Models\Reflection;
use App\Models\Reminder;
use App\Models\RunLog;
use App\Models\SholatPrayer;
use App\Models\SholatSunnah;
use App\Models\Subscription;
use App\Models\TimeBlock;
use App\Models\Todo;
use App\Models\User;
use App\Models\VisionItem;
use App\Support\Dates;
use App\Support\Features;
use App\Support\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * AKUN PERAGA (SHOWCASE) untuk kebutuhan konten/marketing.
 *
 * Mengisi SEMUA modul dengan data yang terlihat "sudah dipakai lama",
 * dan menandai akun dengan users.is_showcase = true supaya halaman
 * memutar animasi isi-otomatis (hanya untuk akun ini).
 *
 * Jalankan: php artisan db:seed --class=ShowcaseSeeder
 * Login:    showcase@molife.space / showcase123
 *
 * AMAN: idempotent (data lama akun ini dihapus dulu) dan tidak menyentuh
 * akun/user lain sama sekali.
 */
class ShowcaseSeeder extends Seeder
{
    public const EMAIL    = 'showcase@molife.space';
    public const PASSWORD = 'showcase123';

    private int $uid;

    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => self::EMAIL],
            ['username' => 'showcase', 'password' => Hash::make(self::PASSWORD)]
        );
        $user->is_showcase = true;
        $user->save();

        $this->uid = $user->id;

        $this->profileAndFeatures();
        $this->wipe();
        $this->subscription();
        $this->life();
        $this->productivity();
        $this->career();
        $this->business();
        $this->finance();

        $this->command?->info('Akun peraga siap: ' . self::EMAIL . ' / ' . self::PASSWORD);
    }

    /* ─────────────────────────── Profil & fitur ─────────────────────────── */

    private function profileAndFeatures(): void
    {
        Profile::model($this->uid)->fill([
            'display_name'  => 'Arif Rahman',
            'religion'      => 'islam',
            'gender'        => 'male',
            'prayer_city'   => 'Jakarta',
            'setup_done'    => true,
            'tour_done'     => true,
            'plan'          => 'pro',
            'sport_target'  => 5,
        ])->save();

        // Semua modul utama dinyalakan supaya dashboard & menu terlihat penuh.
        // 'porn' sengaja dibiarkan mati (tidak cocok untuk materi marketing);
        // 'haid' tidak relevan karena persona laki-laki.
        $on = [
            'timeblock', 'ide_script', 'sholat', 'gym', 'run', 'intimasi',
            'motivasi', 'pomodoro', 'meditasi', 'journal', 'tasks', 'links',
            'statistik', 'goals', 'lamaran', 'persiapan', 'bisnis', 'mental',
            'insights', 'finance', 'sosmed',
        ];
        foreach ($on as $f) {
            Features::set($this->uid, $f, true);
        }
        foreach (['cycling', 'swimming', 'racket', 'custom_sport', 'porn', 'haid'] as $f) {
            Features::set($this->uid, $f, false);
        }
    }

    /** Hapus data lama akun peraga saja (idempotent). */
    private function wipe(): void
    {
        foreach ([
            Subscription::class, SholatPrayer::class, SholatSunnah::class, MoodLog::class,
            MeditationSession::class, GymLog::class, RunLog::class, IntimacyLog::class,
            Todo::class, PomodoroSession::class, JournalEntry::class, Reflection::class,
            Note::class, TimeBlock::class, IdeaScript::class, ImportantLink::class,
            QuoteFavorite::class, VisionItem::class, Goal::class, Reminder::class,
            QuitTracker::class, QuitRelapse::class,
            CareerGoal::class, JobApplication::class, Interview::class,
            PrepLink::class, PrepTemplate::class, PrepQa::class, Contact::class,
            FinanceTransaction::class, FinanceBudget::class, FinanceSavingsGoal::class,
            BusinessDeal::class, BusinessDoc::class,
        ] as $model) {
            $model::where('user_id', $this->uid)->delete();
        }

        // Tugas kolaborasi mengikuti produk, hapus lewat produk.
        $productIds = BusinessProduct::where('user_id', $this->uid)->pluck('id');
        if ($productIds->isNotEmpty()) {
            CollabTask::whereIn('business_product_id', $productIds)->delete();
        }
        BusinessProduct::where('user_id', $this->uid)->delete();
    }

    private function subscription(): void
    {
        Subscription::create([
            'user_id'   => $this->uid,
            'plan'      => '12',
            'months'    => 12,
            'price'     => 89000,
            'status'    => 'active',
            'ref'       => 'SHOWCASE-' . now()->format('YmdHis'),
            'starts_at' => now()->subMonths(4)->toDateString(),
            'ends_at'   => now()->addMonths(8)->toDateString(),
            'paid_at'   => now()->subMonths(4),
        ]);
    }

    /* ───────────────────────────── Life ───────────────────────────── */

    private function life(): void
    {
        $uid = $this->uid;

        // Sholat 5 waktu, 90 hari, konsistensi tinggi (terlihat rajin).
        foreach (range(0, 89) as $i) {
            $date = now()->subDays($i)->toDateString();
            foreach (['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'] as $name) {
                SholatPrayer::create([
                    'user_id'        => $uid,
                    'date'           => $date,
                    'name'           => $name,
                    'done'           => rand(0, 100) < 96,
                    'takbir_pertama' => rand(0, 100) < 72,
                    'rawatib'        => rand(0, 100) < 58,
                ]);
            }
            // Sunnah: Dhuha & Tahajud cukup sering.
            if (rand(0, 100) < 65) SholatSunnah::create(['user_id' => $uid, 'date' => $date, 'name' => 'Dhuha']);
            if (rand(0, 100) < 40) SholatSunnah::create(['user_id' => $uid, 'date' => $date, 'name' => 'Tahajud']);
        }

        // Mood 60 hari
        $moodNotes = [
            'Produktif, closing satu klien', 'Fokus penuh seharian', 'Agak lelah tapi target tercapai',
            'Alhamdulillah tenang', 'Banyak meeting, tetap terkendali', 'Semangat pagi, olahraga lancar',
            'Santai bareng keluarga', 'Deadline padat tapi selesai', 'Hari refleksi yang baik',
        ];
        foreach (range(0, 59) as $i) {
            MoodLog::create([
                'user_id' => $uid,
                'date'    => now()->subDays($i)->toDateString(),
                'score'   => rand(3, 5),
                'energy'  => rand(3, 5),
                'note'    => $moodNotes[array_rand($moodNotes)],
            ]);
        }

        // Meditasi 60 hari (sebagian besar hari)
        $sounds = ['hujan', 'ombak', 'angin'];
        foreach (range(0, 59) as $i) {
            if (rand(0, 100) < 78) {
                MeditationSession::create([
                    'user_id' => $uid,
                    'date'    => now()->subDays($i)->toDateString(),
                    'minutes' => [10, 10, 15, 12, 20][array_rand([0, 1, 2, 3, 4])],
                    'sound'   => $sounds[array_rand($sounds)],
                    'note'    => null,
                ]);
            }
        }

        // Olahraga: gym & lari bergantian selama 10 minggu (±5x/minggu)
        foreach (range(0, 69) as $i) {
            $date = now()->subDays($i)->toDateString();
            $dow  = (int) now()->subDays($i)->dayOfWeek;
            if (in_array($dow, [1, 3, 5], true)) {
                GymLog::create(['user_id' => $uid, 'date' => $date, 'done' => true, 'calories' => rand(280, 480)]);
            }
            if (in_array($dow, [2, 6], true)) {
                $dist = round(rand(30, 80) / 10, 1);
                RunLog::create([
                    'user_id'  => $uid, 'date' => $date, 'done' => true,
                    'distance' => $dist, 'duration' => (int) round($dist * rand(6, 7)),
                    'type'     => 'outdoor', 'calories' => (int) ($dist * 65),
                    'notes'    => null,
                ]);
            }
        }

        // Intimasi (pasangan) beberapa kali per bulan
        foreach ([2, 6, 11, 16, 21, 27, 33, 40, 48] as $i) {
            IntimacyLog::create(['user_id' => $uid, 'date' => now()->subDays($i)->toDateString(), 'count' => 1]);
        }

        // Jurnal & refleksi
        $journals = [
            'Closing klien baru untuk Camemo hari ini. Pelajaran: follow up konsisten itu kuncinya, bukan penawaran yang paling murah.',
            'Sholat subuh berjamaah 7 hari berturut-turut. Efeknya ke mood dan fokus kerja kerasa banget.',
            'Evaluasi keuangan bulan ini: pengeluaran makan masih di atas anggaran. Bulan depan mau masak lebih sering.',
            'Wawancara tahap 2 berjalan lancar. Bagian studi kasus produk terasa paling kuat.',
            'Mulai konsisten meditasi 10 menit tiap pagi. Pikiran jauh lebih tenang sebelum mulai kerja.',
            'Rapat tim untuk Molife. Sepakat fokus ke tiga fitur inti dulu sebelum menambah yang lain.',
            'Lari 8 km pagi ini, rekor terbaik bulan ini. Target bulan depan naik jadi 10 km.',
            'Menyusun ulang prioritas mingguan. Terlalu banyak tugas kecil yang sebenarnya bisa didelegasikan.',
        ];
        foreach ($journals as $k => $content) {
            JournalEntry::create([
                'user_id' => $uid, 'date' => now()->subDays($k * 4 + 1)->toDateString(),
                'template' => 'harian', 'content' => $content,
            ]);
        }

        $reflections = [
            ['Konsisten sholat tepat waktu dan olahraga 5x minggu ini.', 'Kurang disiplin tidur, sering lewat jam 12 malam.'],
            ['Berhasil closing dua proposal sekaligus.', 'Respon ke klien kadang terlalu lama, perlu dipercepat.'],
            ['Pengeluaran bulan ini terkendali di bawah anggaran.', 'Masih sering jajan kopi di luar rencana.'],
            ['Fokus kerja naik setelah rutin meditasi pagi.', 'Terlalu banyak buka sosial media saat jeda kerja.'],
            ['Progres belajar produk berjalan sesuai rencana.', 'Perlu lebih banyak latihan studi kasus untuk wawancara.'],
        ];
        foreach ($reflections as $k => [$good, $improve]) {
            Reflection::create([
                'user_id' => $uid, 'date' => now()->subDays($k * 5 + 2)->toDateString(),
                'good' => $good, 'improve' => $improve,
            ]);
        }

        foreach ([
            'Ide konten: bandingkan rutinitas pagi produktif vs berantakan, pakai data Life Score sendiri.',
            'Checklist persiapan meeting klien: riset perusahaan, siapkan tiga pertanyaan, bawa studi kasus.',
            'Rencana kuartal depan: tambah dua klien retainer, turunkan biaya operasional 15%.',
        ] as $k => $content) {
            Note::create(['user_id' => $uid, 'date' => now()->subDays($k * 3 + 1)->toDateString(), 'content' => $content]);
        }

        // Papan visi, kutipan favorit, tautan penting
        foreach ([
            ['🕌', 'Sholat lima waktu tepat waktu setiap hari'],
            ['💼', 'Punya tiga klien retainer yang stabil'],
            ['🏃', 'Lari 10 km tanpa berhenti'],
            ['📚', 'Khatam satu buku bisnis setiap bulan'],
            ['🏠', 'DP rumah pertama terkumpul'],
            ['✈️', 'Umrah bersama orang tua'],
        ] as [$emoji, $text]) {
            VisionItem::create(['user_id' => $uid, 'emoji' => $emoji, 'text' => $text]);
        }

        foreach ([
            ['Sesungguhnya bersama kesulitan ada kemudahan.', 'QS. Al-Insyirah: 6'],
            ['Disiplin adalah jembatan antara tujuan dan pencapaian.', 'Jim Rohn'],
            ['Sebaik-baik manusia adalah yang paling bermanfaat bagi manusia lain.', 'HR. Ahmad'],
        ] as [$text, $src]) {
            QuoteFavorite::create(['user_id' => $uid, 'text' => $text, 'src' => $src]);
        }

        foreach ([
            ['Dashboard Analitik Bisnis', 'https://analytics.google.com', 'Cek trafik mingguan tiap Senin'],
            ['Template Proposal Klien', 'https://drive.google.com', 'Versi terbaru 2026'],
            ['Riset Kompetitor', 'https://notion.so', 'Update tiap akhir bulan'],
            ['Kalender Konten', 'https://trello.com', 'Jadwal posting Instagram & TikTok'],
        ] as [$title, $url, $notes]) {
            ImportantLink::create(['user_id' => $uid, 'title' => $title, 'url' => $url, 'notes' => $notes]);
        }

        // Pengingat sholat
        foreach ([['Subuh', '04:30'], ['Dzuhur', '12:00'], ['Ashar', '15:15'], ['Maghrib', '18:00'], ['Isya', '19:15']] as [$key, $time]) {
            Reminder::create(['user_id' => $uid, 'key' => $key, 'time' => $time]);
        }

        // Target bulanan (Goals)
        foreach ([['sholat', 28], ['gym', 16], ['run', 10], ['intimacy', 10]] as [$field, $value]) {
            Goal::create(['user_id' => $uid, 'month_key' => now()->format('Y-m'), 'field' => $field, 'value' => $value]);
        }

        // Lepas dari sosial media berlebih
        QuitTracker::create([
            'user_id' => $uid, 'type' => 'sosmed',
            'start_date' => now()->subDays(23)->toDateString(), 'best_streak' => 23,
        ]);
        QuitRelapse::create([
            'user_id' => $uid, 'type' => 'sosmed', 'streak' => 14,
            'date' => now()->subDays(24)->toDateString(), 'note' => 'Kebablasan scroll saat menunggu meeting.',
        ]);
    }

    /* ─────────────────────── Produktivitas & waktu ─────────────────────── */

    private function productivity(): void
    {
        $uid = $this->uid;

        // Tugas harian (hari ini) & mingguan
        foreach ([
            ['Follow up proposal PT Maju Jaya', 'high', true],
            ['Kirim invoice klien Camemo', 'high', true],
            ['Review laporan keuangan bulanan', 'medium', true],
            ['Siapkan materi wawancara tahap 2', 'medium', false],
            ['Update konten landing page Molife', 'low', false],
        ] as [$text, $prio, $done]) {
            Todo::create([
                'user_id' => $uid, 'scope' => 'daily', 'period_key' => now()->toDateString(),
                'text' => $text, 'priority' => $prio, 'done' => $done,
            ]);
        }
        foreach ([
            ['Tutup dua deal yang sedang negosiasi', 'high', true],
            ['Rekrut satu freelancer desain', 'medium', false],
            ['Rapikan pembukuan kuartal ini', 'medium', true],
            ['Olahraga minimal lima kali', 'low', true],
        ] as [$text, $prio, $done]) {
            Todo::create([
                'user_id' => $uid, 'scope' => 'weekly', 'period_key' => Dates::weekKey(),
                'text' => $text, 'priority' => $prio, 'done' => $done,
            ]);
        }

        // Riwayat tugas harian beberapa hari ke belakang (biar grafik terisi)
        $past = ['Balas email klien', 'Riset kompetitor', 'Susun proposal baru', 'Cek performa iklan', 'Panggilan dengan partner'];
        foreach (range(1, 20) as $i) {
            $key = now()->subDays($i)->toDateString();
            foreach (range(1, rand(3, 5)) as $n) {
                Todo::create([
                    'user_id' => $uid, 'scope' => 'daily', 'period_key' => $key,
                    'text' => $past[array_rand($past)], 'priority' => ['high', 'medium', 'low'][rand(0, 2)],
                    'done' => rand(0, 100) < 82,
                ]);
            }
        }

        // Pomodoro 45 hari
        $labels = ['Proposal', 'Coding', 'Riset klien', 'Desain', 'Laporan', 'Belajar produk'];
        foreach (range(0, 44) as $i) {
            if (rand(0, 100) < 80) {
                PomodoroSession::create([
                    'user_id' => $uid, 'date' => now()->subDays($i)->toDateString(),
                    'focus_minutes' => [25, 50, 75, 100][rand(0, 3)],
                    'label' => $labels[array_rand($labels)],
                ]);
            }
        }

        // Time blocking: satu minggu penuh terjadwal
        $week = [
            ['Sholat Subuh & Quran', 270, 330, 'green'],
            ['Olahraga pagi', 330, 405, 'rose'],
            ['Deep work: proposal', 480, 630, 'blue'],
            ['Meeting klien', 660, 720, 'violet'],
            ['Istirahat & Dzuhur', 720, 780, 'green'],
            ['Deep work: produk', 810, 960, 'blue'],
            ['Evaluasi & jurnal', 1230, 1290, 'amber'],
        ];
        foreach (range(0, 6) as $d) {
            $date = now()->startOfWeek()->addDays($d)->toDateString();
            foreach ($week as $k => [$title, $s, $e, $color]) {
                // Akhir pekan lebih longgar
                if ($d >= 5 && in_array($k, [2, 3, 5], true)) continue;
                TimeBlock::create([
                    'user_id' => $uid, 'date' => $date,
                    'start_min' => $s, 'end_min' => $e,
                    'title' => $title, 'color' => $color, 'note' => null,
                ]);
            }
        }

        // Ide & Script konten
        foreach ([
            ['idea', 'Konten: satu hari bersama Molife', "Rekam rutinitas dari subuh sampai malam, tunjukkan bagaimana tiap modul dipakai.\n\nAngle: bukan aplikasi produktivitas biasa, tapi yang mengikat ibadah, kesehatan, dan bisnis dalam satu tempat."],
            ['idea', 'Konten: cara saya kelola 3 bisnis sekaligus', "Tunjukkan papan tugas lintas bisnis, pipeline proposal, dan bagaimana semuanya dipantau dari satu dashboard."],
            ['idea', 'Konten: Life Score dijelaskan', "Bedah empat pilar: spiritual, kesehatan, mental, produktivitas. Kenapa angkanya bisa naik turun."],
            ['idea', 'Kolaborasi influencer muslim', "Target: kreator gaya hidup muslim 10-100 ribu pengikut. Tawarkan akses gratis setahun + kode promo untuk audiens mereka."],
            ['script', 'Script Reels: 3 kemenangan setiap hari', "HOOK (0-3 dtk): \"Setiap hari saya cuma kejar tiga kemenangan.\"\n\nISI (3-20 dtk): Kemenangan pertama, sholat tepat waktu. Kedua, badan bergerak. Ketiga, satu tugas yang paling penting selesai.\n\nDEMO (20-35 dtk): Tampilkan dashboard Molife, Life Score naik.\n\nPENUTUP (35-45 dtk): \"Bukan soal sempurna, tapi soal konsisten. Coba Molife.\""],
            ['script', 'Script TikTok: kenapa aplikasi to-do gagal', "HOOK: \"Aplikasi to-do kamu gagal bukan karena kamu malas.\"\n\nMASALAH: Semua aplikasi cuma ngurus tugas kerja, padahal hidup kamu lebih dari itu.\n\nSOLUSI: Molife satukan ibadah, kesehatan, mental, keuangan, dan bisnis.\n\nCTA: \"Link di bio, coba gratis dulu.\""],
            ['script', 'Script email ke influencer', "Halo kak [Nama],\n\nSaya Arif dari Molife, aplikasi tracker hidup untuk muslim produktif. Saya lihat konten kakak soal rutinitas pagi, dan menurut saya audiens kakak cocok banget.\n\nSaya mau tawarkan akses gratis setahun plus kode promo diskon khusus untuk audiens kakak, dan komisi dari setiap yang berlangganan.\n\nBoleh saya kirim detailnya?\n\nTerima kasih,\nArif"],
        ] as [$type, $title, $content]) {
            IdeaScript::create(['user_id' => $uid, 'type' => $type, 'title' => $title, 'content' => $content]);
        }
    }

    /* ───────────────────────────── Karier ───────────────────────────── */

    private function career(): void
    {
        $uid = $this->uid;

        CareerGoal::create([
            'user_id' => $uid, 'target_role' => 'Senior Product Manager',
            'target_company' => 'Perusahaan Teknologi', 'target_salary' => 28000000,
            'target_date' => now()->addMonths(5)->toDateString(),
            'notes' => 'Perkuat portofolio produk, studi kasus terukur, dan jaringan di komunitas produk.',
        ]);

        $apps = [
            ['Tokopedia', 'Senior Product Manager', 'Jakarta', 28000000, 4, 'interview', 'fulltime', 'linkedin'],
            ['Gojek', 'Product Manager', 'Jakarta', 25000000, 9, 'interview', 'fulltime', 'referral'],
            ['Traveloka', 'Product Manager', 'Jakarta', 26000000, 14, 'offer', 'fulltime', 'linkedin'],
            ['Bukalapak', 'Product Owner', 'Remote', 22000000, 18, 'review', 'fulltime', 'jobstreet'],
            ['Xendit', 'Product Manager', 'Jakarta', 30000000, 21, 'applied', 'fulltime', 'website'],
            ['Ruangguru', 'Senior PM', 'Jakarta', 27000000, 26, 'review', 'fulltime', 'glints'],
            ['Kitabisa', 'Product Manager', 'Remote', 20000000, 33, 'rejected', 'fulltime', 'linkedin'],
            ['Flip', 'Product Manager', 'Jakarta', 24000000, 38, 'interview', 'fulltime', 'referral'],
            ['Startup Fintech', 'Head of Product', 'Remote', 32000000, 45, 'wishlist', 'fulltime', 'other'],
            ['Agency Digital', 'Product Consultant', 'Bandung', 18000000, 52, 'hired', 'freelance', 'referral'],
            ['Sayurbox', 'Product Manager', 'Jakarta', 23000000, 58, 'rejected', 'fulltime', 'jobstreet'],
            ['Halodoc', 'Senior PM', 'Jakarta', 29000000, 63, 'applied', 'fulltime', 'linkedin'],
        ];
        $created = [];
        foreach ($apps as [$co, $pos, $loc, $sal, $days, $status, $type, $channel]) {
            $created[] = JobApplication::create([
                'user_id' => $uid, 'company' => $co, 'position' => $pos, 'location' => $loc,
                'salary' => $sal, 'applied_date' => now()->subDays($days)->toDateString(),
                'status' => $status, 'job_type' => $type, 'channel' => $channel,
                'notes' => null,
            ]);
        }

        // Wawancara: dua sudah selesai, dua akan datang
        $ivs = [
            [0, 'video',  'Tahap 1 - HR',        -6, '10:00', true,  'Ratna (Talent Acquisition)'],
            [0, 'video',  'Tahap 2 - Studi Kasus', 2, '13:30', false, 'Dimas (Head of Product)'],
            [1, 'onsite', 'Tahap 1 - User',      -3, '09:00', true,  'Bagas (Engineering Lead)'],
            [2, 'phone',  'Negosiasi Offer',       4, '15:00', false, 'Sinta (HR Manager)'],
        ];
        foreach ($ivs as [$idx, $type, $round, $dayOffset, $time, $done, $person]) {
            $app = $created[$idx];
            Interview::create([
                'user_id' => $uid, 'application_id' => $app->id,
                'company' => $app->company, 'position' => $app->position,
                'date' => now()->addDays($dayOffset)->toDateString(), 'time' => $time,
                'type' => $type, 'round' => $round,
                'location' => $type === 'onsite' ? 'Kantor pusat, Jakarta Selatan' : null,
                'interviewer' => $person, 'completed' => $done,
                'notes' => $done ? 'Berjalan lancar, pertanyaan fokus ke pengalaman memimpin tim.' : 'Siapkan studi kasus dan metrik dampak.',
            ]);
        }

        foreach ([
            ['CV Terbaru 2026', 'https://drive.google.com/cv', 'cv', 'Versi ATS-friendly'],
            ['Portofolio Produk', 'https://notion.so/portfolio', 'portfolio', 'Tiga studi kasus utama'],
            ['LinkedIn', 'https://linkedin.com/in/arifrahman', 'linkedin', 'Sudah dioptimasi kata kunci'],
            ['GitHub', 'https://github.com/arifrahman', 'github', 'Proyek sampingan'],
        ] as [$name, $url, $type, $notes]) {
            PrepLink::create(['user_id' => $uid, 'name' => $name, 'url' => $url, 'type' => $type, 'notes' => $notes]);
        }

        foreach ([
            ['Email Lamaran', 'email', "Yth. Tim Rekrutmen,\n\nSaya tertarik pada posisi [Posisi] di [Perusahaan]. Selama lima tahun terakhir saya memimpin pengembangan produk digital dengan dampak terukur.\n\nCV dan portofolio saya lampirkan. Terima kasih atas waktunya.\n\nHormat saya,\nArif Rahman"],
            ['Pesan LinkedIn ke Rekruter', 'linkedin', "Halo kak [Nama], saya Arif, Product Manager dengan pengalaman lima tahun. Saya melihat lowongan [Posisi] di [Perusahaan] dan merasa cocok. Boleh saya kirimkan CV untuk dipertimbangkan? Terima kasih."],
            ['Follow Up Setelah Wawancara', 'email', "Terima kasih atas kesempatan wawancaranya kemarin. Saya semakin tertarik setelah memahami arah produk yang sedang dibangun. Jika ada informasi tambahan yang dibutuhkan, dengan senang hati saya siapkan."],
        ] as [$title, $cat, $content]) {
            PrepTemplate::create(['user_id' => $uid, 'title' => $title, 'category' => $cat, 'content' => $content]);
        }

        foreach ([
            ['Ceritakan tentang diri Anda.', 'Product Manager lima tahun, fokus produk konsumen. Terakhir memimpin tim tujuh orang dan menaikkan retensi 30 persen dalam enam bulan.', 'general', 5, null, null, null, null],
            ['Ceritakan saat Anda gagal.', null, 'behavioral', 4,
                'Peluncuran fitur pembayaran baru di kuartal tiga.',
                'Saya bertanggung jawab memastikan adopsi mencapai 20 persen.',
                'Saya terlalu cepat meluncurkan tanpa riset pengguna yang cukup, lalu menghentikan dan mengulang riset.',
                'Adopsi versi kedua mencapai 34 persen, dan riset pengguna jadi tahap wajib sejak itu.'],
            ['Bagaimana Anda menentukan prioritas?', 'Saya pakai kombinasi dampak terhadap metrik utara dan biaya pengerjaan, lalu divalidasi dengan data dan masukan tim.', 'situational', 5, null, null, null, null],
            ['Kenapa tertarik dengan perusahaan kami?', 'Arah produknya menyentuh masalah nyata yang saya pahami, dan budaya timnya terbuka pada eksperimen.', 'general', 4, null, null, null, null],
        ] as [$q, $a, $cat, $conf, $s, $t, $ac, $r]) {
            PrepQa::create([
                'user_id' => $uid, 'question' => $q, 'answer' => $a, 'category' => $cat,
                'confidence' => $conf, 'star_situation' => $s, 'star_task' => $t,
                'star_action' => $ac, 'star_result' => $r,
            ]);
        }

        foreach ([
            ['Dimas Prasetyo', 'Tokopedia', 'Head of Product', 'linkedin', 'Kenalan dari konferensi produk.', 40],
            ['Ratna Wulandari', 'Gojek', 'Talent Acquisition', 'referral', 'Diperkenalkan oleh teman kampus.', 25],
            ['Bagas Nugroho', 'Traveloka', 'Engineering Lead', 'event', 'Ngobrol saat meetup teknologi.', 18],
            ['Sinta Maharani', 'Xendit', 'HR Manager', 'email', 'Balasan lamaran cukup responsif.', 12],
        ] as [$name, $co, $role, $ch, $notes, $days]) {
            Contact::create([
                'user_id' => $uid, 'name' => $name, 'company' => $co, 'role' => $role,
                'channel' => $ch, 'notes' => $notes, 'connected_at' => now()->subDays($days)->toDateString(),
            ]);
        }
    }

    /* ───────────────────────────── Bisnis ───────────────────────────── */

    private function business(): void
    {
        $uid = $this->uid;

        $camemo    = BusinessProduct::create(['user_id' => $uid, 'name' => 'Camemo']);
        $molife    = BusinessProduct::create(['user_id' => $uid, 'name' => 'Molife']);
        $hastacode = BusinessProduct::create(['user_id' => $uid, 'name' => 'Hastacode']);

        $deals = [
            ['PT Maju Jaya',              'F&B',        'Camemo',    7500000,  'won',         12, 'whatsapp',   'Deal! Mulai pengerjaan minggu depan.'],
            ['CV Sinar Abadi',            'Retail',     'Camemo',    5000000,  'negotiation',  5, 'email',      'Minta revisi harga, follow up Kamis.'],
            ['Wedding Organizer Bahagia', 'Event',      'Camemo',    3500000,  'sent',         3, 'sosmed',     null],
            ['Florist Melati',            'Retail',     'Camemo',    2800000,  'won',         20, 'rekomendasi', 'Repeat order kedua.'],
            ['Souvenir Kenangan',         'Retail',     'Camemo',    4200000,  'lead',         1, 'sosmed',     'Masuk dari DM Instagram.'],
            ['PT Teknologi Nusantara',    'Teknologi',  'Molife',   12000000,  'negotiation',  7, 'event',      'Kenalan dari konferensi startup.'],
            ['Klinik Sehat Sentosa',      'Kesehatan',  'Molife',    8000000,  'won',         28, 'website',    null],
            ['Komunitas Produktif',       'Edukasi',    'Molife',    6500000,  'sent',         4, 'rekomendasi', 'Menunggu rapat internal mereka.'],
            ['Pesantren Modern Amanah',   'Edukasi',    'Molife',    9500000,  'lead',         2, 'telepon',    'Tertarik untuk 300 santri.'],
            ['Toko Berkah',               'Retail',     'Camemo',    2500000,  'lost',        45, 'whatsapp',   'Anggaran belum ada, coba lagi kuartal depan.'],
            ['PT Karya Digital',          'Teknologi',  'Hastacode', 18000000, 'won',         35, 'rekomendasi', 'Proyek website korporat.'],
            ['Startup EduTech',           'Edukasi',    'Hastacode', 15000000, 'negotiation',  9, 'lainnya',    'Negosiasi ruang lingkup.'],
            ['Restoran Nusantara',        'F&B',        'Hastacode', 11000000, 'sent',         6, 'email',      null],
            ['Butik Anggun',              'Retail',     'Hastacode', 7500000,  'lost',        50, 'sosmed',     'Pilih vendor lain yang lebih murah.'],
            ['Yayasan Peduli',            'Sosial',     'Molife',    5500000,  'won',         55, 'rekomendasi', 'Harga khusus lembaga sosial.'],
        ];
        foreach ($deals as [$client, $industry, $product, $value, $status, $days, $channel, $notes]) {
            BusinessDeal::create([
                'user_id' => $uid, 'client_name' => $client, 'industry' => $industry,
                'product' => $product, 'value' => $value, 'status' => $status,
                'proposal_date' => now()->subDays($days)->toDateString(),
                'contact' => '08' . rand(1111111111, 9999999999),
                'channel' => $channel, 'notes' => $notes,
                'address' => 'Jakarta',
            ]);
        }

        foreach ([
            [$camemo->id, 'template', 'Penawaran Awal WA', 'whatsapp', "Halo kak! Perkenalkan, saya dari Camemo, platform undangan digital dan buku tamu QR untuk acara spesial kakak. Boleh saya kirimkan demo dan daftar harganya?"],
            [$camemo->id, 'template', 'Follow Up Penawaran', 'followup', "Halo kak, menindaklanjuti penawaran Camemo beberapa hari lalu. Ada yang bisa saya bantu jelaskan? Bulan ini kami ada promo khusus."],
            [$molife->id, 'template', 'Penawaran Korporat', 'email', "Yth. Bapak/Ibu,\n\nKami menawarkan Molife untuk program kesejahteraan karyawan: pelacakan ibadah, kesehatan, dan produktivitas dalam satu aplikasi.\n\nHormat kami,\nArif Rahman"],
            [$molife->id, 'template', 'Pesan Kolaborasi Influencer', 'sosmed', "Halo kak, saya Arif dari Molife. Saya lihat konten kakak soal rutinitas produktif. Kami ingin tawarkan kerja sama: akses gratis setahun, kode promo untuk audiens, plus komisi."],
            [$hastacode->id, 'template', 'Penawaran Jasa Website', 'email', "Yth. Bapak/Ibu,\n\nHastacode membantu bisnis membangun website dan sistem internal yang rapi dan cepat. Terlampir portofolio dan skema harga kami."],
        ] as [$pid, $kind, $title, $cat, $content]) {
            BusinessDoc::create([
                'user_id' => $uid, 'business_product_id' => $pid, 'kind' => $kind,
                'title' => $title, 'category' => $cat, 'content' => $content,
            ]);
        }

        // Papan tugas per proyek
        $tasks = [
            [$camemo->id,    'Kirim 50 proposal ke WO',           'Target satu minggu ke depan',      'progress', 'high',   3],
            [$camemo->id,    'Bikin guide book florist dan WO',   'Format PDF',                        'todo',     'normal', 7],
            [$camemo->id,    'Upload konten sosial media',        'TikTok, IG, FB, Threads',           'review',   'normal', 1],
            [$camemo->id,    'Deck proposal dari WO ke klien',    null,                                'todo',     'high',   5],
            [$camemo->id,    'Desain untuk detail deck',          null,                                'review',   'normal', 2],
            [$molife->id,    'Kontak influencer untuk referal',   'Lewat sosial media dan email',      'todo',     'urgent', 4],
            [$molife->id,    'Bikin pitch deck influencer',       'Penawaran kerja sama',              'progress', 'high',   2],
            [$molife->id,    'Template pesan untuk influencer',   'Email, WA, dan DM sosial media',    'review',   'normal', 6],
            [$molife->id,    'Bikin akun YouTube untuk short',    'Supaya bisa komentar di channel influencer', 'todo', 'normal', 9],
            [$molife->id,    'Rilis fitur papan tugas',           null,                                'done',     'high',   -3],
            [$hastacode->id, 'Listing calon klien',               'Cari data klien di peta',           'progress', 'normal', 8],
            [$hastacode->id, 'Coba chat 50 klien',                null,                                'progress', 'high',   4],
            [$hastacode->id, 'Buat template pesan',               'Untuk WA, sosmed, dan email',       'review',   'normal', 5],
            [$hastacode->id, 'Selesaikan website PT Karya Digital', null,                              'done',     'urgent', -5],
        ];
        foreach ($tasks as [$pid, $title, $note, $status, $prio, $dueIn]) {
            CollabTask::create([
                'business_product_id' => $pid, 'title' => $title, 'note' => $note,
                'status' => $status, 'priority' => $prio,
                'due_date' => now()->addDays($dueIn)->toDateString(),
                'assignee_id' => $this->uid, 'created_by' => $this->uid,
            ]);
        }
    }

    /* ───────────────────────────── Finance ───────────────────────────── */

    private function finance(): void
    {
        $uid = $this->uid;

        // Pemasukan & pengeluaran 3 bulan terakhir
        foreach ([0, 1, 2] as $m) {
            $base = now()->subMonths($m);

            FinanceTransaction::create([
                'user_id' => $uid, 'type' => 'income', 'date' => $base->copy()->startOfMonth()->toDateString(),
                'category' => 'Gaji', 'amount' => 18000000, 'note' => 'Gaji bulanan',
            ]);
            FinanceTransaction::create([
                'user_id' => $uid, 'type' => 'income', 'date' => $base->copy()->startOfMonth()->addDays(9)->toDateString(),
                'category' => 'Bisnis', 'amount' => rand(7000, 15000) * 1000, 'note' => 'Pembayaran klien',
            ]);
            if ($m === 0) {
                FinanceTransaction::create([
                    'user_id' => $uid, 'type' => 'income', 'date' => now()->subDays(6)->toDateString(),
                    'category' => 'Bisnis', 'amount' => 7500000, 'note' => 'Pelunasan PT Maju Jaya',
                ]);
            }

            $expenses = [
                ['Makanan', 45000, 'Makan siang'], ['Makanan', 120000, 'Belanja mingguan'],
                ['Makanan', 38000, 'Makan malam keluarga'], ['Makanan', 85000, 'Makan bareng klien'],
                ['Transportasi', 25000, 'Bensin'], ['Transportasi', 90000, 'Perjalanan ke klien'],
                ['Tagihan', 350000, 'Listrik dan internet'], ['Tagihan', 120000, 'Pulsa dan data'],
                ['Hiburan', 50000, 'Nonton'], ['Hiburan', 75000, 'Langganan musik'],
                ['Kesehatan', 150000, 'Vitamin'], ['Belanja', 275000, 'Kemeja kerja'],
                ['Pendidikan', 199000, 'Kursus daring'], ['Sewa', 2500000, 'Sewa tempat tinggal'],
                ['Sedekah', 500000, 'Sedekah bulanan'],
            ];
            foreach ($expenses as $k => [$cat, $amount, $note]) {
                FinanceTransaction::create([
                    'user_id' => $uid, 'type' => 'expense',
                    'date' => $base->copy()->startOfMonth()->addDays(min(27, $k * 2 + 1))->toDateString(),
                    'category' => $cat, 'amount' => $amount, 'note' => $note,
                ]);
            }
        }

        foreach ([
            ['Makanan', 3000000], ['Transportasi', 800000], ['Hiburan', 400000],
            ['Tagihan', 700000], ['Kesehatan', 500000], ['Belanja', 700000],
            ['Pendidikan', 500000], ['Sewa', 2500000], ['Sedekah', 1000000],
        ] as [$cat, $amount]) {
            FinanceBudget::create([
                'user_id' => $uid, 'month_key' => now()->format('Y-m'),
                'category' => $cat, 'amount' => $amount,
            ]);
        }

        foreach ([
            ['Dana Darurat', 60000000, 38500000, 10, 'emerald'],
            ['Umrah Orang Tua', 70000000, 24000000, 14, 'violet'],
            ['DP Rumah', 150000000, 47000000, 24, 'blue'],
            ['Laptop Baru', 25000000, 18500000, 3, 'amber'],
        ] as [$name, $target, $current, $months, $color]) {
            FinanceSavingsGoal::create([
                'user_id' => $uid, 'name' => $name, 'target' => $target, 'current' => $current,
                'deadline' => now()->addMonths($months)->toDateString(), 'color' => $color,
            ]);
        }
    }
}
