<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Aggregates job listings from free, public job APIs (no API key, no cost).
 * Results are cached so reloads within the cache window cost nothing.
 *
 * Sources:
 *  - Remotive   (https://remotive.com/api/remote-jobs)   — remote jobs, worldwide
 *  - Arbeitnow  (https://www.arbeitnow.com/api/job-board-api) — mostly EU + remote
 *
 * Molife only shows the listing + links out to the original posting; applying
 * happens on the source site, so no scraping and no paid aggregator needed.
 */
class JobFeedService
{
    private const CACHE_TTL = 6 * 3600; // 6 hours — reloads within this window are free
    private const MAX_JOBS  = 40;

    /** Search jobs by keyword across all free sources. Returns normalized arrays. */
    public static function search(string $keyword): array
    {
        $keyword = trim($keyword) !== '' ? trim($keyword) : 'developer';
        $key     = 'jobfeed:' . md5(mb_strtolower($keyword));

        return Cache::remember($key, self::CACHE_TTL, function () use ($keyword) {
            $jobs = array_merge(
                self::fromRemotive($keyword),
                self::fromArbeitnow($keyword),
            );

            // Newest first, cap the list.
            usort($jobs, fn($a, $b) => strcmp($b['posted_at'] ?? '', $a['posted_at'] ?? ''));
            return array_slice($jobs, 0, self::MAX_JOBS);
        });
    }

    /* ── Remotive ── */
    private static function fromRemotive(string $keyword): array
    {
        try {
            $res = Http::timeout(8)->get('https://remotive.com/api/remote-jobs', [
                'search' => $keyword,
                'limit'  => 25,
            ]);
            if (!$res->ok()) return [];

            return collect($res->json('jobs') ?? [])->map(fn($j) => self::normalize(
                source:   'Remotive',
                title:    $j['title'] ?? '',
                company:  $j['company_name'] ?? '',
                location: $j['candidate_required_location'] ?? 'Remote',
                salary:   $j['salary'] ?? '',
                tags:     array_slice((array) ($j['tags'] ?? []), 0, 4),
                url:      $j['url'] ?? '',
                postedAt: $j['publication_date'] ?? '',
                desc:     $j['description'] ?? '',
            ))->filter(fn($j) => $j['title'] && $j['url'])->values()->all();
        } catch (\Throwable $e) {
            Log::warning('JobFeed Remotive failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /* ── Arbeitnow ── */
    private static function fromArbeitnow(string $keyword): array
    {
        try {
            $res = Http::timeout(8)->get('https://www.arbeitnow.com/api/job-board-api');
            if (!$res->ok()) return [];

            $needle = mb_strtolower($keyword);

            return collect($res->json('data') ?? [])
                ->filter(function ($j) use ($needle) {
                    $hay = mb_strtolower(($j['title'] ?? '') . ' ' . implode(' ', (array) ($j['tags'] ?? [])) . ' ' . implode(' ', (array) ($j['job_types'] ?? [])));
                    // Match any word of the keyword so multi-word roles still hit.
                    foreach (preg_split('/\s+/', $needle) as $word) {
                        if ($word !== '' && str_contains($hay, $word)) return true;
                    }
                    return false;
                })
                ->take(20)
                ->map(fn($j) => self::normalize(
                    source:   'Arbeitnow',
                    title:    $j['title'] ?? '',
                    company:  $j['company_name'] ?? '',
                    location: ($j['remote'] ?? false) ? trim(($j['location'] ?? '') . ' · Remote', ' ·') : ($j['location'] ?? ''),
                    salary:   '',
                    tags:     array_slice((array) ($j['tags'] ?? []), 0, 4),
                    url:      $j['url'] ?? '',
                    postedAt: isset($j['created_at']) ? date('c', (int) $j['created_at']) : '',
                    desc:     $j['description'] ?? '',
                ))
                ->filter(fn($j) => $j['title'] && $j['url'])->values()->all();
        } catch (\Throwable $e) {
            Log::warning('JobFeed Arbeitnow failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    private static function normalize(
        string $source, string $title, string $company, string $location,
        string $salary, array $tags, string $url, string $postedAt, string $desc
    ): array {
        return [
            'source'    => $source,
            'title'     => trim($title),
            'company'   => trim($company),
            'location'  => trim($location) ?: 'Remote',
            'salary'    => trim($salary),
            'tags'      => array_map(fn($t) => Str::limit(trim((string) $t), 24, ''), $tags),
            'url'       => $url,
            'posted_at' => $postedAt,
            'excerpt'   => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($desc))), 180),
        ];
    }
}
