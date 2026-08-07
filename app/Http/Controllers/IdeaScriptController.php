<?php

namespace App\Http\Controllers;

use App\Models\IdeaScript;
use Illuminate\Http\Request;

class IdeaScriptController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $items  = IdeaScript::where('user_id', $userId)
            ->latest('updated_at')->get()
            ->map(fn($i) => [
                'id'      => $i->id,
                'type'    => $i->type,
                'title'   => $i->title,
                'content' => $i->content,
                'updated' => $i->updated_at->translatedFormat('j M Y'),
            ]);

        $ideas   = $items->where('type', 'idea')->values();
        $scripts = $items->where('type', 'script')->values();

        return view('pages.ide-script', compact('ideas', 'scripts'));
    }

    private array $rules = [
        'type'    => 'required|in:idea,script',
        'title'   => 'required|string|max:200',
        'content' => 'nullable|string|max:20000',
    ];

    public function store(Request $request)
    {
        IdeaScript::create($request->validate($this->rules) + ['user_id' => auth()->id()]);

        return redirect()->route('ide-script')->with('toast', __('Tersimpan.'));
    }

    public function update(Request $request, string $id)
    {
        IdeaScript::where('user_id', auth()->id())->findOrFail($id)
            ->update($request->validate($this->rules));

        return redirect()->route('ide-script')->with('toast', __('Diperbarui.'));
    }

    public function destroy(string $id)
    {
        IdeaScript::where('user_id', auth()->id())->findOrFail($id)->delete();

        return redirect()->route('ide-script')->with('toast', __('Dihapus.'));
    }
}
