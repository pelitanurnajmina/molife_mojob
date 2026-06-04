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
}
