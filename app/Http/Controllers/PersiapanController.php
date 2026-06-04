<?php

namespace App\Http\Controllers;

use App\Models\PrepLink;
use App\Models\PrepFile;
use App\Models\PrepTemplate;
use App\Models\PrepQa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersiapanController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $links = PrepLink::where('user_id', $userId)->latest()->get()->map(fn($l) => [
            'id' => $l->id, 'name' => $l->name, 'url' => $l->url, 'type' => $l->type, 'notes' => $l->notes,
        ])->toArray();

        $files = PrepFile::where('user_id', $userId)->latest()->get()->map(fn($f) => [
            'id' => $f->id, 'name' => $f->name, 'original_name' => $f->original_name, 'path' => $f->path,
            'size' => $f->size, 'mime' => $f->mime, 'type' => $f->type, 'notes' => $f->notes,
        ])->toArray();

        $templates = PrepTemplate::where('user_id', $userId)->latest('updated_at')->get()->map(fn($t) => [
            'id' => $t->id, 'title' => $t->title, 'category' => $t->category, 'content' => $t->content,
            'updated_at' => $t->updated_at->format('Y-m-d H:i:s'),
        ])->toArray();

        $practiceQA = PrepQa::where('user_id', $userId)->latest('updated_at')->get()->map(fn($q) => [
            'id' => $q->id, 'question' => $q->question, 'answer' => $q->answer, 'category' => $q->category,
            'confidence' => $q->confidence, 'star_situation' => $q->star_situation, 'star_task' => $q->star_task,
            'star_action' => $q->star_action, 'star_result' => $q->star_result,
        ])->toArray();

        $qaCount       = count($practiceQA);
        $avgConfidence = $qaCount > 0 ? round(array_sum(array_column($practiceQA, 'confidence')) / $qaCount, 1) : 0;

        return view('pages.persiapan.index', compact(
            'links', 'files', 'templates', 'practiceQA', 'qaCount', 'avgConfidence'
        ));
    }

    /* ── Links ── */
    public function storeLink(Request $request)
    {
        $v = $request->validate([
            'name'  => 'required|string|max:255',
            'url'   => 'required|url|max:1000',
            'type'  => 'required|in:cv,portfolio,linkedin,github,referral,jobsite,other',
            'notes' => 'nullable|string|max:500',
        ]);
        $v['user_id'] = auth()->id();
        PrepLink::create($v);
        return back()->with('toast', 'Link berhasil disimpan.');
    }

    public function destroyLink(string $id)
    {
        PrepLink::where('user_id', auth()->id())->findOrFail($id)->delete();
        return back()->with('toast', 'Link berhasil dihapus.');
    }

    /* ── Files ── */
    public function storeFile(Request $request)
    {
        $request->validate([
            'file'  => 'required|file|max:10240',
            'name'  => 'nullable|string|max:255',
            'type'  => 'required|in:cv,cover_letter,portfolio,certificate,other',
            'notes' => 'nullable|string|max:500',
        ]);

        $uploaded = $request->file('file');
        $stored   = $uploaded->store('lamaran', 'local');

        PrepFile::create([
            'user_id'       => auth()->id(),
            'name'          => $request->input('name') ?: pathinfo($uploaded->getClientOriginalName(), PATHINFO_FILENAME),
            'original_name' => $uploaded->getClientOriginalName(),
            'path'          => $stored,
            'size'          => $uploaded->getSize(),
            'mime'          => $uploaded->getMimeType(),
            'type'          => $request->input('type'),
            'notes'         => $request->input('notes'),
        ]);

        return back()->with('toast', 'File berhasil diunggah.');
    }

    public function downloadFile(string $id)
    {
        $file = PrepFile::where('user_id', auth()->id())->findOrFail($id);
        if (!Storage::disk('local')->exists($file->path)) abort(404, 'File tidak ditemukan di storage.');
        return Storage::disk('local')->download($file->path, $file->original_name, ['Content-Type' => $file->mime]);
    }

    public function destroyFile(string $id)
    {
        $file = PrepFile::where('user_id', auth()->id())->findOrFail($id);
        $path = $file->path;
        $file->delete();
        if ($path) Storage::disk('local')->delete($path);
        return back()->with('toast', 'File berhasil dihapus.');
    }

    /* ── Practice Q&A ── */
    private array $qaRules = [
        'question'       => 'required|string|max:1000',
        'answer'         => 'nullable|string',
        'category'       => 'required|in:general,behavioral,technical,situational,star',
        'confidence'     => 'required|integer|min:1|max:5',
        'star_situation' => 'nullable|string',
        'star_task'      => 'nullable|string',
        'star_action'    => 'nullable|string',
        'star_result'    => 'nullable|string',
    ];

    public function storeQA(Request $request)
    {
        $v = $request->validate($this->qaRules);
        $v['user_id'] = auth()->id();
        PrepQa::create($v);
        return back()->with('toast', __('Pertanyaan disimpan.'));
    }

    public function updateQA(Request $request, string $id)
    {
        PrepQa::where('user_id', auth()->id())->findOrFail($id)->update($request->validate($this->qaRules));
        return back()->with('toast', __('Pertanyaan diperbarui.'));
    }

    public function destroyQA(string $id)
    {
        PrepQa::where('user_id', auth()->id())->findOrFail($id)->delete();
        return back()->with('toast', __('Pertanyaan dihapus.'));
    }

    /* ── Templates ── */
    private array $tplRules = [
        'title'    => 'required|string|max:255',
        'category' => 'required|in:email,cover_letter,linkedin,whatsapp,other',
        'content'  => 'required|string',
    ];

    public function storeTemplate(Request $request)
    {
        $v = $request->validate($this->tplRules);
        $v['user_id'] = auth()->id();
        PrepTemplate::create($v);
        return back()->with('toast', 'Template berhasil disimpan.');
    }

    public function updateTemplate(Request $request, string $id)
    {
        PrepTemplate::where('user_id', auth()->id())->findOrFail($id)->update($request->validate($this->tplRules));
        return back()->with('toast', 'Template berhasil diperbarui.');
    }

    public function destroyTemplate(string $id)
    {
        PrepTemplate::where('user_id', auth()->id())->findOrFail($id)->delete();
        return back()->with('toast', 'Template berhasil dihapus.');
    }
}
