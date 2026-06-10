<?php

namespace App\Services;

/**
 * Offline prayer-time calculator (no external API).
 *
 * Uses the standard astronomical algorithm (PrayTimes.org core) with the
 * Kemenag RI Indonesia convention: Fajr 20°, Isha 18°, Asr Shafi'i (factor 1),
 * sunset/maghrib at 0.833° (standard atmospheric refraction).
 */
class PrayerTimeService
{
    public const PRAYERS = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];

    // Kemenag RI parameters
    private const FAJR_ANGLE = 20.0;
    private const ISHA_ANGLE = 18.0;
    private const ASR_FACTOR = 1.0;     // Shafi'i
    private const SUNSET_ANGLE = 0.833; // refraction

    /**
     * Curated list of Indonesian cities: key => [name, lat, lng, tz, zone].
     * tz is the UTC offset in hours (WIB +7, WITA +8, WIT +9).
     */
    public static function cities(): array
    {
        return [
            // ── WIB (+7) ──
            'banda-aceh'    => ['Banda Aceh', 5.5483, 95.3238, 7, 'WIB'],
            'medan'         => ['Medan', 3.5952, 98.6722, 7, 'WIB'],
            'padang'        => ['Padang', -0.9471, 100.4172, 7, 'WIB'],
            'pekanbaru'     => ['Pekanbaru', 0.5071, 101.4478, 7, 'WIB'],
            'jambi'         => ['Jambi', -1.6101, 103.6131, 7, 'WIB'],
            'palembang'     => ['Palembang', -2.9761, 104.7754, 7, 'WIB'],
            'bengkulu'      => ['Bengkulu', -3.8004, 102.2655, 7, 'WIB'],
            'bandar-lampung'=> ['Bandar Lampung', -5.3971, 105.2668, 7, 'WIB'],
            'pangkalpinang' => ['Pangkal Pinang', -2.1316, 106.1169, 7, 'WIB'],
            'tanjungpinang' => ['Tanjung Pinang', 0.9186, 104.4558, 7, 'WIB'],
            'batam'         => ['Batam', 1.1301, 104.0529, 7, 'WIB'],
            'jakarta'       => ['Jakarta', -6.2088, 106.8456, 7, 'WIB'],
            'bogor'         => ['Bogor', -6.5950, 106.8166, 7, 'WIB'],
            'depok'         => ['Depok', -6.4025, 106.7942, 7, 'WIB'],
            'tangerang'     => ['Tangerang', -6.1783, 106.6319, 7, 'WIB'],
            'bekasi'        => ['Bekasi', -6.2383, 106.9756, 7, 'WIB'],
            'serang'        => ['Serang', -6.1200, 106.1503, 7, 'WIB'],
            'bandung'       => ['Bandung', -6.9175, 107.6191, 7, 'WIB'],
            'cirebon'       => ['Cirebon', -6.7320, 108.5523, 7, 'WIB'],
            'tasikmalaya'   => ['Tasikmalaya', -7.3274, 108.2207, 7, 'WIB'],
            'semarang'      => ['Semarang', -6.9667, 110.4167, 7, 'WIB'],
            'yogyakarta'    => ['Yogyakarta', -7.7956, 110.3695, 7, 'WIB'],
            'surakarta'     => ['Surakarta (Solo)', -7.5755, 110.8243, 7, 'WIB'],
            'surabaya'      => ['Surabaya', -7.2575, 112.7521, 7, 'WIB'],
            'malang'        => ['Malang', -7.9666, 112.6326, 7, 'WIB'],
            'kediri'        => ['Kediri', -7.8480, 112.0178, 7, 'WIB'],
            'jember'        => ['Jember', -8.1727, 113.7002, 7, 'WIB'],
            'pontianak'     => ['Pontianak', -0.0263, 109.3425, 7, 'WIB'],
            // ── WITA (+8) ──
            'denpasar'      => ['Denpasar', -8.6705, 115.2126, 8, 'WITA'],
            'mataram'       => ['Mataram', -8.5833, 116.1167, 8, 'WITA'],
            'kupang'        => ['Kupang', -10.1772, 123.6070, 8, 'WITA'],
            'banjarmasin'   => ['Banjarmasin', -3.3186, 114.5944, 8, 'WITA'],
            'palangkaraya'  => ['Palangka Raya', -2.2096, 113.9108, 8, 'WITA'],
            'samarinda'     => ['Samarinda', -0.5022, 117.1536, 8, 'WITA'],
            'balikpapan'    => ['Balikpapan', -1.2379, 116.8529, 8, 'WITA'],
            'makassar'      => ['Makassar', -5.1477, 119.4327, 8, 'WITA'],
            'palu'          => ['Palu', -0.8917, 119.8707, 8, 'WITA'],
            'kendari'       => ['Kendari', -3.9985, 122.5130, 8, 'WITA'],
            'gorontalo'     => ['Gorontalo', 0.5435, 123.0568, 8, 'WITA'],
            'manado'        => ['Manado', 1.4748, 124.8421, 8, 'WITA'],
            // ── WIT (+9) ──
            'ambon'         => ['Ambon', -3.6954, 128.1814, 9, 'WIT'],
            'ternate'       => ['Ternate', 0.7900, 127.3667, 9, 'WIT'],
            'jayapura'      => ['Jayapura', -2.5916, 140.6690, 9, 'WIT'],
            'manokwari'     => ['Manokwari', -0.8615, 134.0620, 9, 'WIT'],
            'sorong'        => ['Sorong', -0.8762, 131.2558, 9, 'WIT'],
        ];
    }

    public static function cityExists(?string $key): bool
    {
        return $key !== null && array_key_exists($key, self::cities());
    }

    public static function cityLabel(?string $key): ?string
    {
        return self::cityExists($key) ? self::cities()[$key][0] : null;
    }

    /**
     * Prayer times for a city on a date.
     * Returns ['Subuh'=>'04:24', 'Dzuhur'=>'11:42', ...] or [] if city unknown.
     */
    public static function forCity(?string $cityKey, ?string $date = null): array
    {
        if (!self::cityExists($cityKey)) return [];
        [, $lat, $lng, $tz] = self::cities()[$cityKey];
        return self::compute($lat, $lng, (float) $tz, $date ?? date('Y-m-d'));
    }

    /** Core computation. Returns map of prayer name => 'HH:MM'. */
    public static function compute(float $lat, float $lng, float $tz, string $date): array
    {
        [$y, $m, $d] = array_map('intval', explode('-', $date));
        $jd = self::julian($y, $m, $d) - $lng / (15 * 24);

        // initial guess times (hours) as day-portions
        $fajr    = self::sunAngleTime($jd, $lat, self::FAJR_ANGLE, 5 / 24, true);
        $dhuhr   = self::midDay($jd, 12 / 24);
        $asr     = self::asrTime($jd, $lat, self::ASR_FACTOR, 13 / 24);
        $maghrib = self::sunAngleTime($jd, $lat, self::SUNSET_ANGLE, 18 / 24, false);
        $isha    = self::sunAngleTime($jd, $lat, self::ISHA_ANGLE, 18 / 24, false);

        $adjust = fn($t) => $t + $tz - $lng / 15;

        return [
            'Subuh'   => self::fmt($adjust($fajr)),
            'Dzuhur'  => self::fmt($adjust($dhuhr)),
            'Ashar'   => self::fmt($adjust($asr)),
            'Maghrib' => self::fmt($adjust($maghrib)),
            'Isya'    => self::fmt($adjust($isha)),
        ];
    }

    /* ── Astronomy helpers (degree-based trig) ── */

    private static function julian(int $y, int $m, int $d): float
    {
        if ($m <= 2) { $y -= 1; $m += 12; }
        $a = floor($y / 100);
        $b = 2 - $a + floor($a / 4);
        return floor(365.25 * ($y + 4716)) + floor(30.6001 * ($m + 1)) + $d + $b - 1524.5;
    }

    /** Sun position: [declination(deg), equationOfTime(hours)]. */
    private static function sunPosition(float $jd): array
    {
        $D = $jd - 2451545.0;
        $g = self::fixAngle(357.529 + 0.98560028 * $D);
        $q = self::fixAngle(280.459 + 0.98564736 * $D);
        $L = self::fixAngle($q + 1.915 * self::dSin($g) + 0.020 * self::dSin(2 * $g));
        $e = 23.439 - 0.00000036 * $D;
        $RA = self::fixHour(rad2deg(atan2(self::dCos($e) * self::dSin($L), self::dCos($L))) / 15);
        $eqt = $q / 15 - $RA;
        $decl = rad2deg(asin(self::dSin($e) * self::dSin($L)));
        return [$decl, $eqt];
    }

    private static function midDay(float $jd, float $t): float
    {
        [, $eqt] = self::sunPosition($jd + $t);
        return self::fixHour(12 - $eqt);
    }

    private static function sunAngleTime(float $jd, float $lat, float $angle, float $t, bool $ccw): float
    {
        [$decl] = self::sunPosition($jd + $t);
        $noon = self::midDay($jd, $t);
        $arg = (-self::dSin($angle) - self::dSin($decl) * self::dSin($lat))
             / (self::dCos($decl) * self::dCos($lat));
        $arg = max(-1.0, min(1.0, $arg));
        $hourAngle = (1 / 15) * rad2deg(acos($arg));
        return $noon + ($ccw ? -$hourAngle : $hourAngle);
    }

    private static function asrTime(float $jd, float $lat, float $factor, float $t): float
    {
        [$decl] = self::sunPosition($jd + $t);
        // shadow-ratio angle
        $angle = -rad2deg(atan(1 / ($factor + tan(deg2rad(abs($lat - $decl))))));
        return self::sunAngleTime($jd, $lat, $angle, $t, false);
    }

    private static function dSin(float $deg): float { return sin(deg2rad($deg)); }
    private static function dCos(float $deg): float { return cos(deg2rad($deg)); }

    private static function fixAngle(float $a): float { $a = fmod($a, 360); return $a < 0 ? $a + 360 : $a; }
    private static function fixHour(float $a): float  { $a = fmod($a, 24);  return $a < 0 ? $a + 24 : $a; }

    private static function fmt(float $hours): string
    {
        $hours = self::fixHour($hours + 0.5 / 60); // round to nearest minute
        $h = (int) floor($hours);
        $min = (int) floor(($hours - $h) * 60);
        return sprintf('%02d:%02d', $h, $min);
    }
}
