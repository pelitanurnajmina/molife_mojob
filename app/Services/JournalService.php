<?php

namespace App\Services;

use App\Models\JournalEntry;

class JournalService
{
    public const DEFAULT_TEMPLATE = 'loa';

    /** Template metadata + guided prompts. Beginner-friendly LoA structure. */
    public static function templates(): array
    {
        return [
            'loa' => [
                'label' => 'Law of Attraction',
                'desc'  => 'Tarik hal baik dengan rasa syukur, afirmasi, dan visualisasi impianmu.',
                'color' => 'violet',
                'fields' => [
                    [
                        'key' => 'gratitude',
                        'label' => 'Rasa Syukur',
                        'help'  => 'Tulis 3 hal yang kamu syukuri hari ini, sekecil apa pun. Rasa syukur menarik lebih banyak hal baik.',
                        'placeholder' => "1. Aku bersyukur atas...\n2. Aku bersyukur atas...\n3. Aku bersyukur atas...",
                        'rows' => 4,
                        'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                    ],
                    [
                        'key' => 'affirmation',
                        'label' => 'Afirmasi Positif',
                        'help'  => 'Kalimat positif tentang dirimu, pakai kata "Saya/Aku". Contoh: "Aku layak bahagia", "Aku menarik rezeki".',
                        'placeholder' => "Aku adalah...\nAku mampu...\nAku layak...",
                        'rows' => 3,
                        'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
                    ],
                    [
                        'key' => 'manifest',
                        'label' => 'Manifestasi (seolah sudah terjadi)',
                        'help'  => 'Tulis impianmu seolah sudah tercapai, pakai waktu sekarang. Contoh: "Aku sangat bersyukur kini memiliki...".',
                        'placeholder' => "Aku sangat bersyukur kini...\nRasanya luar biasa karena...",
                        'rows' => 4,
                        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                    ],
                    [
                        'key' => 'visualize',
                        'label' => 'Visualisasi',
                        'help'  => 'Bayangkan hidup impianmu. Apa yang kamu lihat, rasakan, dengar? Gambarkan sedetail mungkin.',
                        'placeholder' => 'Aku membayangkan diriku...',
                        'rows' => 4,
                        'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                    ],
                    [
                        'key' => 'action',
                        'label' => 'Satu Langkah Hari Ini',
                        'help'  => 'Satu aksi kecil yang bisa kamu lakukan hari ini untuk mendekat ke impianmu.',
                        'placeholder' => 'Hari ini aku akan...',
                        'rows' => 2,
                        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                ],
            ],
        ];
    }

    public static function template(string $key = self::DEFAULT_TEMPLATE): array
    {
        return self::templates()[$key] ?? self::templates()[self::DEFAULT_TEMPLATE];
    }

    public static function entry(int $userId, string $date, string $template = self::DEFAULT_TEMPLATE): array
    {
        $row = JournalEntry::where('user_id', $userId)->whereDate('date', $date)
            ->where('template', $template)->first();
        return $row?->content ?? [];
    }

    /** Save (one entry per template per day). */
    public static function save(int $userId, string $date, string $template, array $content): void
    {
        // strip empty values so a blank submit doesn't count as an entry
        $content = array_filter($content, fn($v) => trim((string) $v) !== '');
        if (empty($content)) {
            JournalEntry::where('user_id', $userId)->whereDate('date', $date)->where('template', $template)->delete();
            return;
        }
        JournalEntry::updateOrCreate(
            ['user_id' => $userId, 'date' => $date, 'template' => $template],
            ['content' => $content]
        );
    }

    /** Consecutive-day writing streak (any template). */
    public static function streak(int $userId): int
    {
        $dates = JournalEntry::where('user_id', $userId)
            ->orderByDesc('date')->pluck('date')
            ->map(fn($d) => $d->format('Y-m-d'))->unique()->values()->all();
        if (empty($dates)) return 0;

        $set = array_flip($dates);
        $streak = 0;
        $cursor = new \DateTime('today');
        // allow streak to count even if today not yet written (start from today; if missing, try yesterday once)
        if (!isset($set[$cursor->format('Y-m-d')])) $cursor->modify('-1 day');
        while (isset($set[$cursor->format('Y-m-d')])) {
            $streak++;
            $cursor->modify('-1 day');
        }
        return $streak;
    }

    /** Recent entries as display rows, newest first. */
    public static function history(int $userId, int $limit = 30): array
    {
        return JournalEntry::where('user_id', $userId)
            ->orderByDesc('date')->orderByDesc('id')->limit($limit)->get()
            ->map(fn($r) => [
                'id'       => $r->id,
                'date'     => $r->date->format('Y-m-d'),
                'template' => $r->template,
                'content'  => $r->content ?? [],
            ])->toArray();
    }
}
