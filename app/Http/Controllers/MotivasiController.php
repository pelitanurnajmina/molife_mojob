<?php

namespace App\Http\Controllers;

use App\Models\QuoteFavorite;
use App\Models\VisionItem;
use App\Support\Profile;
use Illuminate\Http\Request;

class MotivasiController extends Controller
{
    /** [text, src (tokoh), cat] — cat: spiritual | productivity | health | mindset */
    public const QUOTES = [
        ['text' => 'Sesungguhnya sholat itu mencegah dari perbuatan keji dan mungkar.', 'src' => 'QS. Al-Ankabut: 45', 'cat' => 'spiritual'],
        ['text' => 'Sebaik-baik amal adalah yang konsisten walau sedikit.', 'src' => 'HR. Bukhari & Muslim', 'cat' => 'spiritual'],
        ['text' => 'Barangsiapa bersungguh-sungguh, pasti akan mendapatkan hasilnya.', 'src' => 'Pepatah Arab', 'cat' => 'spiritual'],
        ['text' => 'Bersyukur mengubah apa yang kita miliki menjadi cukup.', 'src' => 'Aesop', 'cat' => 'spiritual'],
        ['text' => 'Kamu tidak harus hebat untuk memulai, tapi kamu harus memulai untuk menjadi hebat.', 'src' => 'Zig Ziglar', 'cat' => 'mindset'],
        ['text' => 'Jangan bandingkan dirimu hari ini dengan orang lain, tapi dengan dirimu kemarin.', 'src' => 'Jordan Peterson', 'cat' => 'mindset'],
        ['text' => 'Energi mengalir ke mana fokus tertuju. Fokuslah pada hal yang membangun.', 'src' => 'Tony Robbins', 'cat' => 'mindset'],
        ['text' => 'Hidup itu 10% apa yang terjadi padamu dan 90% bagaimana kamu meresponnya.', 'src' => 'Charles Swindoll', 'cat' => 'mindset'],
        ['text' => 'Apa yang kamu pikirkan, kamu menjadi. Apa yang kamu rasakan, kamu tarik.', 'src' => 'Buddha', 'cat' => 'mindset'],
        ['text' => 'Disiplin adalah jembatan antara tujuan dan pencapaian.', 'src' => 'Jim Rohn', 'cat' => 'productivity'],
        ['text' => 'Masa depanmu ditentukan oleh apa yang kamu lakukan hari ini, bukan besok.', 'src' => 'Robert Kiyosaki', 'cat' => 'productivity'],
        ['text' => 'Konsistensi kecil setiap hari mengalahkan usaha besar yang sesekali.', 'src' => 'James Clear', 'cat' => 'productivity'],
        ['text' => 'Cara untuk memulai adalah berhenti berbicara dan mulai melakukan.', 'src' => 'Walt Disney', 'cat' => 'productivity'],
        ['text' => 'Kamu tidak naik ke level tujuanmu, kamu turun ke level sistemmu.', 'src' => 'James Clear', 'cat' => 'productivity'],
        ['text' => 'Tubuh yang sehat adalah tamu yang menyenangkan bagi jiwa.', 'src' => 'Francis Bacon', 'cat' => 'health'],
        ['text' => 'Olahraga bukan hukuman untuk tubuhmu, tapi perayaan atas apa yang ia bisa lakukan.', 'src' => 'Anonim', 'cat' => 'health'],
        ['text' => 'Jaga tubuhmu. Ia satu-satunya tempat yang harus kamu tinggali.', 'src' => 'Jim Rohn', 'cat' => 'health'],
        ['text' => 'Kesehatan adalah kekayaan yang sesungguhnya, bukan emas dan perak.', 'src' => 'Mahatma Gandhi', 'cat' => 'health'],
    ];

    public const CATEGORIES = [
        'all'          => 'Semua',
        'spiritual'    => 'Spiritual',
        'productivity' => 'Produktivitas',
        'health'       => 'Kesehatan',
        'mindset'      => 'Mindset',
    ];

    public const AFFIRMATIONS = [
        'Aku layak atas semua hal baik dalam hidupku.',
        'Aku menarik energi positif, peluang, dan rezeki.',
        'Aku cukup, persis sebagaimana diriku saat ini.',
        'Setiap hari aku tumbuh jadi versi terbaik dari diriku.',
        'Aku memilih tenang, syukur, dan fokus hari ini.',
        'Aku mampu menghadapi apa pun yang datang hari ini.',
        'Pikiran tenang dan hatiku damai.',
        'Aku pantas bahagia, sehat, dan dicintai.',
        'Usaha kecilku hari ini menanam hasil besar di masa depan.',
        'Aku percaya pada prosesku sendiri.',
    ];

    public const CHALLENGES = [
        'Minum satu gelas air putih sekarang juga. 💧',
        'Tarik napas dalam 5 kali, hembuskan perlahan. 🌬️',
        'Kirim pesan terima kasih ke satu orang hari ini.',
        'Rapikan satu sudut meja atau kamarmu.',
        'Jalan kaki 5 menit tanpa menyentuh HP.',
        'Tulis satu hal yang kamu syukuri hari ini.',
        'Matikan notifikasi selama 1 jam dan fokus.',
        'Lakukan peregangan tubuh selama 2 menit.',
        'Ucapkan afirmasi hari ini di depan cermin.',
        'Tidur 30 menit lebih awal malam ini.',
    ];

    public function index()
    {
        $userId = auth()->id();
        $z      = (int) date('z');

        $quote        = self::QUOTES[$z % count(self::QUOTES)];
        $affirmation  = self::AFFIRMATIONS[$z % count(self::AFFIRMATIONS)];
        $challenge    = self::CHALLENGES[$z % count(self::CHALLENGES)];
        $quotes       = self::QUOTES;
        $affirmations = self::AFFIRMATIONS;
        $categories   = self::CATEGORIES;

        $favorites  = QuoteFavorite::where('user_id', $userId)->latest()->get(['id', 'text', 'src']);
        $favTexts   = $favorites->pluck('text')->all();
        $vision     = VisionItem::where('user_id', $userId)->latest()->get(['id', 'emoji', 'text']);
        $bigWhy     = Profile::model($userId)->big_why ?? '';

        return view('pages.motivasi', compact(
            'quote', 'quotes', 'categories', 'affirmation', 'affirmations', 'challenge',
            'favorites', 'favTexts', 'vision', 'bigWhy'
        ));
    }

    /** Toggle a quote favorite (add if new, remove if already saved). */
    public function toggleFavorite(Request $request)
    {
        $r = $request->validate(['text' => 'required|string|max:500', 'src' => 'nullable|string|max:120']);
        $userId = auth()->id();

        $existing = QuoteFavorite::where('user_id', $userId)->where('text', $r['text'])->first();
        if ($existing) {
            $existing->delete();
            return response()->json(['favorited' => false]);
        }
        $fav = QuoteFavorite::create(['user_id' => $userId, 'text' => $r['text'], 'src' => $r['src'] ?? null]);
        return response()->json(['favorited' => true, 'id' => $fav->id]);
    }

    public function deleteFavorite(string $id)
    {
        QuoteFavorite::where('user_id', auth()->id())->where('id', $id)->delete();
        return redirect()->route('motivasi')->with('toast', __('Quote dihapus dari favorit.'));
    }

    /** Save the user's "Big Why". */
    public function saveWhy(Request $request)
    {
        $request->validate(['big_why' => 'nullable|string|max:1000']);
        $p = Profile::model(auth()->id());
        $p->big_why = trim($request->big_why ?? '');
        $p->save();
        return redirect()->route('motivasi')->with('toast', __('Alasan besarmu tersimpan.'));
    }

    /* ── Vision board ── */
    public function addVision(Request $request)
    {
        $r = $request->validate(['text' => 'required|string|max:80', 'emoji' => 'nullable|string|max:16']);
        if (VisionItem::where('user_id', auth()->id())->count() >= 12) {
            return redirect()->route('motivasi')->with('toast', __('Maksimal 12 impian di vision board.'));
        }
        VisionItem::create(['user_id' => auth()->id(), 'text' => trim($r['text']), 'emoji' => $r['emoji'] ?: '✨']);
        return redirect()->route('motivasi')->with('toast', __('Impian ditambahkan ke vision board!'));
    }

    public function deleteVision(string $id)
    {
        VisionItem::where('user_id', auth()->id())->where('id', $id)->delete();
        return redirect()->route('motivasi');
    }
}
