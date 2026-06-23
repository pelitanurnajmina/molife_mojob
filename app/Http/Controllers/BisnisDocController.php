<?php

namespace App\Http\Controllers;

use App\Models\BusinessDoc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BisnisDocController extends Controller
{
    public const TYPES = [
        'proposal' => 'Proposal',
        'kontrak'  => 'Kontrak',
        'invoice'  => 'Invoice',
        'katalog'  => 'Katalog Produk',
        'profil'   => 'Company Profile',
        'lainnya'  => 'Lainnya',
    ];

    public const TPL_CATEGORIES = [
        'email'     => 'Email',
        'penawaran' => 'Penawaran',
        'whatsapp'  => 'WhatsApp',
        'followup'  => 'Follow Up',
        'lainnya'   => 'Lainnya',
    ];

    public function index()
    {
        $userId = auth()->id();
        $links = BusinessDoc::where('user_id', $userId)->where('kind', 'link')->latest()->get()
            ->map(fn($d) => ['id' => $d->id, 'title' => $d->title, 'url' => $d->url, 'type' => $d->type, 'notes' => $d->notes])->toArray();
        $files = BusinessDoc::where('user_id', $userId)->where('kind', 'file')->latest()->get()
            ->map(fn($d) => ['id' => $d->id, 'title' => $d->title, 'original_name' => $d->original_name,
                'size' => $d->size, 'mime' => $d->mime, 'type' => $d->type, 'notes' => $d->notes])->toArray();
        $templates = BusinessDoc::where('user_id', $userId)->where('kind', 'template')->latest('updated_at')->get()
            ->map(fn($d) => ['id' => $d->id, 'title' => $d->title, 'category' => $d->category, 'content' => $d->content,
                'updated_at' => $d->updated_at->format('Y-m-d H:i')])->toArray();

        $types         = self::TYPES;
        $tplCategories = self::TPL_CATEGORIES;

        return view('pages.bisnis.docs', compact('links', 'files', 'templates', 'types', 'tplCategories'));
    }

    /* ── Links ── */
    public function storeLink(Request $request)
    {
        $r = $request->validate([
            'title' => 'required|string|max:255',
            'url'   => 'required|url|max:500',
            'type'  => 'nullable|in:' . implode(',', array_keys(self::TYPES)),
            'notes' => 'nullable|string|max:300',
        ]);
        BusinessDoc::create(['user_id' => auth()->id(), 'kind' => 'link'] + $r);
        return redirect()->route('bisnis.docs')->with('toast', __('Link ditambahkan.'));
    }

    /* ── Files ── */
    public function storeFile(Request $request)
    {
        $request->validate([
            'file'  => 'required|file|max:10240',
            'title' => 'nullable|string|max:255',
            'type'  => 'nullable|in:' . implode(',', array_keys(self::TYPES)),
            'notes' => 'nullable|string|max:300',
        ]);
        $uploaded = $request->file('file');
        $path = $uploaded->store('bisnis', 'local');

        BusinessDoc::create([
            'user_id'       => auth()->id(),
            'kind'          => 'file',
            'type'          => $request->input('type'),
            'title'         => $request->title ?: $uploaded->getClientOriginalName(),
            'path'          => $path,
            'original_name' => $uploaded->getClientOriginalName(),
            'mime'          => $uploaded->getClientMimeType(),
            'size'          => $uploaded->getSize(),
            'notes'         => $request->notes,
        ]);
        return redirect()->route('bisnis.docs')->with('toast', __('File diunggah.'));
    }

    public function downloadFile(string $id)
    {
        $file = BusinessDoc::where('user_id', auth()->id())->where('kind', 'file')->findOrFail($id);
        if (!$file->path || !Storage::disk('local')->exists($file->path)) abort(404, 'File tidak ditemukan.');
        return Storage::disk('local')->download($file->path, $file->original_name, ['Content-Type' => $file->mime]);
    }

    /* ── Templates ── */
    private function tplRules(): array
    {
        return [
            'title'    => 'required|string|max:255',
            'category' => 'nullable|in:' . implode(',', array_keys(self::TPL_CATEGORIES)),
            'content'  => 'required|string',
        ];
    }

    public function storeTemplate(Request $request)
    {
        $r = $request->validate($this->tplRules());
        BusinessDoc::create(['user_id' => auth()->id(), 'kind' => 'template'] + $r);
        return redirect()->route('bisnis.docs')->with('toast', __('Template disimpan.'));
    }

    public function updateTemplate(Request $request, string $id)
    {
        $doc = BusinessDoc::where('user_id', auth()->id())->where('kind', 'template')->findOrFail($id);
        $doc->update($request->validate($this->tplRules()));
        return redirect()->route('bisnis.docs')->with('toast', __('Template diperbarui.'));
    }

    /* ── Delete (any doc) ── */
    public function destroy(string $id)
    {
        $doc = BusinessDoc::where('user_id', auth()->id())->findOrFail($id);
        $path = $doc->path;
        $doc->delete();
        if ($path) Storage::disk('local')->delete($path);
        return redirect()->route('bisnis.docs')->with('toast', __('Dihapus.'));
    }
}
