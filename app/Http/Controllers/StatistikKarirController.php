<?php

namespace App\Http\Controllers;

use App\Models\CareerGoal;
use App\Models\Contact;
use App\Models\Interview;
use App\Models\JobApplication;
use App\Services\InsightService;
use Illuminate\Http\Request;

class StatistikKarirController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $applications = JobApplication::where('user_id', $userId)->orderByDesc('applied_date')->get();
        $appArr = $applications->map(fn($a) => [
            'id' => $a->id, 'company' => $a->company, 'position' => $a->position,
            'location' => $a->location, 'salary' => $a->salary,
            'applied_date' => optional($a->applied_date)->format('Y-m-d'),
            'status' => $a->status, 'job_type' => $a->job_type, 'channel' => $a->channel,
            'job_url' => $a->job_url, 'notes' => $a->notes,
        ])->toArray();

        $jobCounts = InsightService::applicationCounts($userId);
        $jobTotal  = $applications->count();

        // Career goals
        $cg = CareerGoal::firstOrNew(['user_id' => $userId]);
        $careerGoals = [
            'target_role'    => $cg->target_role ?? '',
            'target_company' => $cg->target_company ?? '',
            'target_salary'  => $cg->target_salary ?? '',
            'target_date'    => $cg->target_date ?? '',
            'notes'          => $cg->notes ?? '',
        ];

        // Upcoming interviews
        $upcomingInterviews = Interview::where('user_id', $userId)->where('completed', false)
            ->whereDate('date', '>=', date('Y-m-d'))->orderBy('date')->orderBy('time')->get()
            ->map(fn($iv) => [
                'id' => $iv->id, 'company' => $iv->company, 'position' => $iv->position,
                'date' => optional($iv->date)->format('Y-m-d'), 'time' => $iv->time, 'type' => $iv->type,
            ])->toArray();

        // Contacts
        $contacts = Contact::where('user_id', $userId)->latest()->get()->map(fn($c) => [
            'id' => $c->id, 'name' => $c->name, 'company' => $c->company, 'role' => $c->role,
            'channel' => $c->channel, 'notes' => $c->notes,
            'connected_at' => optional($c->connected_at)->format('Y-m-d'),
        ])->toArray();

        // KPI rates
        $responded     = $jobTotal - ($jobCounts['applied'] ?? 0) - ($jobCounts['wishlist'] ?? 0);
        $responseRate  = $jobTotal > 0 ? round(($responded / $jobTotal) * 100) : 0;
        $interviewed   = ($jobCounts['interview'] ?? 0) + ($jobCounts['offer'] ?? 0) + ($jobCounts['hired'] ?? 0);
        $interviewRate = $jobTotal > 0 ? round(($interviewed / $jobTotal) * 100) : 0;
        $successful    = ($jobCounts['offer'] ?? 0) + ($jobCounts['hired'] ?? 0);
        $successRate   = $jobTotal > 0 ? round(($successful / $jobTotal) * 100) : 0;
        $active = ($jobCounts['wishlist'] ?? 0) + ($jobCounts['applied'] ?? 0)
                + ($jobCounts['review'] ?? 0) + ($jobCounts['interview'] ?? 0) + ($jobCounts['offer'] ?? 0);

        // This / last month
        $thisMonth      = date('Y-m');
        $thisMonthCount = count(array_filter($appArr, fn($a) => str_starts_with($a['applied_date'] ?? '', $thisMonth)));
        $lastMonth      = date('Y-m', strtotime('first day of last month'));
        $lastMonthCount = count(array_filter($appArr, fn($a) => str_starts_with($a['applied_date'] ?? '', $lastMonth)));

        // Weekly trend (12 weeks)
        $weeklyTrend = [];
        for ($w = 11; $w >= 0; $w--) {
            $start = date('Y-m-d', strtotime("monday this week -$w weeks"));
            $end   = date('Y-m-d', strtotime("$start +6 days"));
            $count = count(array_filter($appArr, fn($a) => ($a['applied_date'] ?? '') >= $start && ($a['applied_date'] ?? '') <= $end));
            $weeklyTrend[] = ['week' => date('j M', strtotime($start)), 'count' => $count];
        }
        $trendLabels = array_column($weeklyTrend, 'week');
        $trendCounts = array_column($weeklyTrend, 'count');

        // Pipeline
        $pipeline = [
            ['key'=>'wishlist','label'=>'Wishlist','count'=>$jobCounts['wishlist'] ?? 0,'color'=>'#a78bfa'],
            ['key'=>'applied','label'=>'Dikirim','count'=>$jobCounts['applied'] ?? 0,'color'=>'#6b7280'],
            ['key'=>'review','label'=>'Review','count'=>$jobCounts['review'] ?? 0,'color'=>'#f59e0b'],
            ['key'=>'interview','label'=>'Interview','count'=>$jobCounts['interview'] ?? 0,'color'=>'#3b82f6'],
            ['key'=>'offer','label'=>'Tawaran','count'=>$jobCounts['offer'] ?? 0,'color'=>'#10b981'],
            ['key'=>'hired','label'=>'Diterima','count'=>$jobCounts['hired'] ?? 0,'color'=>'#059669'],
            ['key'=>'rejected','label'=>'Ditolak','count'=>$jobCounts['rejected'] ?? 0,'color'=>'#ef4444'],
        ];

        $recentApps = array_slice($appArr, 0, 5);

        // Heatmap last 60 days
        $heatmap = [];
        for ($i = 59; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $count = count(array_filter($appArr, fn($a) => ($a['applied_date'] ?? '') === $d));
            $heatmap[] = ['date' => $d, 'count' => $count];
        }

        return view('pages.karir.index', compact(
            'jobTotal', 'jobCounts', 'thisMonthCount', 'lastMonthCount',
            'responseRate', 'interviewRate', 'successRate', 'active',
            'pipeline', 'weeklyTrend', 'trendLabels', 'trendCounts',
            'recentApps', 'heatmap', 'careerGoals', 'upcomingInterviews', 'contacts'
        ));
    }

    public function updateGoals(Request $request)
    {
        $validated = $request->validate([
            'target_role'    => 'nullable|string|max:255',
            'target_company' => 'nullable|string|max:255',
            'target_salary'  => 'nullable|string|max:100',
            'target_date'    => 'nullable|date',
            'notes'          => 'nullable|string|max:1000',
        ]);

        CareerGoal::updateOrCreate(['user_id' => auth()->id()], $validated);

        return back()->with('toast', __('Target karir diperbarui.'));
    }

    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'company'      => 'nullable|string|max:255',
            'role'         => 'nullable|string|max:255',
            'channel'      => 'required|in:linkedin,email,referral,event,other',
            'notes'        => 'nullable|string|max:500',
            'connected_at' => 'nullable|date',
        ]);
        $validated['user_id'] = auth()->id();
        Contact::create($validated);

        return back()->with('toast', __('Kontak ditambahkan.'));
    }

    public function destroyContact(string $id)
    {
        Contact::where('user_id', auth()->id())->findOrFail($id)->delete();
        return back()->with('toast', __('Kontak dihapus.'));
    }
}
