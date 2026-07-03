<?php

namespace App\Support;

/**
 * Static blog registry. Each entry's article body lives in
 * resources/views/blog/posts/{slug}.blade.php — this holds only metadata
 * used for listing, SEO tags, and the sitemap.
 */
class BlogPosts
{
    /** All posts, newest first. */
    public static function all(): array
    {
        $posts = self::POSTS;
        uasort($posts, fn($a, $b) => strcmp($b['date'], $a['date']));
        return $posts;
    }

    public static function find(string $slug): ?array
    {
        $p = self::POSTS[$slug] ?? null;
        return $p ? ['slug' => $slug] + $p : null;
    }

    /** Up to $n other posts (for "Baca juga"). */
    public static function related(string $slug, int $n = 3): array
    {
        $out = [];
        foreach (self::all() as $s => $p) {
            if ($s === $slug) continue;
            $out[$s] = $p;
            if (count($out) >= $n) break;
        }
        return $out;
    }

    private const POSTS = [
        'cara-konsisten-sholat-5-waktu' => [
            'title'       => 'Cara Konsisten Sholat 5 Waktu Tanpa Bolong (Panduan Praktis)',
            'description' => 'Susah konsisten sholat 5 waktu? Ini 7 cara praktis membangun kebiasaan sholat tepat waktu, lengkap dengan tips melacak streak harianmu.',
            'excerpt'     => 'Tujuh kebiasaan kecil yang membuat sholat 5 waktu jadi otomatis, bukan beban. Plus cara melacaknya tiap hari.',
            'date'        => '2026-06-22',
            'updated'     => '2026-06-22',
            'author'      => 'Tim Molife',
            'category'    => 'Spiritual',
            'read'        => '7 menit',
            'color'       => 'green',
            'emoji'       => '🕌',
            'keywords'    => 'cara konsisten sholat, sholat tepat waktu, tracker sholat, kebiasaan sholat 5 waktu, streak sholat',
        ],
        'cara-mengatur-keuangan-pribadi-pemula' => [
            'title'       => 'Cara Mengatur Keuangan Pribadi untuk Pemula (Anti Boncos)',
            'description' => 'Gaji selalu habis sebelum akhir bulan? Pelajari cara mengatur keuangan pribadi untuk pemula: mencatat, membuat anggaran, dan menabung dengan konsisten.',
            'excerpt'     => 'Langkah sederhana mengatur uang biar tidak habis di tengah bulan: catat, anggarkan, lalu otomatiskan tabunganmu.',
            'date'        => '2026-06-20',
            'updated'     => '2026-06-20',
            'author'      => 'Tim Molife',
            'category'    => 'Keuangan',
            'read'        => '8 menit',
            'color'       => 'blue',
            'emoji'       => '💸',
            'keywords'    => 'cara mengatur keuangan pribadi, atur keuangan pemula, anggaran bulanan, cara menabung, mencatat pengeluaran',
        ],
        'cara-melacak-lamaran-kerja' => [
            'title'       => 'Cara Melacak Lamaran Kerja agar Lebih Terorganisir',
            'description' => 'Bingung melamar ke mana saja dan sudah sampai tahap apa? Ini cara melacak lamaran kerja secara rapi agar peluang dapat kerja makin besar.',
            'excerpt'     => 'Berhenti melamar secara acak. Susun pipeline lamaranmu dari wishlist sampai offer agar tidak ada peluang yang lewat.',
            'date'        => '2026-06-18',
            'updated'     => '2026-06-18',
            'author'      => 'Tim Molife',
            'category'    => 'Karier',
            'read'        => '6 menit',
            'color'       => 'violet',
            'emoji'       => '💼',
            'keywords'    => 'cara melacak lamaran kerja, tracker lamaran kerja, tips mencari kerja, manajemen lamaran kerja',
        ],
        'teknik-pomodoro-fokus-tanpa-burnout' => [
            'title'       => 'Teknik Pomodoro: Cara Fokus Kerja Tanpa Burnout',
            'description' => 'Sulit fokus dan gampang terdistraksi? Pelajari teknik Pomodoro untuk bekerja lebih fokus, produktif, dan tetap waras tanpa kelelahan.',
            'excerpt'     => 'Metode 25 menit kerja, 5 menit istirahat yang terbukti melawan distraksi. Begini cara memakainya tanpa burnout.',
            'date'        => '2026-06-15',
            'updated'     => '2026-06-15',
            'author'      => 'Tim Molife',
            'category'    => 'Produktivitas',
            'read'        => '6 menit',
            'color'       => 'rose',
            'emoji'       => '🍅',
            'keywords'    => 'teknik pomodoro, cara fokus kerja, tips produktif, menghindari burnout, timer fokus',
        ],
        'cara-membangun-kebiasaan-baik' => [
            'title'       => 'Cara Membangun Kebiasaan Baik yang Bertahan Lama',
            'description' => 'Kenapa resolusi selalu gagal? Pelajari cara membangun kebiasaan baik yang bertahan lewat sistem kecil, konsistensi, dan pelacakan harian.',
            'excerpt'     => 'Resolusi gagal karena mengandalkan motivasi. Bangun sistem kecil yang berulang, dan biarkan datanya menjaga konsistensimu.',
            'date'        => '2026-06-12',
            'updated'     => '2026-06-12',
            'author'      => 'Tim Molife',
            'category'    => 'Kebiasaan',
            'read'        => '7 menit',
            'color'       => 'orange',
            'emoji'       => '🌱',
            'keywords'    => 'cara membangun kebiasaan baik, habit tracker, konsistensi, membentuk kebiasaan, atomic habits indonesia',
        ],
    ];
}
