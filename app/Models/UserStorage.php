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
            'sholat'       => [],
            'gym'          => [],
            'run'          => [],
            'intimacy'     => [],
            'todos'        => ['daily' => [], 'weekly' => []],
            'notes'        => [],
            'reflections'  => [],
            'goals'        => [],
            'reminders'    => [],
            'applications' => [],
            'interviews'   => [],
            'links'        => [],
            'files'        => [],
            'templates'    => [],
            'features'     => [],
        ], $data);

        if (!isset($this->data['todos']['daily']))  $this->data['todos']['daily']  = [];
        if (!isset($this->data['todos']['weekly'])) $this->data['todos']['weekly'] = [];
        if (!isset($this->data['run']))             $this->data['run']             = [];
        if (!isset($this->data['applications']))    $this->data['applications']    = [];
        if (!isset($this->data['interviews']))      $this->data['interviews']      = [];
        if (!isset($this->data['links']))           $this->data['links']           = [];
        if (!isset($this->data['files']))           $this->data['files']           = [];
        if (!isset($this->data['templates']))       $this->data['templates']       = [];
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

    public function addDailyTodo(string $date, string $text): void
    {
        $this->data['todos']['daily'][$date][] = ['id' => uniqid('todo_'), 'text' => $text, 'done' => false];
    }

    public function addWeeklyTodo(string $weekKey, string $text): void
    {
        $this->data['todos']['weekly'][$weekKey][] = ['id' => uniqid('todo_'), 'text' => $text, 'done' => false];
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
                'date'     => $ds,
                'sholat'   => $this->getSholatStats($ds),
                'gym'      => $this->getGym($ds),
                'run'      => $this->getRun($ds),
                'intimacy' => $this->getIntimacy($ds),
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

    /* ---- Feature Visibility ---- */

    public function getFeatures(): array
    {
        $defaults = [
            'sholat'    => true,
            'gym'       => true,
            'run'       => true,
            'intimasi'  => true,
            'tasks'     => true,
            'statistik' => true,
            'goals'     => true,
            'lamaran'   => true,
            'persiapan' => true,
        ];
        return array_merge($defaults, $this->data['features'] ?? []);
    }

    public function toggleFeature(string $key): bool
    {
        $features       = $this->getFeatures();
        $new            = !($features[$key] ?? true);
        $this->data['features'][$key] = $new;
        return $new;
    }
}
