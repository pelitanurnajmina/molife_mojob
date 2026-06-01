<?php

namespace App\Http\Controllers;

use App\Models\UserStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersiapanController extends Controller
{
    public function index()
    {
        $storage   = UserStorage::fromSession();
        $links     = $storage->getLinks();
        $files     = $storage->getFiles();
        $templates = $storage->getTemplates();
        $practiceQA = $storage->getPracticeQA();

        // Sort newest first
        usort($links,     fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        usort($files,     fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        usort($templates, fn($a, $b) => strcmp($b['updated_at'], $a['updated_at']));
        usort($practiceQA, fn($a, $b) => strcmp($b['updated_at'], $a['updated_at']));

        // Confidence stats
        $qaCount      = count($practiceQA);
        $avgConfidence = $qaCount > 0
            ? round(array_sum(array_column($practiceQA, 'confidence')) / $qaCount, 1)
            : 0;

        return view('pages.persiapan.index', compact(
            'links', 'files', 'templates', 'practiceQA', 'qaCount', 'avgConfidence'
        ));
    }

    /* ---- Links ---- */

    public function storeLink(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'url'   => 'required|url|max:1000',
            'type'  => 'required|in:cv,portfolio,linkedin,github,referral,jobsite,other',
            'notes' => 'nullable|string|max:500',
        ]);

        $storage = UserStorage::fromSession();
        $storage->addLink($validated);
        $storage->save();

        return back()->with('toast', 'Link berhasil disimpan.');
    }

    public function destroyLink(string $id)
    {
        $storage = UserStorage::fromSession();
        if (!$storage->findLink($id)) abort(404);
        $storage->deleteLink($id);
        $storage->save();

        return back()->with('toast', 'Link berhasil dihapus.');
    }

    /* ---- Files ---- */

    public function storeFile(Request $request)
    {
        $request->validate([
            'file'  => 'required|file|max:10240', // 10 MB
            'name'  => 'nullable|string|max:255',
            'type'  => 'required|in:cv,cover_letter,portfolio,certificate,other',
            'notes' => 'nullable|string|max:500',
        ]);

        $uploadedFile = $request->file('file');
        $disk         = 'local';
        $dir          = 'lamaran';

        // Store with unique name to prevent collisions
        $stored = $uploadedFile->store($dir, $disk);

        $storage = UserStorage::fromSession();
        $storage->addFile([
            'name'          => $request->input('name') ?: pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME),
            'original_name' => $uploadedFile->getClientOriginalName(),
            'path'          => $stored,
            'size'          => $uploadedFile->getSize(),
            'mime'          => $uploadedFile->getMimeType(),
            'type'          => $request->input('type'),
            'notes'         => $request->input('notes'),
        ]);
        $storage->save();

        return back()->with('toast', 'File berhasil diunggah.');
    }

    public function downloadFile(string $id)
    {
        $storage = UserStorage::fromSession();
        $file    = $storage->findFile($id);
        if (!$file) abort(404);

        if (!Storage::disk('local')->exists($file['path'])) {
            abort(404, 'File tidak ditemukan di storage.');
        }

        return Storage::disk('local')->download(
            $file['path'],
            $file['original_name'],
            ['Content-Type' => $file['mime']]
        );
    }

    public function destroyFile(string $id)
    {
        $storage = UserStorage::fromSession();
        if (!$storage->findFile($id)) abort(404);

        $path = $storage->deleteFile($id);
        $storage->save();

        if ($path) {
            Storage::disk('local')->delete($path);
        }

        return back()->with('toast', 'File berhasil dihapus.');
    }

    /* ---- Practice Q&A ---- */

    public function storeQA(Request $request)
    {
        $validated = $request->validate([
            'question'       => 'required|string|max:1000',
            'answer'         => 'nullable|string',
            'category'       => 'required|in:general,behavioral,technical,situational,star',
            'confidence'     => 'required|integer|min:1|max:5',
            'star_situation' => 'nullable|string',
            'star_task'      => 'nullable|string',
            'star_action'    => 'nullable|string',
            'star_result'    => 'nullable|string',
        ]);

        $storage = UserStorage::fromSession();
        $storage->addPracticeQA($validated);
        $storage->save();

        return back()->with('toast', __('Pertanyaan disimpan.'));
    }

    public function updateQA(Request $request, string $id)
    {
        $validated = $request->validate([
            'question'       => 'required|string|max:1000',
            'answer'         => 'nullable|string',
            'category'       => 'required|in:general,behavioral,technical,situational,star',
            'confidence'     => 'required|integer|min:1|max:5',
            'star_situation' => 'nullable|string',
            'star_task'      => 'nullable|string',
            'star_action'    => 'nullable|string',
            'star_result'    => 'nullable|string',
        ]);

        $storage = UserStorage::fromSession();
        if (!$storage->findPracticeQA($id)) abort(404);
        $storage->updatePracticeQA($id, $validated);
        $storage->save();

        return back()->with('toast', __('Pertanyaan diperbarui.'));
    }

    public function destroyQA(string $id)
    {
        $storage = UserStorage::fromSession();
        if (!$storage->findPracticeQA($id)) abort(404);
        $storage->deletePracticeQA($id);
        $storage->save();

        return back()->with('toast', __('Pertanyaan dihapus.'));
    }

    /* ---- Templates ---- */

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'required|in:email,cover_letter,linkedin,whatsapp,other',
            'content'  => 'required|string',
        ]);

        $storage = UserStorage::fromSession();
        $storage->addTemplate($validated);
        $storage->save();

        return back()->with('toast', 'Template berhasil disimpan.');
    }

    public function updateTemplate(Request $request, string $id)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'category' => 'required|in:email,cover_letter,linkedin,whatsapp,other',
            'content'  => 'required|string',
        ]);

        $storage = UserStorage::fromSession();
        if (!$storage->findTemplate($id)) abort(404);
        $storage->updateTemplate($id, $validated);
        $storage->save();

        return back()->with('toast', 'Template berhasil diperbarui.');
    }

    public function destroyTemplate(string $id)
    {
        $storage = UserStorage::fromSession();
        if (!$storage->findTemplate($id)) abort(404);
        $storage->deleteTemplate($id);
        $storage->save();

        return back()->with('toast', 'Template berhasil dihapus.');
    }
}
