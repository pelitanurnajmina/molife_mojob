<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Services\InsightService;
use App\Support\Profile;
use Illuminate\Http\Request;

class LamaranController extends Controller
{
    private array $rules = [
        'company'      => 'required|string|max:255',
        'position'     => 'required|string|max:255',
        'location'     => 'nullable|string|max:255',
        'salary'       => 'nullable|string|max:100',
        'applied_date' => 'required|date',
        'status'       => 'required|in:wishlist,applied,review,interview,offer,hired,rejected',
        'job_type'     => 'nullable|in:fulltime,parttime,internship,freelance,contract',
        'channel'      => 'nullable|in:linkedin,jobstreet,glints,upwork,fiverr,kontrakhub,email,referral,website,other',
        'job_url'      => 'nullable|url|max:500',
        'notes'        => 'nullable|string',
    ];

    public function index(Request $request)
    {
        $userId = auth()->id();
        $query  = JobApplication::where('user_id', $userId);

        $filterStatus = $request->input('status', 'all');
        if ($filterStatus !== 'all') $query->where('status', $filterStatus);
        if ($q = $request->input('q')) {
            $query->where(fn($w) => $w->where('company', 'like', "%$q%")->orWhere('position', 'like', "%$q%"));
        }

        $apps = $query->orderByDesc('applied_date')->get()->map(fn($a) => [
            'id' => $a->id, 'company' => $a->company, 'position' => $a->position,
            'location' => $a->location, 'salary' => $a->salary,
            'applied_date' => optional($a->applied_date)->format('Y-m-d'),
            'status' => $a->status, 'job_type' => $a->job_type, 'channel' => $a->channel,
            'job_url' => $a->job_url, 'notes' => $a->notes,
        ])->toArray();

        $counts = InsightService::applicationCounts($userId);
        $total  = JobApplication::where('user_id', $userId)->count();
        $active = ($counts['applied'] ?? 0) + ($counts['review'] ?? 0) + ($counts['interview'] ?? 0)
                + ($counts['offer'] ?? 0) + ($counts['wishlist'] ?? 0);

        $lamaranLimit = Profile::lamaranLimit($userId);
        $atLimit      = $lamaranLimit !== null && $total >= $lamaranLimit;
        $isFreemium   = Profile::isFreemium($userId);

        return view('pages.lamaran.index', compact(
            'apps', 'counts', 'total', 'active', 'filterStatus',
            'lamaranLimit', 'atLimit', 'isFreemium'
        ));
    }

    public function store(Request $request)
    {
        $userId = auth()->id();
        $limit  = Profile::lamaranLimit($userId);
        if ($limit !== null && JobApplication::where('user_id', $userId)->count() >= $limit) {
            return redirect()->route('lamaran.index')
                ->with('toast', __('Sudah mencapai batas :n lamaran. Upgrade ke Plus untuk tanpa batas.', ['n' => $limit]));
        }

        $data = $request->validate($this->rules);
        $data['user_id'] = $userId;
        JobApplication::create($data);

        return redirect()->route('lamaran.index')->with('toast', 'Lamaran berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $app = JobApplication::where('user_id', auth()->id())->findOrFail($id);
        $app->update($request->validate($this->rules));
        return redirect()->route('lamaran.index')->with('toast', 'Lamaran berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        JobApplication::where('user_id', auth()->id())->findOrFail($id)->delete();
        return redirect()->route('lamaran.index')->with('toast', 'Lamaran berhasil dihapus.');
    }

    public function export()
    {
        if (!Profile::isPro()) {
            return redirect()->route('lamaran.index')->with('toast', __('Export laporan hanya tersedia di paket Pro.'));
        }

        $apps = JobApplication::where('user_id', auth()->id())->orderByDesc('applied_date')->get();

        $csv = "Perusahaan,Posisi,Tipe,Channel,Lokasi,Gaji,Tanggal Melamar,Status,URL,Catatan\n";
        foreach ($apps as $a) {
            $csv .= implode(',', [
                '"' . str_replace('"', '""', $a->company ?? '') . '"',
                '"' . str_replace('"', '""', $a->position ?? '') . '"',
                '"' . str_replace('"', '""', $a->job_type ?? '') . '"',
                '"' . str_replace('"', '""', $a->channel ?? '') . '"',
                '"' . str_replace('"', '""', $a->location ?? '') . '"',
                '"' . str_replace('"', '""', $a->salary ?? '') . '"',
                optional($a->applied_date)->format('Y-m-d') ?? '',
                $a->status ?? '',
                '"' . str_replace('"', '""', $a->job_url ?? '') . '"',
                '"' . str_replace('"', '""', $a->notes ?? '') . '"',
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="molife-lamaran-' . date('Y-m-d') . '.csv"');
    }

    /* ── Impor lamaran dari CSV / Excel ── */

    private static function statusFromLabel(string $v): string
    {
        $v = strtolower(trim($v));
        return match (true) {
            in_array($v, ['wishlist', 'incaran', 'rencana'])                 => 'wishlist',
            in_array($v, ['applied', 'dilamar', 'melamar', 'lamar'])         => 'applied',
            in_array($v, ['review', 'screening', 'proses'])                  => 'review',
            in_array($v, ['interview', 'wawancara'])                         => 'interview',
            in_array($v, ['offer', 'penawaran', 'offering'])                 => 'offer',
            in_array($v, ['hired', 'diterima', 'lolos'])                     => 'hired',
            in_array($v, ['rejected', 'ditolak', 'gagal'])                   => 'rejected',
            default                                                          => 'applied',
        };
    }

    private static function jobTypeFromLabel(string $v): ?string
    {
        $v = preg_replace('/[^a-z]/', '', strtolower($v));
        return match (true) {
            str_contains($v, 'full')                         => 'fulltime',
            str_contains($v, 'part')                         => 'parttime',
            str_contains($v, 'intern') || $v === 'magang'    => 'internship',
            str_contains($v, 'free')                         => 'freelance',
            str_contains($v, 'contract') || $v === 'kontrak' => 'contract',
            default                                          => null,
        };
    }

    private static function channelFromLabel(string $v): ?string
    {
        $v = preg_replace('/[^a-z]/', '', strtolower($v));
        if ($v === '') return null;
        $known = ['linkedin', 'jobstreet', 'glints', 'upwork', 'fiverr', 'kontrakhub', 'email', 'referral', 'website'];
        foreach ($known as $k) {
            if (str_contains($v, $k)) return $k;
        }
        return 'other';
    }

    /** File contoh untuk diisi user sebelum impor (header sama dengan hasil ekspor). */
    public function importTemplate()
    {
        $csv = "\xEF\xBB\xBF" . "Perusahaan,Posisi,Tipe,Channel,Lokasi,Gaji,Tanggal Melamar,Status,URL,Catatan\n"
            . '"Tokopedia","Product Manager","Full Time","LinkedIn","Jakarta","15000000",2026-06-20,"Interview","https://linkedin.com/jobs/123","Interview tahap 2"' . "\n"
            . '"Gojek","Data Analyst","Full Time","Website","Remote","",2026-07-01,"Dilamar","",""' . "\n";

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="contoh-impor-lamaran.csv"');
    }

    public function import(Request $request)
    {
        $request->validate(
            ['file' => 'required|file|max:5120|mimes:csv,txt,xlsx'],
            ['file.mimes' => __('Format harus CSV atau Excel (.xlsx).'), 'file.max' => __('Ukuran maksimal 5 MB.')]
        );

        $userId = auth()->id();
        $upload = $request->file('file');
        $parsed = \App\Support\SpreadsheetReader::read($upload->getRealPath(), $upload->getClientOriginalName());
        if ($parsed['error']) {
            return back()->with('toast', $parsed['error']);
        }

        $ok = 0; $skipped = 0;
        foreach ($parsed['rows'] as $row) {
            $company  = \App\Support\SpreadsheetReader::pick($row, ['Perusahaan', 'Company', 'Nama Perusahaan']);
            $position = \App\Support\SpreadsheetReader::pick($row, ['Posisi', 'Position', 'Jabatan', 'Role']);
            if ($company === '' || $position === '') { $skipped++; continue; }

            $date = \App\Support\SpreadsheetReader::normalizeDate(
                \App\Support\SpreadsheetReader::pick($row, ['Tanggal Melamar', 'Tanggal', 'Applied Date', 'Date'])
            ) ?: date('Y-m-d');

            $url = \App\Support\SpreadsheetReader::pick($row, ['URL', 'Link', 'Job URL']);
            if ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) $url = '';

            JobApplication::create([
                'user_id'      => $userId,
                'company'      => mb_substr($company, 0, 255),
                'position'     => mb_substr($position, 0, 255),
                'location'     => mb_substr(\App\Support\SpreadsheetReader::pick($row, ['Lokasi', 'Location', 'Kota']), 0, 255) ?: null,
                'salary'       => mb_substr(\App\Support\SpreadsheetReader::pick($row, ['Gaji', 'Salary']), 0, 100) ?: null,
                'applied_date' => $date,
                'status'       => self::statusFromLabel(\App\Support\SpreadsheetReader::pick($row, ['Status'])),
                'job_type'     => self::jobTypeFromLabel(\App\Support\SpreadsheetReader::pick($row, ['Tipe', 'Job Type', 'Tipe Kerja'])),
                'channel'      => self::channelFromLabel(\App\Support\SpreadsheetReader::pick($row, ['Channel', 'Sumber', 'Via'])),
                'job_url'      => $url ?: null,
                'notes'        => \App\Support\SpreadsheetReader::pick($row, ['Catatan', 'Notes']) ?: null,
            ]);
            $ok++;
        }

        $msg = __(':n lamaran berhasil diimpor.', ['n' => $ok]);
        if ($skipped > 0) $msg .= ' ' . __(':n baris dilewati (perusahaan/posisi kosong).', ['n' => $skipped]);

        return redirect()->route('lamaran.index')->with('toast', $msg);
    }
}
