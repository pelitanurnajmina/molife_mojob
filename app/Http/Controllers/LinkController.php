<?php

namespace App\Http\Controllers;

use App\Models\ImportantLink;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    private array $rules = [
        'title' => 'required|string|max:255',
        'url'   => 'required|url|max:1000',
        'notes' => 'nullable|string|max:255',
    ];

    public function index()
    {
        $links = ImportantLink::where('user_id', auth()->id())->latest()->get()
            ->map(fn($l) => ['id' => $l->id, 'title' => $l->title, 'url' => $l->url, 'notes' => $l->notes])
            ->toArray();
        return view('pages.links', compact('links'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules);
        $data['user_id'] = auth()->id();
        ImportantLink::create($data);
        return redirect()->route('links')->with('toast', __('Link ditambahkan.'));
    }

    public function update(Request $request, string $id)
    {
        $link = ImportantLink::where('user_id', auth()->id())->findOrFail($id);
        $link->update($request->validate($this->rules));
        return redirect()->route('links')->with('toast', __('Link diperbarui.'));
    }

    public function destroy(string $id)
    {
        ImportantLink::where('user_id', auth()->id())->findOrFail($id)->delete();
        return redirect()->route('links')->with('toast', __('Link dihapus.'));
    }
}
