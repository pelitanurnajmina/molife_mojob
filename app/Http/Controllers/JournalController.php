<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Services\JournalService;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $userId   = auth()->id();
        $today    = date('Y-m-d');
        $template = JournalService::DEFAULT_TEMPLATE;

        // Editing date: from ?date=, clamped to not be in the future
        $editDate = $request->query('date', $today);
        if (!strtotime($editDate) || $editDate > $today) $editDate = $today;

        $meta          = JournalService::template($template);
        $today_content = JournalService::entry($userId, $editDate, $template);
        $streak        = JournalService::streak($userId);
        $history       = JournalService::history($userId);

        return view('pages.journal', compact('today', 'editDate', 'template', 'meta', 'today_content', 'streak', 'history'));
    }

    public function store(Request $request)
    {
        $today    = date('Y-m-d');
        $template = $request->input('template', JournalService::DEFAULT_TEMPLATE);

        // Save to the submitted date (defaults today, never future)
        $date = $request->input('date', $today);
        if (!strtotime($date) || $date > $today) $date = $today;

        $meta = JournalService::template($template);
        $keys = array_column($meta['fields'], 'key');

        $content = [];
        foreach ($keys as $k) {
            $content[$k] = (string) $request->input('content.' . $k, '');
        }

        JournalService::save($userId = auth()->id(), $date, $template, $content);

        return redirect()->route('journal')->with('toast', __('Journal tersimpan. Terus konsisten!'));
    }

    public function destroy(string $id)
    {
        JournalEntry::where('user_id', auth()->id())->where('id', $id)->delete();
        return redirect()->route('journal')->with('toast', __('Entri journal dihapus.'));
    }
}
