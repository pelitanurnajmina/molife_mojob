<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use Illuminate\Http\Request;

class WawancaraController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $today  = date('Y-m-d');

        $toArr = fn($iv) => [
            'id' => $iv->id, 'company' => $iv->company, 'position' => $iv->position,
            'date' => optional($iv->date)->format('Y-m-d'), 'time' => $iv->time,
            'type' => $iv->type, 'round' => $iv->round, 'location' => $iv->location,
            'interviewer' => $iv->interviewer, 'notes' => $iv->notes,
            'completed' => (bool) $iv->completed, 'application_id' => $iv->application_id,
        ];

        $upcoming = Interview::where('user_id', $userId)->where('completed', false)
            ->whereDate('date', '>=', $today)->orderBy('date')->orderBy('time')->get()->map($toArr)->toArray();
        $completed = Interview::where('user_id', $userId)->where('completed', true)
            ->orderByDesc('date')->orderByDesc('time')->get()->map($toArr)->toArray();

        return view('pages.wawancara.index', compact('upcoming', 'completed'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company'        => 'required|string|max:255',
            'position'       => 'required|string|max:255',
            'date'           => 'required|date',
            'time'           => 'required',
            'type'           => 'required|in:video,phone,onsite',
            'round'          => 'nullable|string|max:255',
            'location'       => 'nullable|string|max:255',
            'interviewer'    => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
            'application_id' => 'nullable|integer',
        ]);
        $data['user_id'] = auth()->id();
        Interview::create($data);

        return redirect()->route('wawancara.index')->with('toast', 'Wawancara berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $iv = Interview::where('user_id', auth()->id())->findOrFail($id);
        $iv->update($request->validate([
            'company'     => 'required|string|max:255',
            'position'    => 'required|string|max:255',
            'date'        => 'required|date',
            'time'        => 'required',
            'type'        => 'required|in:video,phone,onsite',
            'round'       => 'nullable|string|max:255',
            'location'    => 'nullable|string|max:255',
            'interviewer' => 'nullable|string|max:255',
            'notes'       => 'nullable|string',
        ]));

        return redirect()->route('wawancara.index')->with('toast', 'Wawancara berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        Interview::where('user_id', auth()->id())->findOrFail($id)->delete();
        return redirect()->route('wawancara.index')->with('toast', 'Wawancara berhasil dihapus.');
    }

    public function complete(string $id)
    {
        $iv = Interview::where('user_id', auth()->id())->findOrFail($id);
        $iv->update(['completed' => true]);
        return back()->with('toast', 'Wawancara ditandai selesai.');
    }
}
