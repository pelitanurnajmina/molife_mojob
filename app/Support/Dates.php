<?php

namespace App\Support;

use DateTime;

/**
 * Pure date / range helpers (no storage). Used across activity pages.
 */
class Dates
{
    public static function weekDates(?string $date = null): array
    {
        $now = $date ? new DateTime($date) : new DateTime();
        $dow = (int) $now->format('N');
        $mon = clone $now;
        $mon->modify('-' . ($dow - 1) . ' days');
        $out = [];
        for ($i = 0; $i < 7; $i++) {
            $d = clone $mon;
            $d->modify("+$i days");
            $out[] = $d->format('Y-m-d');
        }
        return $out;
    }

    public static function weekKey(?string $date = null): string
    {
        return self::weekDates($date)[0];
    }

    public static function monthDates(): array
    {
        $now  = new DateTime();
        $y    = (int) $now->format('Y');
        $m    = (int) $now->format('m');
        $days = cal_days_in_month(CAL_GREGORIAN, $m, $y);
        $out  = [];
        for ($i = 1; $i <= $days; $i++) {
            $out[] = sprintf('%04d-%02d-%02d', $y, $m, $i);
        }
        return $out;
    }

    public static function rangeDates(int $months): array
    {
        $end    = new DateTime('today');
        $cursor = (new DateTime('today'))->modify("-{$months} months")->modify('+1 day');
        $out    = [];
        while ($cursor <= $end) {
            $out[] = $cursor->format('Y-m-d');
            $cursor->modify('+1 day');
        }
        return $out;
    }

    public static function rangeToMonths(string $range): ?int
    {
        return match ($range) {
            '3m'  => 3,
            '6m'  => 6,
            '12m' => 12,
            default => null,
        };
    }

    /**
     * Per-month rows of daily cells for the multi-month strip view.
     * $cellFn(string $date): ['active'=>bool,'value'=>int,'title'=>string]
     */
    public static function buildStripRows(int $months, callable $cellFn): array
    {
        $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $todayDt = new DateTime('today');
        $anchor  = (new DateTime('today'))->modify("-{$months} months")->modify('+1 day');

        $crossYear = $anchor->format('Y') !== $todayDt->format('Y');
        $fmt = fn(DateTime $d) => $d->format('j') . ' ' . $monthShort[(int) $d->format('n')]
                                . ($crossYear ? " '" . $d->format('y') : '');

        $rows = [];
        $activeDays = 0;
        $total = 0;

        for ($i = 0; $i < $months; $i++) {
            $pStart = (clone $anchor)->modify("+{$i} months");
            $pEnd   = (clone $anchor)->modify('+' . ($i + 1) . ' months')->modify('-1 day');
            if ($pEnd > $todayDt) $pEnd = clone $todayDt;

            $cells  = [];
            $cursor = clone $pStart;
            while ($cursor <= $pEnd) {
                $cell = $cellFn($cursor->format('Y-m-d'));
                if (!empty($cell['active'])) $activeDays++;
                $total += (int) ($cell['value'] ?? 0);
                $cells[] = ['active' => (bool) ($cell['active'] ?? false), 'title' => $cell['title'] ?? ''];
                $cursor->modify('+1 day');
            }

            $rows[] = ['label' => $fmt($pStart) . ' – ' . $fmt($pEnd), 'cells' => $cells];
        }

        $startYear = $anchor->format('Y');
        $endYear   = $todayDt->format('Y');
        $title = $anchor->format('j') . ' ' . $monthShort[(int) $anchor->format('n')]
               . ($startYear !== $endYear ? ' ' . $startYear : '')
               . ' – ' . $todayDt->format('j') . ' ' . $monthShort[(int) $todayDt->format('n')]
               . ' ' . $endYear;

        return ['rows' => $rows, 'activeDays' => $activeDays, 'total' => $total, 'title' => $title];
    }
}
