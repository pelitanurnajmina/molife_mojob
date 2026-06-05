<?php

namespace App\Http\Controllers;

use App\Services\SholatService;
use App\Services\StatsService;
use App\Services\MoodService;
use App\Services\QuitService;
use App\Support\Features;

class MotivasiController extends Controller
{
    public const QUOTES = [
        ['text' => 'Sesungguhnya sholat itu mencegah dari perbuatan keji dan mungkar.', 'src' => 'QS. Al-Ankabut: 45'],
        ['text' => 'Konsistensi kecil setiap hari mengalahkan usaha besar yang sesekali.', 'src' => 'Mojob'],
        ['text' => 'Tubuh yang sehat adalah tamu yang menyenangkan bagi jiwa.', 'src' => 'Francis Bacon'],
        ['text' => 'Disiplin adalah jembatan antara tujuan dan pencapaian.', 'src' => 'Jim Rohn'],
        ['text' => 'Kamu tidak harus hebat untuk memulai, tapi kamu harus memulai untuk menjadi hebat.', 'src' => 'Zig Ziglar'],
        ['text' => 'Sebaik-baik amal adalah yang konsisten walau sedikit.', 'src' => 'HR. Bukhari & Muslim'],
        ['text' => 'Setiap hari bersih adalah kemenangan. Rayakan langkah kecilmu.', 'src' => 'Mojob'],
        ['text' => 'Jangan bandingkan dirimu hari ini dengan orang lain, tapi dengan dirimu kemarin.', 'src' => 'Mojob'],
        ['text' => 'Energi mengalir ke mana fokus tertuju. Fokuslah pada hal yang membangun.', 'src' => 'Tony Robbins'],
        ['text' => 'Masa depanmu ditentukan oleh apa yang kamu lakukan hari ini, bukan besok.', 'src' => 'Robert Kiyosaki'],
        ['text' => 'Olahraga bukan hukuman untuk tubuhmu, tapi perayaan atas apa yang bisa ia lakukan.', 'src' => 'Mojob'],
        ['text' => 'Barangsiapa bersungguh-sungguh, pasti akan mendapatkan hasilnya.', 'src' => 'Man Jadda Wajada'],
    ];

    public function index()
    {
        $userId   = auth()->id();
        $features = Features::map($userId);

        // Deterministic daily quote
        $quote = self::QUOTES[(int) date('z') % count(self::QUOTES)];

        // Impact cards from real data, framed positively
        $impacts = [];

        if ($features['sholat'] ?? false) {
            $streak = SholatService::streak($userId);
            $impacts[] = [
                'value' => $streak, 'unit' => 'hari',
                'label' => 'Konsisten sholat 5 waktu',
                'color' => 'green',
                'icon'  => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                'msg'   => $streak >= 7 ? 'Kebiasaan spiritual yang kuat!' : 'Setiap hari membangun fondasi.',
            ];
        }
        if ($features['gym'] ?? false) {
            $g = StatsService::gymMonthlyCount($userId);
            $impacts[] = [
                'value' => $g, 'unit' => 'sesi',
                'label' => 'Latihan gym bulan ini', 'color' => 'blue',
                'icon'  => 'M13 10V3L4 14h7v7l9-11h-7z',
                'msg'   => $g >= 8 ? 'Tubuhmu berterima kasih!' : 'Mulai bergerak, mulai berubah.',
            ];
        }
        if ($features['run'] ?? false) {
            $km = StatsService::runMonthlyDistance($userId);
            $impacts[] = [
                'value' => number_format($km, 1), 'unit' => 'km',
                'label' => 'Total lari bulan ini', 'color' => 'emerald',
                'icon'  => 'M22 12h-4l-3 9L9 3l-3 9H2',
                'msg'   => $km >= 10 ? 'Jarak yang luar biasa!' : 'Selangkah demi selangkah.',
            ];
        }
        if ($features['mental'] ?? false) {
            $avg = MoodService::avgScore($userId, 30);
            $impacts[] = [
                'value' => $avg > 0 ? $avg : '—', 'unit' => '/5',
                'label' => 'Rata-rata mood 30 hari', 'color' => 'violet',
                'icon'  => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'msg'   => $avg >= 4 ? 'Mental yang sehat!' : 'Terus jaga dirimu.',
            ];
        }
        if ($features['porn'] ?? false) {
            $s = QuitService::streak($userId, 'porn');
            $impacts[] = [
                'value' => $s, 'unit' => 'hari',
                'label' => 'Bebas pornografi', 'color' => 'rose',
                'icon'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                'msg'   => $s >= 7 ? 'Kontrol diri yang hebat!' : 'Setiap hari adalah kemenangan.',
            ];
        }
        if ($features['sosmed'] ?? false) {
            $s = QuitService::streak($userId, 'sosmed');
            $impacts[] = [
                'value' => $s, 'unit' => 'hari',
                'label' => 'Disiplin sosial media', 'color' => 'sky',
                'icon'  => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
                'msg'   => $s >= 7 ? 'Fokusmu kembali!' : 'Waktu adalah aset paling berharga.',
            ];
        }

        return view('pages.motivasi', compact('quote', 'impacts'));
    }
}
