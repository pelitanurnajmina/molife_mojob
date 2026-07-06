<?php

namespace App\Support;

/**
 * Pembaca CSV & XLSX ringan untuk fitur impor (tanpa dependensi eksternal).
 *
 * - CSV : auto-deteksi delimiter (koma / titik-koma / tab), buang BOM.
 * - XLSX: baca sheet pertama via ZipArchive + SimpleXML (shared strings +
 *         inline string + angka; tanggal serial Excel dikonversi di normalizeDate).
 *
 * Hasil: array baris asosiatif, key = header yang sudah dinormalisasi
 * (huruf kecil, tanpa spasi/karakter aneh) sehingga "Nama Klien" == "nama_klien".
 */
class SpreadsheetReader
{
    public const MAX_ROWS = 500;

    /** @return array{rows: array<int, array<string,string>>, error: ?string} */
    public static function read(string $path, string $originalName): array
    {
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $grid = $ext === 'xlsx' ? self::readXlsx($path) : self::readCsv($path);
        if ($grid === null) {
            return ['rows' => [], 'error' => __('File tidak bisa dibaca. Pastikan formatnya CSV atau Excel (.xlsx).')];
        }

        // Baris pertama = header.
        $grid = array_values(array_filter($grid, fn($r) => count(array_filter($r, fn($c) => trim((string) $c) !== '')) > 0));
        if (count($grid) < 2) {
            return ['rows' => [], 'error' => __('File kosong atau hanya berisi header.')];
        }

        $headers = array_map(fn($h) => self::normalizeHeader((string) $h), array_shift($grid));

        if (count($grid) > self::MAX_ROWS) {
            return ['rows' => [], 'error' => __('Maksimal :n baris per impor. Pecah file kamu menjadi beberapa bagian.', ['n' => self::MAX_ROWS])];
        }

        $rows = [];
        foreach ($grid as $line) {
            $row = [];
            foreach ($headers as $i => $key) {
                if ($key === '') continue;
                $row[$key] = trim((string) ($line[$i] ?? ''));
            }
            $rows[] = $row;
        }

        return ['rows' => $rows, 'error' => null];
    }

    /** "Nama Klien / Perusahaan" → "namaklienperusahaan" */
    public static function normalizeHeader(string $h): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(trim($h)));
    }

    /** Ambil nilai baris berdasarkan beberapa kandidat nama kolom. */
    public static function pick(array $row, array $candidates): string
    {
        foreach ($candidates as $c) {
            $key = self::normalizeHeader($c);
            if (isset($row[$key]) && $row[$key] !== '') return $row[$key];
        }
        return '';
    }

    /**
     * Normalisasi tanggal dari berbagai format umum ke Y-m-d (atau '' bila gagal):
     * 2026-07-06, 06/07/2026, 06-07-2026, 6 Juli 2026 (strtotime), serial Excel (45123).
     */
    public static function normalizeDate(string $value): string
    {
        $value = trim($value);
        if ($value === '') return '';

        // Serial date Excel (hari sejak 1900-01-01; > 25569 berarti setelah 1970).
        if (preg_match('/^\d{4,6}(\.\d+)?$/', $value)) {
            $ts = ((float) $value - 25569) * 86400;
            if ($ts > 0 && $ts < 4102444800) return gmdate('Y-m-d', (int) $ts);
        }

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})/', $value, $m)) {
            return sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]);
        }
        // DD/MM/YYYY atau DD-MM-YYYY (format Indonesia).
        if (preg_match('#^(\d{1,2})[/-](\d{1,2})[/-](\d{4})$#', $value, $m)) {
            return sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]);
        }

        $ts = strtotime($value);
        return $ts !== false ? date('Y-m-d', $ts) : '';
    }

    /** "Rp 1.500.000" / "1,500,000" / "1500000" → 1500000 */
    public static function normalizeAmount(string $value): int
    {
        $digits = preg_replace('/[^\d]/', '', $value);
        return $digits === '' ? 0 : (int) $digits;
    }

    /* ── CSV ── */
    private static function readCsv(string $path): ?array
    {
        $content = @file_get_contents($path);
        if ($content === false) return null;

        // Buang BOM UTF-8.
        if (str_starts_with($content, "\xEF\xBB\xBF")) $content = substr($content, 3);

        // Deteksi delimiter dari baris pertama.
        $firstLine = strtok($content, "\r\n") ?: '';
        $delims = [',' => substr_count($firstLine, ','), ';' => substr_count($firstLine, ';'), "\t" => substr_count($firstLine, "\t")];
        arsort($delims);
        $delimiter = array_key_first($delims);
        if ($delims[$delimiter] === 0) $delimiter = ',';

        $rows = [];
        $fh = fopen('php://memory', 'r+');
        fwrite($fh, $content);
        rewind($fh);
        while (($line = fgetcsv($fh, 0, $delimiter, '"', '\\')) !== false) {
            $rows[] = $line;
        }
        fclose($fh);

        return $rows;
    }

    /* ── XLSX (sheet pertama saja) ── */
    private static function readXlsx(string $path): ?array
    {
        if (!class_exists(\ZipArchive::class)) return null;

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) return null;

        // Shared strings (teks yang dipakai berulang).
        $shared = [];
        if (($xml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            $sst = @simplexml_load_string($xml);
            if ($sst !== false) {
                foreach ($sst->si as $si) {
                    if (isset($si->t)) {
                        $shared[] = (string) $si->t;
                    } else { // rich text: gabung semua run
                        $txt = '';
                        foreach ($si->r as $r) $txt .= (string) $r->t;
                        $shared[] = $txt;
                    }
                }
            }
        }

        // Sheet pertama.
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        if ($sheetXml === false) return null;

        $sheet = @simplexml_load_string($sheetXml);
        if ($sheet === false) return null;

        $rows = [];
        foreach ($sheet->sheetData->row as $row) {
            $cells = [];
            foreach ($row->c as $c) {
                // Kolom dari referensi sel (A1 → 0, B1 → 1, dst).
                $ref = (string) $c['r'];
                preg_match('/^([A-Z]+)/', $ref, $m);
                $col = 0;
                foreach (str_split($m[1] ?? 'A') as $ch) $col = $col * 26 + (ord($ch) - 64);
                $col--;

                $type  = (string) $c['t'];
                $value = '';
                if ($type === 's') {
                    $value = $shared[(int) $c->v] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string) ($c->is->t ?? '');
                } else {
                    $value = (string) ($c->v ?? '');
                }
                $cells[$col] = $value;
            }
            if ($cells) {
                $max = max(array_keys($cells));
                $line = array_fill(0, $max + 1, '');
                foreach ($cells as $i => $v) $line[$i] = $v;
                $rows[] = $line;
            } else {
                $rows[] = [];
            }
        }

        return $rows;
    }
}
