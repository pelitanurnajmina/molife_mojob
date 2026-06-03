<?php

namespace App\Models;

/**
 * UserStorage — manages all Molife data in the Laravel session.
 * Replaces localStorage from the original React/SPA version.
 * Data structure mirrors the original JSON shape exactly.
 */
class UserStorage
{
    private array $data;
    private static ?self $current = null;

    public function __construct(array $data = [])
    {
        $this->data = array_merge([
            'profile'       => [],
            'finance'       => ['transactions' => [], 'budgets' => [], 'savings_goals' => []],
            'sholat'        => [],
            'spiritual'     => [],
            'gym'           => [],
            'run'           => [],
            'cycling'       => [],
            'swimming'      => [],
            'racket'        => [],
            'custom_sport'  => [],
            'intimacy'      => [],
            'todos'         => ['daily' => [], 'weekly' => []],
            'notes'         => [],
            'reflections'   => [],
            'goals'         => [],
            'reminders'     => [],
            'applications'  => [],
            'interviews'    => [],
            'links'         => [],
            'files'         => [],
            'templates'     => [],
            'features'      => [],
            'moods'         => [],
            'career_goals'  => [],
            'practice_qa'   => [],
            'contacts'      => [],
        ], $data);

        if (!isset($this->data['todos']['daily']))  $this->data['todos']['daily']  = [];
        if (!isset($this->data['todos']['weekly'])) $this->data['todos']['weekly'] = [];
        if (!isset($this->data['run']))             $this->data['run']             = [];
        if (!isset($this->data['applications']))    $this->data['applications']    = [];
        if (!isset($this->data['interviews']))      $this->data['interviews']      = [];
        if (!isset($this->data['links']))           $this->data['links']           = [];
        if (!isset($this->data['files']))           $this->data['files']           = [];
        if (!isset($this->data['templates']))       $this->data['templates']       = [];
        if (!isset($this->data['moods']))           $this->data['moods']           = [];
        if (!isset($this->data['career_goals']))    $this->data['career_goals']    = [];
        if (!isset($this->data['practice_qa']))     $this->data['practice_qa']     = [];
        if (!isset($this->data['contacts']))        $this->data['contacts']        = [];
    }

    public static function fromSession(): self
    {
        if (self::$current !== null) return self::$current;

        $userId = auth()->id();
        if (!$userId) return new self([]);

        $record = UserData::where('user_id', $userId)->first();
        $data   = $record ? json_decode($record->data, true) : [];

        self::$current = new self(is_array($data) ? $data : []);
        return self::$current;
    }

    public function save(): void
    {
        $userId = auth()->id();
        if (!$userId) return;

        UserData::updateOrCreate(
            ['user_id' => $userId],
            ['data'    => json_encode($this->data)]
        );

        self::$current = $this;
    }

    public static function resetInstance(): void
    {
        self::$current = null;
    }

    public function toArray(): array { return $this->data; }

    /* ---- Sholat ---- */

    public function getSholat(string $date): array
    {
        return $this->data['sholat'][$date] ?? ['wajib' => [], 'sunnah' => []];
    }

    public function toggleSholatWajib(string $date, string $name): void
    {
        if (isset($this->data['sholat'][$date]['wajib'][$name])) {
            unset($this->data['sholat'][$date]['wajib'][$name]);
        } else {
            $this->data['sholat'][$date]['wajib'][$name] = [
                'done' => true, 'takbirPertama' => false, 'rawatib' => false,
            ];
        }
    }

    public function toggleTakbirPertama(string $date, string $name): void
    {
        if (!isset($this->data['sholat'][$date]['wajib'][$name])) return;
        $cur = $this->data['sholat'][$date]['wajib'][$name]['takbirPertama'] ?? false;
        $this->data['sholat'][$date]['wajib'][$name]['takbirPertama'] = !$cur;
    }

    public function toggleRawatib(string $date, string $name): void
    {
        if (!isset($this->data['sholat'][$date]['wajib'][$name])) return;
        $cur = $this->data['sholat'][$date]['wajib'][$name]['rawatib'] ?? false;
        $this->data['sholat'][$date]['wajib'][$name]['rawatib'] = !$cur;
    }

    public function toggleSholatSunnah(string $date, string $name): void
    {
        $sunnah = $this->data['sholat'][$date]['sunnah'] ?? [];
        $idx    = array_search($name, $sunnah);
        if ($idx !== false) {
            array_splice($sunnah, $idx, 1);
        } else {
            $sunnah[] = $name;
        }
        $this->data['sholat'][$date]['sunnah'] = array_values($sunnah);
    }

    public function getSholatStats(string $date): array
    {
        $day     = $this->getSholat($date);
        $wajib   = count(array_filter($day['wajib'] ?? [], fn($s) => $s['done'] ?? false));
        $takbir  = count(array_filter($day['wajib'] ?? [], fn($s) => $s['takbirPertama'] ?? false));
        $rawatib = count(array_filter($day['wajib'] ?? [], fn($s) => $s['rawatib'] ?? false));
        $sunnah  = count($day['sunnah'] ?? []);
        return compact('wajib', 'takbir', 'rawatib', 'sunnah') + ['total' => $wajib + $takbir + $rawatib + $sunnah];
    }

    public function getSholatStreak(): int
    {
        $streak = 0;
        $today  = (new \DateTime('today'))->format('Y-m-d');
        $check  = new \DateTime('today');
        while (true) {
            $ds = $check->format('Y-m-d');
            if ($this->getSholatStats($ds)['wajib'] >= 5) {
                $streak++;
            } else {
                if ($ds === $today && $streak === 0) { $check->modify('-1 day'); continue; }
                break;
            }
            $check->modify('-1 day');
        }
        return $streak;
    }

    public function getTakbirStreak(): int
    {
        $streak = 0;
        $today  = (new \DateTime('today'))->format('Y-m-d');
        $check  = new \DateTime('today');
        while (true) {
            $ds = $check->format('Y-m-d');
            if ($this->getSholatStats($ds)['takbir'] >= 5) {
                $streak++;
            } else {
                if ($ds === $today && $streak === 0) { $check->modify('-1 day'); continue; }
                break;
            }
            $check->modify('-1 day');
        }
        return $streak;
    }

    /* ---- Gym ---- */

    public function getGym(string $date): array
    {
        return $this->data['gym'][$date] ?? ['done' => false, 'calories' => 0];
    }

    public function toggleGym(string $date, int $calories = 0): void
    {
        $cur = $this->data['gym'][$date] ?? ['done' => false, 'calories' => 0];
        $this->data['gym'][$date] = $cur['done']
            ? ['done' => false, 'calories' => 0]
            : ['done' => true,  'calories' => $calories];
    }

    public function updateGymCalories(string $date, int $calories): void
    {
        if (isset($this->data['gym'][$date])) {
            $this->data['gym'][$date]['calories'] = $calories;
        }
    }

    public function getGymWeeklyCount(): int
    {
        return count(array_filter(self::getWeekDates(), fn($d) => $this->data['gym'][$d]['done'] ?? false));
    }

    public function getGymMonthlyCount(): int
    {
        $prefix = (new \DateTime())->format('Y-m');
        $count  = 0;
        foreach ($this->data['gym'] as $date => $g) {
            if (str_starts_with($date, $prefix) && ($g['done'] ?? false)) $count++;
        }
        return $count;
    }

    public function getTotalCaloriesThisWeek(): int
    {
        return array_sum(array_map(fn($d) => $this->data['gym'][$d]['calories'] ?? 0, self::getWeekDates()));
    }

    /* ---- Run ---- */

    public function getRun(string $date): array
    {
        return $this->data['run'][$date] ?? [
            'done'     => false,
            'distance' => 0.0,
            'duration' => 0,
            'type'     => 'easy',
            'calories' => 0,
            'notes'    => '',
        ];
    }

    public function toggleRun(string $date): void
    {
        $cur = $this->getRun($date);
        $this->data['run'][$date] = array_merge($cur, ['done' => !$cur['done']]);
    }

    public function updateRun(string $date, array $data): void
    {
        $this->data['run'][$date] = array_merge($this->getRun($date), $data);
    }

    public function getRunWeeklyCount(): int
    {
        return count(array_filter(self::getWeekDates(), fn($d) => $this->getRun($d)['done']));
    }

    public function getRunWeeklyDistance(): float
    {
        return (float) array_sum(array_map(fn($d) => $this->getRun($d)['distance'] ?? 0, self::getWeekDates()));
    }

    public function getRunMonthlyCount(): int
    {
        $prefix = date('Y-m');
        $count  = 0;
        foreach ($this->data['run'] as $date => $r) {
            if (str_starts_with($date, $prefix) && ($r['done'] ?? false)) $count++;
        }
        return $count;
    }

    public function getRunMonthlyDistance(): float
    {
        $prefix = date('Y-m');
        $total  = 0.0;
        foreach ($this->data['run'] as $date => $r) {
            if (str_starts_with($date, $prefix) && ($r['done'] ?? false)) {
                $total += (float)($r['distance'] ?? 0);
            }
        }
        return $total;
    }

    public function getRunPersonalBests(): array
    {
        $done = array_filter(
            $this->data['run'] ?? [],
            fn($r) => ($r['done'] ?? false) && ($r['distance'] ?? 0) > 0
        );
        if (empty($done)) return ['distance' => 0.0, 'pace' => 0.0];

        $bestDist = max(array_map(fn($r) => (float)($r['distance'] ?? 0), $done));

        $paces = [];
        foreach ($done as $r) {
            $dist = (float)($r['distance'] ?? 0);
            $dur  = (int)($r['duration']  ?? 0);
            if ($dist > 0 && $dur > 0) $paces[] = $dur / $dist;
        }

        return [
            'distance' => $bestDist,
            'pace'     => empty($paces) ? 0.0 : min($paces),
        ];
    }

    public function getRunHistory(int $limit = 10): array
    {
        $runs = [];
        foreach ($this->data['run'] ?? [] as $date => $r) {
            if ($r['done'] ?? false) $runs[$date] = array_merge($r, ['date' => $date]);
        }
        krsort($runs);
        return array_values(array_slice($runs, 0, $limit));
    }

    /* ---- Intimacy ---- */

    public function getIntimacy(string $date): int
    {
        return $this->data['intimacy'][$date] ?? 0;
    }

    public function changeIntimacy(string $date, int $delta): void
    {
        $cur = $this->data['intimacy'][$date] ?? 0;
        $this->data['intimacy'][$date] = max(0, $cur + $delta);
    }

    public function getIntimacyMonthlyCount(): int
    {
        $prefix = (new \DateTime())->format('Y-m');
        $total  = 0;
        foreach ($this->data['intimacy'] as $date => $count) {
            if (str_starts_with($date, $prefix)) $total += $count;
        }
        return $total;
    }

    /* ---- Todos ---- */

    public function addDailyTodo(string $date, string $text, string $priority = 'medium'): void
    {
        $this->data['todos']['daily'][$date][] = [
            'id'       => uniqid('todo_'),
            'text'     => $text,
            'done'     => false,
            'priority' => in_array($priority, ['high', 'medium', 'low']) ? $priority : 'medium',
        ];
    }

    public function addWeeklyTodo(string $weekKey, string $text, string $priority = 'medium'): void
    {
        $this->data['todos']['weekly'][$weekKey][] = [
            'id'       => uniqid('todo_'),
            'text'     => $text,
            'done'     => false,
            'priority' => in_array($priority, ['high', 'medium', 'low']) ? $priority : 'medium',
        ];
    }

    public function toggleDailyTodo(string $date, string $id): void
    {
        if (!isset($this->data['todos']['daily'][$date])) return;
        foreach ($this->data['todos']['daily'][$date] as &$t) {
            if ($t['id'] === $id) { $t['done'] = !$t['done']; break; }
        }
        unset($t);
    }

    public function toggleWeeklyTodo(string $weekKey, string $id): void
    {
        if (!isset($this->data['todos']['weekly'][$weekKey])) return;
        foreach ($this->data['todos']['weekly'][$weekKey] as &$t) {
            if ($t['id'] === $id) { $t['done'] = !$t['done']; break; }
        }
        unset($t);
    }

    public function deleteDailyTodo(string $date, string $id): void
    {
        $this->data['todos']['daily'][$date] = array_values(
            array_filter($this->data['todos']['daily'][$date] ?? [], fn($t) => $t['id'] !== $id)
        );
    }

    public function deleteWeeklyTodo(string $weekKey, string $id): void
    {
        $this->data['todos']['weekly'][$weekKey] = array_values(
            array_filter($this->data['todos']['weekly'][$weekKey] ?? [], fn($t) => $t['id'] !== $id)
        );
    }

    public function getDailyTodos(string $date): array  { return $this->data['todos']['daily'][$date] ?? []; }
    public function getWeeklyTodos(string $wk): array   { return $this->data['todos']['weekly'][$wk] ?? []; }

    /* ---- Notes & Reflections ---- */

    public function updateNote(string $date, string $text): void      { $this->data['notes'][$date] = $text; }
    public function getNote(string $date): string                     { return $this->data['notes'][$date] ?? ''; }

    public function updateReflection(string $date, string $field, string $text): void
    {
        $this->data['reflections'][$date][$field] = $text;
    }

    public function getReflection(string $date): array
    {
        return $this->data['reflections'][$date] ?? ['good' => '', 'improve' => ''];
    }

    public function deleteReflection(string $date): void
    {
        unset($this->data['reflections'][$date]);
    }

    /**
     * All reflections that have content, newest first.
     * Returns [ ['date'=>, 'good'=>, 'improve'=>], ... ]
     */
    public function getAllReflections(): array
    {
        $out = [];
        foreach ($this->data['reflections'] ?? [] as $date => $r) {
            $good    = $r['good']    ?? '';
            $improve = $r['improve'] ?? '';
            if ($good === '' && $improve === '') continue;
            $out[] = ['date' => $date, 'good' => $good, 'improve' => $improve];
        }
        usort($out, fn($a, $b) => strcmp($b['date'], $a['date'])); // newest first
        return $out;
    }

    /* ---- Goals ---- */

    public function updateGoal(string $mk, string $field, int $value): void { $this->data['goals'][$mk][$field] = $value; }
    public function getGoals(string $mk): array { return $this->data['goals'][$mk] ?? []; }

    /* ---- Reminders ---- */

    public function updateReminder(string $key, string $time): void
    {
        if (!$time) unset($this->data['reminders'][$key]);
        else        $this->data['reminders'][$key] = $time;
    }

    public function getReminders(): array { return $this->data['reminders'] ?? []; }

    /* ---- Static helpers ---- */

    public static function getWeekDates(?string $date = null): array
    {
        $now = $date ? new \DateTime($date) : new \DateTime();
        $dow = (int)$now->format('N');
        $mon = clone $now;
        $mon->modify('-' . ($dow - 1) . ' days');
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $d = clone $mon;
            $d->modify("+$i days");
            $dates[] = $d->format('Y-m-d');
        }
        return $dates;
    }

    public static function getMonthDates(): array
    {
        $now  = new \DateTime();
        $y    = (int)$now->format('Y');
        $m    = (int)$now->format('m');
        $days = cal_days_in_month(CAL_GREGORIAN, $m, $y);
        $out  = [];
        for ($i = 1; $i <= $days; $i++) {
            $out[] = sprintf('%04d-%02d-%02d', $y, $m, $i);
        }
        return $out;
    }

    public static function getWeekKey(?string $date = null): string
    {
        return self::getWeekDates($date)[0];
    }

    /**
     * Return all dates (Y-m-d) from N months ago up to today, inclusive.
     * Used for multi-month heatmap views.
     */
    public static function getRangeDates(int $months): array
    {
        $end    = new \DateTime('today');
        $cursor = (new \DateTime('today'))->modify("-{$months} months")->modify('+1 day');
        $out    = [];
        while ($cursor <= $end) {
            $out[] = $cursor->format('Y-m-d');
            $cursor->modify('+1 day');
        }
        return $out;
    }

    /** Map a UI range key to a month count (null = monthly calendar view). */
    public static function rangeToMonths(string $range): ?int
    {
        return match ($range) {
            '3m'  => 3,
            '6m'  => 6,
            '12m' => 12,
            default => null, // 'month'
        };
    }

    /**
     * Build per-month rows of daily cells for the multi-month strip view.
     *
     * $cellFn(string $date): array  must return ['active' => bool, 'value' => int, 'title' => string]
     *
     * Returns: [
     *   'rows'       => [ ['label' => '4 Mar – 3 Apr', 'cells' => [['active'=>bool,'title'=>str], ...]], ... ],
     *   'activeDays' => int,
     *   'total'      => int,
     *   'title'      => '4 Mar – 3 Jun 2026',
     * ]
     */
    public static function buildStripRows(int $months, callable $cellFn): array
    {
        $monthShort = ['', 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $todayDt = new \DateTime('today');
        $anchor  = (new \DateTime('today'))->modify("-{$months} months")->modify('+1 day');

        // Show year on row labels only when the range crosses calendar years
        $crossYear = $anchor->format('Y') !== $todayDt->format('Y');
        $fmt = fn(\DateTime $d) => $d->format('j') . ' ' . $monthShort[(int)$d->format('n')]
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

            $rows[] = [
                'label' => $fmt($pStart) . ' – ' . $fmt($pEnd),
                'cells' => $cells,
            ];
        }

        $startYear = $anchor->format('Y');
        $endYear   = $todayDt->format('Y');
        $title = $anchor->format('j') . ' ' . $monthShort[(int)$anchor->format('n')]
               . ($startYear !== $endYear ? ' ' . $startYear : '')
               . ' – ' . $todayDt->format('j') . ' ' . $monthShort[(int)$todayDt->format('n')]
               . ' ' . $endYear;

        return [
            'rows'       => $rows,
            'activeDays' => $activeDays,
            'total'      => $total,
            'title'      => $title,
        ];
    }

    public function getMissedDays(): array
    {
        $missed = [];
        for ($i = 7; $i >= 1; $i--) {
            $d = new \DateTime();
            $d->modify("-$i days");
            $ds    = $d->format('Y-m-d');
            $stats = $this->getSholatStats($ds);
            if ($stats['wajib'] < 5) {
                $missed[] = ['date' => $ds, 'sholatWajib' => $stats['wajib'], 'hasGym' => $this->getGym($ds)['done']];
            }
        }
        return $missed;
    }

    public function getAll30DaysStats(): array
    {
        $out = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = new \DateTime();
            $d->modify("-$i days");
            $ds    = $d->format('Y-m-d');
            $out[] = [
                'date'      => $ds,
                'sholat'    => $this->getSholatStats($ds),
                'spiritual' => $this->getSpiritualDay($ds),
                'gym'       => $this->getGym($ds),
                'run'       => $this->getRun($ds),
                'cycling'   => $this->getCycling($ds),
                'swimming'  => $this->getSwimming($ds),
                'racket'    => $this->getRacket($ds),
                'custom'    => $this->getCustomSport($ds),
                'intimacy'  => $this->getIntimacy($ds),
                'mood'      => $this->getMood($ds),
            ];
        }
        return $out;
    }

    /* ---- Job Applications ---- */

    public function getApplications(): array
    {
        return $this->data['applications'] ?? [];
    }

    public function addApplication(array $data): string
    {
        $id = uniqid('app_');
        $this->data['applications'][] = array_merge([
            'id'           => $id,
            'company'      => '',
            'position'     => '',
            'location'     => null,
            'salary'       => null,
            'applied_date' => date('Y-m-d'),
            'status'       => 'applied',
            'job_url'      => null,
            'stage'        => null,
            'notes'        => null,
            'created_at'   => date('Y-m-d H:i:s'),
        ], $data, ['id' => $id]);
        return $id;
    }

    public function updateApplication(string $id, array $data): void
    {
        foreach ($this->data['applications'] as &$app) {
            if ($app['id'] === $id) {
                $app = array_merge($app, $data, ['id' => $id]);
                break;
            }
        }
    }

    public function deleteApplication(string $id): void
    {
        $this->data['applications'] = array_values(
            array_filter($this->data['applications'], fn($a) => $a['id'] !== $id)
        );
        // Also remove linked interviews
        $this->data['interviews'] = array_values(
            array_filter($this->data['interviews'], fn($iv) => ($iv['application_id'] ?? null) !== $id)
        );
    }

    public function findApplication(string $id): ?array
    {
        foreach ($this->data['applications'] as $app) {
            if ($app['id'] === $id) return $app;
        }
        return null;
    }

    public function getApplicationCounts(): array
    {
        $apps = $this->data['applications'];
        $statuses = ['applied', 'review', 'interview', 'offer', 'hired', 'rejected'];
        $counts = [];
        foreach ($statuses as $s) {
            $counts[$s] = count(array_filter($apps, fn($a) => ($a['status'] ?? '') === $s));
        }
        return $counts;
    }

    /* ---- Interviews ---- */

    public function getInterviews(): array
    {
        return $this->data['interviews'] ?? [];
    }

    public function addInterview(array $data): string
    {
        $id = uniqid('iv_');
        $this->data['interviews'][] = array_merge([
            'id'             => $id,
            'company'        => '',
            'position'       => '',
            'date'           => date('Y-m-d'),
            'time'           => '09:00',
            'type'           => 'video',
            'round'          => null,
            'location'       => null,
            'interviewer'    => null,
            'notes'          => null,
            'completed'      => false,
            'application_id' => null,
            'created_at'     => date('Y-m-d H:i:s'),
        ], $data, ['id' => $id]);
        return $id;
    }

    public function updateInterview(string $id, array $data): void
    {
        foreach ($this->data['interviews'] as &$iv) {
            if ($iv['id'] === $id) {
                $iv = array_merge($iv, $data, ['id' => $id]);
                break;
            }
        }
    }

    public function deleteInterview(string $id): void
    {
        $this->data['interviews'] = array_values(
            array_filter($this->data['interviews'], fn($iv) => $iv['id'] !== $id)
        );
    }

    public function findInterview(string $id): ?array
    {
        foreach ($this->data['interviews'] as $iv) {
            if ($iv['id'] === $id) return $iv;
        }
        return null;
    }

    public function completeInterview(string $id): void
    {
        $this->updateInterview($id, ['completed' => true]);
    }

    public function getUpcomingInterviews(): array
    {
        $today = date('Y-m-d');
        $upcoming = array_filter(
            $this->data['interviews'],
            fn($iv) => !($iv['completed'] ?? false) && ($iv['date'] ?? '') >= $today
        );
        usort($upcoming, fn($a, $b) => strcmp($a['date'] . $a['time'], $b['date'] . $b['time']));
        return array_values($upcoming);
    }

    public function getWeeklyApplicationTrend(): array
    {
        $weeks = [];
        for ($i = 11; $i >= 0; $i--) {
            $d   = new \DateTime();
            $d->modify("-{$i} weeks");
            $mon = clone $d;
            $mon->modify('-' . ((int)$d->format('N') - 1) . ' days');
            $sun = clone $mon;
            $sun->modify('+6 days');
            $weekStart = $mon->format('Y-m-d');
            $weekEnd   = $sun->format('Y-m-d');
            $count = count(array_filter(
                $this->data['applications'],
                fn($a) => ($a['applied_date'] ?? '') >= $weekStart && ($a['applied_date'] ?? '') <= $weekEnd
            ));
            $weeks[] = ['week' => $mon->format('d/m'), 'count' => $count];
        }
        return $weeks;
    }

    /* ---- Links ---- */

    public function getLinks(): array { return $this->data['links'] ?? []; }

    public function addLink(array $data): string
    {
        $id = uniqid('lnk_');
        $this->data['links'][] = [
            'id'         => $id,
            'name'       => $data['name'] ?? '',
            'url'        => $data['url'] ?? '',
            'type'       => $data['type'] ?? 'other',
            'notes'      => $data['notes'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        return $id;
    }

    public function deleteLink(string $id): void
    {
        $this->data['links'] = array_values(
            array_filter($this->data['links'], fn($l) => $l['id'] !== $id)
        );
    }

    public function findLink(string $id): ?array
    {
        foreach ($this->data['links'] as $l) {
            if ($l['id'] === $id) return $l;
        }
        return null;
    }

    /* ---- Files ---- */

    public function getFiles(): array { return $this->data['files'] ?? []; }

    public function addFile(array $data): string
    {
        $id = uniqid('fil_');
        $this->data['files'][] = [
            'id'            => $id,
            'name'          => $data['name'] ?? '',
            'original_name' => $data['original_name'] ?? '',
            'path'          => $data['path'] ?? '',
            'size'          => $data['size'] ?? 0,
            'mime'          => $data['mime'] ?? '',
            'type'          => $data['type'] ?? 'other',
            'notes'         => $data['notes'] ?? null,
            'created_at'    => date('Y-m-d H:i:s'),
        ];
        return $id;
    }

    public function deleteFile(string $id): ?string
    {
        $path = null;
        foreach ($this->data['files'] as $f) {
            if ($f['id'] === $id) { $path = $f['path']; break; }
        }
        $this->data['files'] = array_values(
            array_filter($this->data['files'], fn($f) => $f['id'] !== $id)
        );
        return $path;
    }

    public function findFile(string $id): ?array
    {
        foreach ($this->data['files'] as $f) {
            if ($f['id'] === $id) return $f;
        }
        return null;
    }

    /* ---- Templates ---- */

    public function getTemplates(): array { return $this->data['templates'] ?? []; }

    public function addTemplate(array $data): string
    {
        $id = uniqid('tpl_');
        $this->data['templates'][] = [
            'id'         => $id,
            'title'      => $data['title'] ?? 'Template Baru',
            'category'   => $data['category'] ?? 'email',
            'content'    => $data['content'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        return $id;
    }

    public function updateTemplate(string $id, array $data): void
    {
        foreach ($this->data['templates'] as &$tpl) {
            if ($tpl['id'] === $id) {
                $tpl['title']      = $data['title']    ?? $tpl['title'];
                $tpl['category']   = $data['category'] ?? $tpl['category'];
                $tpl['content']    = $data['content']  ?? $tpl['content'];
                $tpl['updated_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
    }

    public function deleteTemplate(string $id): void
    {
        $this->data['templates'] = array_values(
            array_filter($this->data['templates'], fn($t) => $t['id'] !== $id)
        );
    }

    public function findTemplate(string $id): ?array
    {
        foreach ($this->data['templates'] as $t) {
            if ($t['id'] === $id) return $t;
        }
        return null;
    }

    /* ---- Career Goals ---- */

    public function getCareerGoals(): array
    {
        return array_merge([
            'target_role'    => '',
            'target_company' => '',
            'target_salary'  => '',
            'target_date'    => '',
            'notes'          => '',
            'skills'         => [],
        ], $this->data['career_goals'] ?? []);
    }

    public function updateCareerGoals(array $data): void
    {
        $this->data['career_goals'] = array_merge($this->getCareerGoals(), $data);
    }

    /* ---- Practice Q&A ---- */

    public function getPracticeQA(): array { return $this->data['practice_qa'] ?? []; }

    public function addPracticeQA(array $data): string
    {
        $id = uniqid('qa_');
        $this->data['practice_qa'][] = array_merge([
            'id'             => $id,
            'question'       => '',
            'answer'         => '',
            'category'       => 'general',
            'confidence'     => 3,
            'star_situation' => '',
            'star_task'      => '',
            'star_action'    => '',
            'star_result'    => '',
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ], $data, ['id' => $id]);
        return $id;
    }

    public function updatePracticeQA(string $id, array $data): void
    {
        foreach ($this->data['practice_qa'] as &$qa) {
            if ($qa['id'] === $id) {
                $qa = array_merge($qa, $data, ['id' => $id, 'updated_at' => date('Y-m-d H:i:s')]);
                break;
            }
        }
        unset($qa);
    }

    public function deletePracticeQA(string $id): void
    {
        $this->data['practice_qa'] = array_values(
            array_filter($this->data['practice_qa'] ?? [], fn($q) => $q['id'] !== $id)
        );
    }

    public function findPracticeQA(string $id): ?array
    {
        foreach ($this->data['practice_qa'] ?? [] as $qa) {
            if ($qa['id'] === $id) return $qa;
        }
        return null;
    }

    /* ---- Networking Contacts ---- */

    public function getContacts(): array { return $this->data['contacts'] ?? []; }

    public function addContact(array $data): string
    {
        $id = uniqid('cnt_');
        $this->data['contacts'][] = array_merge([
            'id'           => $id,
            'name'         => '',
            'company'      => '',
            'role'         => '',
            'channel'      => 'linkedin',
            'notes'        => '',
            'connected_at' => date('Y-m-d'),
            'created_at'   => date('Y-m-d H:i:s'),
        ], $data, ['id' => $id]);
        return $id;
    }

    public function deleteContact(string $id): void
    {
        $this->data['contacts'] = array_values(
            array_filter($this->data['contacts'] ?? [], fn($c) => $c['id'] !== $id)
        );
    }

    public function findContact(string $id): ?array
    {
        foreach ($this->data['contacts'] ?? [] as $c) {
            if ($c['id'] === $id) return $c;
        }
        return null;
    }

    /* ---- Mood / Mental ---- */

    public function saveMood(string $date, int $score, int $energy, string $note = ''): void
    {
        $this->data['moods'][$date] = [
            'score'  => max(1, min(5, $score)),
            'energy' => max(1, min(5, $energy)),
            'note'   => $note,
            'time'   => date('H:i'),
        ];
    }

    public function getMood(string $date): array
    {
        return $this->data['moods'][$date] ?? ['score' => 0, 'energy' => 0, 'note' => ''];
    }

    public function getMoodHistory(int $days = 30): array
    {
        $out = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d  = new \DateTime();
            $d->modify("-$i days");
            $ds   = $d->format('Y-m-d');
            $mood = $this->getMood($ds);
            $out[] = [
                'date'   => $ds,
                'score'  => $mood['score'],
                'energy' => $mood['energy'],
                'note'   => $mood['note'],
            ];
        }
        return $out;
    }

    public function getMoodAvg(int $days = 7): float
    {
        $scores = [];
        for ($i = 0; $i < $days; $i++) {
            $d = new \DateTime();
            $d->modify("-$i days");
            $m = $this->getMood($d->format('Y-m-d'));
            if ($m['score'] > 0) $scores[] = $m['score'];
        }
        return empty($scores) ? 0.0 : round(array_sum($scores) / count($scores), 1);
    }

    public function getEnergyAvg(int $days = 7): float
    {
        $scores = [];
        for ($i = 0; $i < $days; $i++) {
            $d = new \DateTime();
            $d->modify("-$i days");
            $m = $this->getMood($d->format('Y-m-d'));
            if ($m['energy'] > 0) $scores[] = $m['energy'];
        }
        return empty($scores) ? 0.0 : round(array_sum($scores) / count($scores), 1);
    }

    public function getLifeScore(string $date = ''): array
    {
        if (!$date) $date = date('Y-m-d');

        // Spiritual (25%): sholat wajib completed
        $sholatStats = $this->getSholatStats($date);
        $spiritual   = min(100, ($sholatStats['wajib'] / 5) * 100);

        // Health (25%): gym + run, 50 pts each
        $gymDone = $this->getGym($date)['done'] ? 50 : 0;
        $runDone = $this->getRun($date)['done'] ? 50 : 0;
        $health  = min(100, $gymDone + $runDone);

        // Mental (25%): mood score /5 * 100
        $mood   = $this->getMood($date);
        $mental = $mood['score'] > 0 ? ($mood['score'] / 5) * 100 : 0;

        // Productivity (25%): tasks done / total
        $todos       = $this->getDailyTodos($date);
        $total       = count($todos);
        $done        = count(array_filter($todos, fn($t) => $t['done']));
        $productivity = $total > 0 ? ($done / $total) * 100 : 0;

        // Overall: average of what's been logged
        $parts = [$spiritual, $health];
        if ($mood['score'] > 0) $parts[] = $mental;
        if ($total > 0)         $parts[] = $productivity;
        $overall = empty($parts) ? 0 : round(array_sum($parts) / count($parts));

        return [
            'overall'      => $overall,
            'spiritual'    => round($spiritual),
            'health'       => round($health),
            'mental'       => round($mental),
            'productivity' => round($productivity),
            'hasMood'      => $mood['score'] > 0,
            'hasTasks'     => $total > 0,
        ];
    }

    public function getInsights(): array
    {
        $insights = [];
        $today    = date('Y-m-d');

        // Sholat streak
        $streak = $this->getSholatStreak();
        if ($streak >= 7) {
            $insights[] = ['type' => 'success', 'icon' => 'streak', 'text' => "Streak sholat {$streak} hari berturut-turut! Luar biasa."];
        } elseif ($streak >= 3) {
            $insights[] = ['type' => 'info', 'icon' => 'prayer', 'text' => "Streak sholat {$streak} hari. Terus jaga!"];
        } elseif ($streak === 0) {
            $insights[] = ['type' => 'warning', 'icon' => 'warning', 'text' => 'Sholat kemarin belum lengkap. Mulai lagi hari ini!'];
        }

        // Gym
        $gymMonthly = $this->getGymMonthlyCount();
        if ($gymMonthly >= 16) {
            $insights[] = ['type' => 'success', 'icon' => 'gym', 'text' => "{$gymMonthly}× gym bulan ini. Target on track!"];
        } elseif ($gymMonthly >= 8) {
            $insights[] = ['type' => 'info', 'icon' => 'gym', 'text' => "{$gymMonthly}× gym bulan ini. Tambah frekuensi agar capai target!"];
        }

        // Run
        $runMonthly = $this->getRunMonthlyCount();
        $runDist    = $this->getRunMonthlyDistance();
        if ($runDist >= 1) {
            $insights[] = ['type' => 'info', 'icon' => 'run', 'text' => number_format($runDist, 1) . " km total lari bulan ini ({$runMonthly} sesi)."];
        }

        // Mood trend
        $moodAvg = $this->getMoodAvg(7);
        if ($moodAvg >= 4) {
            $insights[] = ['type' => 'success', 'icon' => 'mood-good', 'text' => "Rata-rata mood 7 hari: {$moodAvg}/5. Kondisi mental sangat baik!"];
        } elseif ($moodAvg > 0 && $moodAvg < 3) {
            $insights[] = ['type' => 'warning', 'icon' => 'mood-bad', 'text' => "Mood rata-rata minggu ini {$moodAvg}/5. Perlu lebih banyak self-care."];
        } elseif ($moodAvg > 0) {
            $insights[] = ['type' => 'info', 'icon' => 'mood-ok', 'text' => "Rata-rata mood 7 hari: {$moodAvg}/5."];
        }

        // Productivity — today's tasks
        $dailyTodos = $this->getDailyTodos($today);
        if (count($dailyTodos) > 0) {
            $doneTasks  = count(array_filter($dailyTodos, fn($t) => $t['done']));
            $totalTasks = count($dailyTodos);
            if ($doneTasks === $totalTasks) {
                $insights[] = ['type' => 'success', 'icon' => 'tasks-done', 'text' => "Semua {$totalTasks} task harian selesai hari ini!"];
            } else {
                $insights[] = ['type' => 'info', 'icon' => 'tasks', 'text' => "{$doneTasks}/{$totalTasks} task harian selesai hari ini."];
            }
        }

        // Career
        $counts = $this->getApplicationCounts();
        $active = ($counts['applied'] ?? 0) + ($counts['review'] ?? 0) + ($counts['interview'] ?? 0);
        if ($active > 0) {
            $insights[] = ['type' => 'info', 'icon' => 'career', 'text' => "{$active} lamaran aktif sedang menunggu respon."];
        }
        if (($counts['interview'] ?? 0) > 0) {
            $insights[] = ['type' => 'success', 'icon' => 'interview', 'text' => ($counts['interview']) . " lamaran sudah sampai tahap interview!"];
        }

        if (empty($insights)) {
            $insights[] = ['type' => 'info', 'icon' => 'intro', 'text' => 'Mulai tracking aktivitasmu untuk mendapatkan insight personal.'];
        }

        return $insights;
    }

    /* ---- Feature Visibility ---- */

    /* ---- Finance ---- */

    public function addTransaction(array $tx): void
    {
        if (!isset($this->data['finance']['transactions'])) $this->data['finance']['transactions'] = [];
        $this->data['finance']['transactions'][] = $tx;
    }

    public function getTransactions(?string $monthKey = null): array
    {
        $txs = $this->data['finance']['transactions'] ?? [];
        if ($monthKey) {
            $txs = array_values(array_filter($txs, fn($t) => str_starts_with($t['date'] ?? '', $monthKey)));
        }
        usort($txs, fn($a, $b) => ($b['date'] ?? '') <=> ($a['date'] ?? ''));
        return $txs;
    }

    public function updateTransaction(string $id, array $data): void
    {
        foreach ($this->data['finance']['transactions'] ?? [] as &$tx) {
            if (($tx['id'] ?? '') === $id) { $tx = array_merge($tx, $data); break; }
        }
    }

    public function deleteTransaction(string $id): void
    {
        $this->data['finance']['transactions'] = array_values(
            array_filter($this->data['finance']['transactions'] ?? [], fn($t) => ($t['id'] ?? '') !== $id)
        );
    }

    public function getFinanceSummary(string $monthKey): array
    {
        $txs     = $this->getTransactions($monthKey);
        $income  = array_sum(array_map(fn($t) => $t['amount'] ?? 0, array_filter($txs, fn($t) => $t['type'] === 'income')));
        $expense = array_sum(array_map(fn($t) => $t['amount'] ?? 0, array_filter($txs, fn($t) => $t['type'] === 'expense')));
        return ['income' => $income, 'expense' => $expense, 'balance' => $income - $expense];
    }

    public function getSpentByCategory(string $monthKey): array
    {
        $result = [];
        foreach ($this->getTransactions($monthKey) as $tx) {
            if ($tx['type'] === 'expense') {
                $result[$tx['category']] = ($result[$tx['category']] ?? 0) + ($tx['amount'] ?? 0);
            }
        }
        return $result;
    }

    public function getBudget(string $monthKey): array  { return $this->data['finance']['budgets'][$monthKey] ?? []; }

    public function setBudget(string $monthKey, string $cat, int $amount): void
    {
        $this->data['finance']['budgets'][$monthKey][$cat] = $amount;
    }

    public function getSavingsGoals(): array { return array_values($this->data['finance']['savings_goals'] ?? []); }

    public function saveSavingsGoal(array $goal): void
    {
        $goals = $this->data['finance']['savings_goals'] ?? [];
        $found = false;
        foreach ($goals as &$g) {
            if (($g['id'] ?? '') === $goal['id']) { $g = $goal; $found = true; break; }
        }
        if (!$found) $goals[] = $goal;
        $this->data['finance']['savings_goals'] = $goals;
    }

    public function deleteSavingsGoal(string $id): void
    {
        $this->data['finance']['savings_goals'] = array_values(
            array_filter($this->data['finance']['savings_goals'] ?? [], fn($g) => ($g['id'] ?? '') !== $id)
        );
    }

    /* ---- Profile ---- */

    public function getProfile(): array
    {
        return array_merge([
            'setup_done'   => false,
            'display_name' => '',
            'religion'     => '',
            'sports'       => [],
            'custom_sport_name' => '',
        ], $this->data['profile'] ?? []);
    }

    public function updateProfile(array $data): void
    {
        $this->data['profile'] = array_merge($this->getProfile(), $data);
    }

    /* ---- Referral ---- */

    /** Stable referral code for this user (generated once, persisted). */
    public function getReferralCode(): string
    {
        $code = $this->data['profile']['referral_code'] ?? null;
        if (!$code) {
            $name = $this->data['profile']['display_name'] ?? (auth()->user()->username ?? 'user');
            $slug = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $name));
            $slug = substr($slug ?: 'USER', 0, 4);
            $code = $slug . strtoupper(substr(md5((string) (auth()->id() ?? '') . $name . microtime()), 0, 4));
            $this->data['profile']['referral_code'] = $code;
        }
        return $code;
    }

    public function getReferralStats(): array
    {
        return array_merge([
            'invited'   => 0,   // total signed up via code
            'converted' => 0,   // upgraded to paid
            'earnings'  => 0,   // available commission to withdraw (Rp)
            'pending'   => 0,   // pending payout (Rp)
        ], $this->data['profile']['referral_stats'] ?? []);
    }

    public function getPayoutRequests(): array
    {
        $list = $this->data['profile']['payouts'] ?? [];
        return array_reverse($list); // newest first
    }

    public function addPayoutRequest(array $payout): void
    {
        $this->data['profile']['payouts'][] = $payout;
        // Move withdrawn amount from earnings → pending
        $stats = $this->getReferralStats();
        $stats['pending']  = ($stats['pending'] ?? 0) + ($payout['amount'] ?? 0);
        $stats['earnings'] = max(0, ($stats['earnings'] ?? 0) - ($payout['amount'] ?? 0));
        $this->data['profile']['referral_stats'] = $stats;
    }

    /* ---- Subscription Plan ---- */

    public function getPlan(): string
    {
        return $this->data['profile']['plan'] ?? 'freemium';
    }

    public function isFreemium(): bool { return $this->getPlan() === 'freemium'; }
    public function isPlus(): bool     { return $this->getPlan() === 'plus'; }
    public function isPro(): bool      { return $this->getPlan() === 'pro'; }
    public function hasPaidPlan(): bool { return in_array($this->getPlan(), ['plus','pro']); }

    /** Limits per plan */
    public const LAMARAN_LIMIT_FREEMIUM      = 10;
    public const FINANCE_DAYS_LIMIT_FREEMIUM = 7;

    public function getLamaranLimit(): ?int
    {
        return $this->isFreemium() ? self::LAMARAN_LIMIT_FREEMIUM : null;
    }

    public function getFinanceDaysLimit(): ?int
    {
        return $this->isFreemium() ? self::FINANCE_DAYS_LIMIT_FREEMIUM : null;
    }

    public function isAtLamaranLimit(): bool
    {
        $limit = $this->getLamaranLimit();
        if ($limit === null) return false;
        return count($this->data['applications'] ?? []) >= $limit;
    }

    /**
     * Auto-generate contextual notifications based on user state.
     * Returns array of ['id', 'type', 'icon', 'title', 'message', 'link', 'time']
     * Newest first.
     */
    public function getNotifications(): array
    {
        $today      = date('Y-m-d');
        $features   = $this->getFeatures();
        $notifs     = [];

        /* Plan: at lamaran limit */
        if ($this->isAtLamaranLimit()) {
            $notifs[] = [
                'id'      => 'plan.lamaran_limit',
                'type'    => 'warning',
                'icon'    => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                'title'   => __('Batas lamaran tercapai'),
                'message' => __('Kamu sudah mencapai batas 10 lamaran. Upgrade ke Plus untuk tanpa batas.'),
                'link'    => route('settings.langganan'),
                'time'    => __('Sekarang'),
            ];
        }

        /* Sholat: not done today (Islam users only) */
        if (($features['sholat'] ?? false) && (int) date('G') >= 12) {
            $today_stats = $this->getSholatStats($today);
            if ($today_stats['wajib'] < 5) {
                $notifs[] = [
                    'id'      => 'sholat.incomplete',
                    'type'    => 'info',
                    'icon'    => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z',
                    'title'   => __('Sholat hari ini: :n/5', ['n' => $today_stats['wajib']]),
                    'message' => __('Masih ada waktu untuk melengkapi sholat hari ini.'),
                    'link'    => route('sholat'),
                    'time'    => __('Hari ini'),
                ];
            }
        }

        /* Streak milestone */
        $streak = $this->getSholatStreak();
        if (in_array($streak, [3, 7, 14, 30, 60, 100])) {
            $notifs[] = [
                'id'      => 'streak.milestone.' . $streak,
                'type'    => 'success',
                'icon'    => 'M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z',
                'title'   => __(':n hari streak! 🔥', ['n' => $streak]),
                'message' => __('Pertahankan konsistensimu, kamu sedang dalam ritme yang bagus.'),
                'link'    => route('statistik'),
                'time'    => __('Hari ini'),
            ];
        }

        /* Finance: budget warning (≥90%) */
        if ($features['finance'] ?? false) {
            $monthKey = date('Y-m');
            $budget   = $this->getBudget($monthKey);
            $spent    = $this->getSpentByCategory($monthKey);
            foreach ($budget as $cat => $limit) {
                if ($limit <= 0) continue;
                $usedPct = ($spent[$cat] ?? 0) / $limit * 100;
                if ($usedPct >= 90) {
                    $notifs[] = [
                        'id'      => 'budget.warning.' . $cat,
                        'type'    => 'warning',
                        'icon'    => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                        'title'   => __('Anggaran :cat hampir habis', ['cat' => $cat]),
                        'message' => __(':p% sudah terpakai bulan ini.', ['p' => (int) $usedPct]),
                        'link'    => route('finance.anggaran'),
                        'time'    => __('Bulan ini'),
                    ];
                    if (count($notifs) >= 8) break; // cap
                }
            }
        }

        /* Interview reminder: today or tomorrow */
        if ($features['lamaran'] ?? false) {
            foreach ($this->data['applications'] ?? [] as $app) {
                if (($app['status'] ?? '') !== 'interview') continue;
                $intDate = $app['interview_date'] ?? null;
                if (!$intDate) continue;
                if ($intDate === $today) {
                    $notifs[] = [
                        'id'      => 'interview.today.' . ($app['id'] ?? ''),
                        'type'    => 'info',
                        'icon'    => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                        'title'   => __('Interview hari ini'),
                        'message' => ($app['company'] ?? '?') . ' — ' . ($app['position'] ?? '?'),
                        'link'    => route('lamaran.index'),
                        'time'    => __('Hari ini'),
                    ];
                } elseif ($intDate === date('Y-m-d', strtotime('+1 day'))) {
                    $notifs[] = [
                        'id'      => 'interview.tomorrow.' . ($app['id'] ?? ''),
                        'type'    => 'info',
                        'icon'    => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                        'title'   => __('Interview besok'),
                        'message' => ($app['company'] ?? '?') . ' — ' . ($app['position'] ?? '?'),
                        'link'    => route('lamaran.index'),
                        'time'    => __('Besok'),
                    ];
                }
            }
        }

        /* Mood: not logged today */
        if (($features['mental'] ?? false) && (int) date('G') >= 18) {
            $mood = $this->getMood($today);
            if (($mood['score'] ?? 0) === 0) {
                $notifs[] = [
                    'id'      => 'mood.unlogged',
                    'type'    => 'info',
                    'icon'    => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'title'   => __('Catat mood hari ini'),
                    'message' => __('Refleksikan perasaanmu sebelum hari berakhir.'),
                    'link'    => route('mental'),
                    'time'    => __('Hari ini'),
                ];
            }
        }

        return $notifs;
    }

    /* ---- Spiritual (non-Islam) ---- */

    public function getSpiritualDay(string $date): array
    {
        return $this->data['spiritual'][$date] ?? [];
    }

    public function toggleSpiritual(string $date, string $type): bool
    {
        $current = $this->data['spiritual'][$date][$type] ?? false;
        $this->data['spiritual'][$date][$type] = !$current;
        return !$current;
    }

    public function getSpiritualStreak(array $types): int
    {
        $streak = 0;
        $d = new \DateTime('yesterday');
        for ($i = 0; $i < 60; $i++) {
            $day  = $this->getSpiritualDay($d->format('Y-m-d'));
            $done = (bool) count(array_filter($types, fn($t) => !empty($day[$t])));
            if (!$done) break;
            $streak++;
            $d->modify('-1 day');
        }
        return $streak;
    }

    /* ---- Cycling ---- */

    public function getCycling(string $date): array
    {
        return array_merge(['done' => false, 'km' => 0, 'duration' => 0],
            $this->data['cycling'][$date] ?? []);
    }

    public function saveCycling(string $date, float $km, int $duration): void
    {
        $this->data['cycling'][$date] = ['done' => true, 'km' => $km, 'duration' => $duration];
    }

    public function resetCycling(string $date): void
    {
        $this->data['cycling'][$date] = ['done' => false, 'km' => 0, 'duration' => 0];
    }

    public function getAllCycling(): array { return $this->data['cycling'] ?? []; }

    /* ---- Swimming ---- */

    public function getSwimming(string $date): array
    {
        return array_merge(['done' => false, 'laps' => 0, 'duration' => 0],
            $this->data['swimming'][$date] ?? []);
    }

    public function saveSwimming(string $date, int $laps, int $duration): void
    {
        $this->data['swimming'][$date] = ['done' => true, 'laps' => $laps, 'duration' => $duration];
    }

    public function resetSwimming(string $date): void
    {
        $this->data['swimming'][$date] = ['done' => false, 'laps' => 0, 'duration' => 0];
    }

    public function getAllSwimming(): array { return $this->data['swimming'] ?? []; }

    /* ---- Racket Sports (Tennis/Badminton) ---- */

    public function getRacket(string $date): array
    {
        return array_merge(['done' => false, 'sets' => 0],
            $this->data['racket'][$date] ?? []);
    }

    public function saveRacket(string $date, int $sets): void
    {
        $this->data['racket'][$date] = ['done' => true, 'sets' => $sets];
    }

    public function resetRacket(string $date): void
    {
        $this->data['racket'][$date] = ['done' => false, 'sets' => 0];
    }

    public function getAllRacket(): array { return $this->data['racket'] ?? []; }

    /* ---- Custom Sport ---- */

    public function getCustomSport(string $date): array
    {
        return array_merge(['done' => false, 'duration' => 0],
            $this->data['custom_sport'][$date] ?? []);
    }

    public function saveCustomSport(string $date, int $duration): void
    {
        $this->data['custom_sport'][$date] = ['done' => true, 'duration' => $duration];
    }

    public function resetCustomSport(string $date): void
    {
        $this->data['custom_sport'][$date] = ['done' => false, 'duration' => 0];
    }

    /* ---- Features ---- */

    public function getFeatures(): array
    {
        $defaults = [
            'sholat'       => true,
            'spiritual'    => false,
            'gym'          => true,
            'run'          => true,
            'cycling'      => false,
            'swimming'     => false,
            'racket'       => false,
            'custom_sport' => false,
            'intimasi'     => true,
            'tasks'        => true,
            'statistik'    => true,
            'goals'        => true,
            'lamaran'      => true,
            'persiapan'    => true,
            'mental'       => true,
            'insights'     => true,
            'finance'      => true,
        ];
        return array_merge($defaults, $this->data['features'] ?? []);
    }

    public function setFeature(string $key, bool $value): void
    {
        $this->data['features'][$key] = $value;
    }

    public function toggleFeature(string $key): bool
    {
        $features                     = $this->getFeatures();
        $new                          = !($features[$key] ?? true);
        $this->data['features'][$key] = $new;
        return $new;
    }
}
