<?php

namespace App\Services;

use App\Models\BusinessDeal;

class BusinessService
{
    /** Awal komunikasi dengan klien: key => label. */
    public const CHANNELS = [
        'email'       => 'Email',
        'whatsapp'    => 'WhatsApp',
        'sosmed'      => 'Media Sosial',
        'rekomendasi' => 'Rekomendasi / Partner',
        'telepon'     => 'Telepon',
        'website'     => 'Website',
        'event'       => 'Event / Offline',
        'lainnya'     => 'Lainnya',
    ];

    /** key => [label, tw (tailwind color), hex (for charts)] */
    public static function statuses(): array
    {
        return [
            'lead'        => ['label' => 'Prospek',   'tw' => 'violet', 'hex' => '#a78bfa'],
            'sent'        => ['label' => 'Terkirim',  'tw' => 'gray',   'hex' => '#6b7280'],
            'negotiation' => ['label' => 'Negosiasi', 'tw' => 'amber',  'hex' => '#f59e0b'],
            'won'         => ['label' => 'Deal',      'tw' => 'green',  'hex' => '#10b981'],
            'lost'        => ['label' => 'Batal',     'tw' => 'red',    'hex' => '#ef4444'],
        ];
    }

    public static function counts(int $userId, ?string $product = null): array
    {
        $rows = BusinessDeal::where('user_id', $userId)
            ->when($product !== null, fn($q) => $q->where('product', $product))
            ->selectRaw('status, count(*) as c')->groupBy('status')->pluck('c', 'status')->toArray();
        $out = [];
        foreach (array_keys(self::statuses()) as $s) $out[$s] = (int) ($rows[$s] ?? 0);
        return $out;
    }

    /** Analitik; bila $product diisi, semua angka di-scope ke produk itu saja (dipakai kolaborasi). */
    public static function analytics(int $userId, ?string $product = null): array
    {
        $deals  = BusinessDeal::where('user_id', $userId)
            ->when($product !== null, fn($q) => $q->where('product', $product))
            ->get();
        $counts = self::counts($userId, $product);
        $total  = $deals->count();

        $active        = $counts['lead'] + $counts['sent'] + $counts['negotiation'];
        $closed        = $counts['won'] + $counts['lost'];
        $winRate       = $closed > 0 ? round($counts['won'] / $closed * 100) : 0;
        $responseRate  = $total > 0 ? round(($total - $counts['lead']) / $total * 100) : 0;

        $pipelineValue = (int) $deals->whereIn('status', ['lead', 'sent', 'negotiation'])->sum('value');
        $wonValue      = (int) $deals->where('status', 'won')->sum('value');

        $thisMonth      = date('Y-m');
        $thisMonthCount = $deals->filter(fn($d) => $d->proposal_date && $d->proposal_date->format('Y-m') === $thisMonth)->count();

        // Weekly trend (12 weeks) by proposal_date
        $trend = [];
        for ($w = 11; $w >= 0; $w--) {
            $start = date('Y-m-d', strtotime("monday this week -$w weeks"));
            $end   = date('Y-m-d', strtotime("$start +6 days"));
            $c = $deals->filter(function ($d) use ($start, $end) {
                $pd = optional($d->proposal_date)->format('Y-m-d');
                return $pd && $pd >= $start && $pd <= $end;
            })->count();
            $trend[] = ['week' => date('j M', strtotime($start)), 'count' => $c];
        }

        // Top industries (bidang)
        $industries = $deals->filter(fn($d) => trim((string) $d->industry) !== '')
            ->groupBy(fn($d) => trim($d->industry))
            ->map->count()->sortDesc()->take(5)
            ->map(fn($c, $name) => ['name' => $name, 'count' => $c])->values()->toArray();

        return compact(
            'total', 'counts', 'active', 'closed', 'winRate', 'responseRate',
            'pipelineValue', 'wonValue', 'thisMonthCount', 'trend', 'industries'
        );
    }
}
