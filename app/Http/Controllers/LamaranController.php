<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class LamaranController extends Controller
{
    public function index(Request $request)
    {
        $storage = UserStorage::fromSession();
        $apps    = $storage->getApplications();

        // Filter by status
        $filterStatus = $request->input('status', 'all');
        if ($filterStatus !== 'all') {
            $apps = array_values(array_filter($apps, fn($a) => ($a['status'] ?? '') === $filterStatus));
        }

        // Search
        if ($q = $request->input('q')) {
            $q    = strtolower($q);
            $apps = array_values(array_filter(
                $apps,
                fn($a) => str_contains(strtolower($a['company'] ?? ''), $q)
                       || str_contains(strtolower($a['position'] ?? ''), $q)
            ));
        }

        // Sort
        usort($apps, fn($a, $b) => strcmp($b['applied_date'] ?? '', $a['applied_date'] ?? ''));

        $counts = $storage->getApplicationCounts();
        $total  = count($storage->getApplications());
        $active = array_sum(array_filter($counts, fn($v, $k) => !in_array($k, ['hired', 'rejected']), ARRAY_FILTER_USE_BOTH));

        return view('pages.lamaran.index', compact('apps', 'counts', 'total', 'active', 'filterStatus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company'      => 'required|string|max:255',
            'position'     => 'required|string|max:255',
            'location'     => 'nullable|string|max:255',
            'salary'       => 'nullable|string|max:100',
            'applied_date' => 'required|date',
            'status'       => 'required|in:applied,review,interview,offer,hired,rejected',
            'job_url'      => 'nullable|url|max:500',
            'stage'        => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        $storage = UserStorage::fromSession();
        $storage->addApplication($validated);
        $storage->save();

        return redirect()->route('lamaran.index')
            ->with('toast', 'Lamaran berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'company'      => 'required|string|max:255',
            'position'     => 'required|string|max:255',
            'location'     => 'nullable|string|max:255',
            'salary'       => 'nullable|string|max:100',
            'applied_date' => 'required|date',
            'status'       => 'required|in:applied,review,interview,offer,hired,rejected',
            'job_url'      => 'nullable|url|max:500',
            'stage'        => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
        ]);

        $storage = UserStorage::fromSession();
        if (!$storage->findApplication($id)) abort(404);
        $storage->updateApplication($id, $validated);
        $storage->save();

        return redirect()->route('lamaran.index')
            ->with('toast', 'Lamaran berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $storage = UserStorage::fromSession();
        if (!$storage->findApplication($id)) abort(404);
        $storage->deleteApplication($id);
        $storage->save();

        return redirect()->route('lamaran.index')
            ->with('toast', 'Lamaran berhasil dihapus.');
    }

    public function export()
    {
        $storage = UserStorage::fromSession();
        $apps    = $storage->getApplications();

        usort($apps, fn($a, $b) => strcmp($b['applied_date'] ?? '', $a['applied_date'] ?? ''));

        $csv = "Perusahaan,Posisi,Lokasi,Gaji,Tanggal Melamar,Status,Tahap,URL,Catatan\n";
        foreach ($apps as $app) {
            $csv .= implode(',', [
                '"' . str_replace('"', '""', $app['company'] ?? '') . '"',
                '"' . str_replace('"', '""', $app['position'] ?? '') . '"',
                '"' . str_replace('"', '""', $app['location'] ?? '') . '"',
                '"' . str_replace('"', '""', $app['salary'] ?? '') . '"',
                $app['applied_date'] ?? '',
                $app['status'] ?? '',
                '"' . str_replace('"', '""', $app['stage'] ?? '') . '"',
                '"' . str_replace('"', '""', $app['job_url'] ?? '') . '"',
                '"' . str_replace('"', '""', $app['notes'] ?? '') . '"',
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="molife-lamaran-' . date('Y-m-d') . '.csv"');
    }
}
