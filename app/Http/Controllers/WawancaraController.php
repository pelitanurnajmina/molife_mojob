<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;

class WawancaraController extends Controller
{
    public function index()
    {
        $storage  = UserStorage::fromSession();
        $all      = $storage->getInterviews();
        $today    = date('Y-m-d');

        $upcoming  = array_values(array_filter($all, fn($iv) => !($iv['completed'] ?? false) && ($iv['date'] ?? '') >= $today));
        $completed = array_values(array_filter($all, fn($iv) => ($iv['completed'] ?? false)));

        usort($upcoming, fn($a, $b) => strcmp($a['date'] . $a['time'], $b['date'] . $b['time']));
        usort($completed, fn($a, $b) => strcmp($b['date'] . $b['time'], $a['date'] . $a['time']));

        return view('pages.wawancara.index', compact('upcoming', 'completed'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company'        => 'required|string|max:255',
            'position'       => 'required|string|max:255',
            'date'           => 'required|date',
            'time'           => 'required',
            'type'           => 'required|in:video,phone,onsite',
            'round'          => 'nullable|string|max:255',
            'location'       => 'nullable|string|max:255',
            'interviewer'    => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
            'application_id' => 'nullable|string',
        ]);

        $storage = UserStorage::fromSession();
        $storage->addInterview($validated);
        $storage->save();

        return redirect()->route('wawancara.index')
            ->with('toast', 'Wawancara berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'company'     => 'required|string|max:255',
            'position'    => 'required|string|max:255',
            'date'        => 'required|date',
            'time'        => 'required',
            'type'        => 'required|in:video,phone,onsite',
            'round'       => 'nullable|string|max:255',
            'location'    => 'nullable|string|max:255',
            'interviewer' => 'nullable|string|max:255',
            'notes'       => 'nullable|string',
        ]);

        $storage = UserStorage::fromSession();
        if (!$storage->findInterview($id)) abort(404);
        $storage->updateInterview($id, $validated);
        $storage->save();

        return redirect()->route('wawancara.index')
            ->with('toast', 'Wawancara berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $storage = UserStorage::fromSession();
        if (!$storage->findInterview($id)) abort(404);
        $storage->deleteInterview($id);
        $storage->save();

        return redirect()->route('wawancara.index')
            ->with('toast', 'Wawancara berhasil dihapus.');
    }

    public function complete(string $id)
    {
        $storage = UserStorage::fromSession();
        if (!$storage->findInterview($id)) abort(404);
        $storage->completeInterview($id);
        $storage->save();

        return back()->with('toast', 'Wawancara ditandai selesai.');
    }
}
