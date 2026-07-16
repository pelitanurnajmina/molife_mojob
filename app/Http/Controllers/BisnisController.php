<?php

namespace App\Http\Controllers;

use App\Models\BusinessDeal;
use App\Models\BusinessProduct;
use App\Services\BusinessService;
use Illuminate\Http\Request;

class BisnisController extends Controller
{
    private array $rules = [
        'client_name'   => 'required|string|max:255',
        'industry'      => 'nullable|string|max:120',
        'address'       => 'nullable|string|max:255',
        'contact'       => 'nullable|string|max:255',
        'channel'       => 'nullable|in:email,whatsapp,sosmed,rekomendasi,telepon,website,event,lainnya',
        'product'       => 'nullable|string|max:255',
        'value'         => 'nullable|integer|min:0',
        'status'        => 'required|in:lead,sent,negotiation,won,lost',
        'proposal_date' => 'nullable|date',
        'notes'         => 'nullable|string',
    ];

    /* ── Overview / analytics ── */
    public function index()
    {
        $userId   = auth()->id();
        $a        = BusinessService::analytics($userId);
        $statuses = BusinessService::statuses();

        $recent = BusinessDeal::where('user_id', $userId)->latest('proposal_date')->latest('id')->limit(5)->get()
            ->map(fn($d) => [
                'client_name' => $d->client_name, 'product' => $d->product,
                'value' => (int) $d->value, 'status' => $d->status,
                'date' => optional($d->proposal_date)->format('Y-m-d'),
            ])->toArray();

        return view('pages.bisnis.index', array_merge($a, compact('statuses', 'recent')));
    }

    /* ── Deals / clients list ── */
    public function deals(Request $request)
    {
        $userId = auth()->id();
        $query  = BusinessDeal::where('user_id', $userId);

        $filterStatus  = $request->input('status', 'all');
        $filterProduct = $request->input('product', 'all');
        if ($filterStatus !== 'all') $query->where('status', $filterStatus);
        if ($filterProduct !== 'all') $query->where('product', $filterProduct);
        if ($q = $request->input('q')) {
            $query->where(fn($w) => $w->where('client_name', 'like', "%$q%")
                ->orWhere('product', 'like', "%$q%")->orWhere('industry', 'like', "%$q%"));
        }

        $deals = $query->orderByDesc('proposal_date')->orderByDesc('id')->get()->map(fn($d) => [
            'id' => $d->id, 'client_name' => $d->client_name, 'industry' => $d->industry,
            'address' => $d->address, 'contact' => $d->contact, 'channel' => $d->channel, 'product' => $d->product,
            'value' => (int) $d->value, 'status' => $d->status,
            'proposal_date' => optional($d->proposal_date)->format('Y-m-d'), 'notes' => $d->notes,
        ])->toArray();

        $counts   = BusinessService::counts($userId);
        $total    = BusinessDeal::where('user_id', $userId)->count();
        $statuses = BusinessService::statuses();
        $productRows = BusinessProduct::where('user_id', $userId)
            ->with(['collaborators' => fn($q) => $q->latest('id')])
            ->orderBy('name')->get();
        $products    = $productRows->pluck('name')->toArray();

        // Jumlah proposal per produk untuk kartu folder.
        $dealsPerProduct = BusinessDeal::where('user_id', $userId)
            ->selectRaw('product, count(*) as c')->groupBy('product')->pluck('c', 'product');

        return view('pages.bisnis.deals', compact('deals', 'counts', 'total', 'filterStatus', 'filterProduct', 'statuses', 'products', 'productRows', 'dealsPerProduct'));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules);
        $data['user_id'] = auth()->id();
        BusinessDeal::create($data);
        return redirect()->route('bisnis.deals')->with('toast', __('Proposal/klien ditambahkan.'));
    }

    /* ── Products (untuk dropdown produk) ── */
    public function storeProduct(Request $request)
    {
        $r = $request->validate(['name' => 'required|string|max:100']);
        BusinessProduct::firstOrCreate(['user_id' => auth()->id(), 'name' => trim($r['name'])]);
        return redirect()->route('bisnis.deals')->with('toast', __('Proyek ditambahkan.'));
    }

    public function destroyProduct(string $id)
    {
        BusinessProduct::where('user_id', auth()->id())->where('id', $id)->delete();
        return redirect()->route('bisnis.deals')->with('toast', __('Proyek dihapus.'));
    }

    public function update(Request $request, string $id)
    {
        $deal = BusinessDeal::where('user_id', auth()->id())->findOrFail($id);
        $deal->update($request->validate($this->rules));
        return redirect()->route('bisnis.deals')->with('toast', __('Data diperbarui.'));
    }

    /* ── Papan tugas lintas-bisnis (POV owner atas semua proyek + tugas umum) ── */

    /** Query tugas yang boleh diakses user: tugas proyek miliknya + tugas umum buatannya. */
    private function taskScope(int $userId)
    {
        $ownedIds = BusinessProduct::where('user_id', $userId)->pluck('id');
        return \App\Models\CollabTask::where(function ($q) use ($ownedIds, $userId) {
            $q->whereIn('business_product_id', $ownedIds)
              ->orWhere(fn($w) => $w->whereNull('business_product_id')->where('created_by', $userId));
        });
    }

    public function tasksBoard()
    {
        $userId   = auth()->id();
        $products = BusinessProduct::where('user_id', $userId)->orderBy('name')->get();

        // Peta assignee per proyek ('' = tugas umum, hanya owner).
        $me = auth()->user()->username ?: __('Saya');
        $assigneesByProject = ['' => [$userId => $me]];
        $productNames = [];
        foreach ($products as $p) {
            $assigneesByProject[(string) $p->id] = \App\Services\CollabService::assignees($p);
            $productNames[$p->id] = $p->name;
        }

        $tasks = $this->taskScope($userId)->with('product')->orderBy('created_at')->get()
            ->map(fn($t) => [
                'id'          => $t->id,
                'title'       => $t->title,
                'note'        => $t->note,
                'status'      => in_array($t->status, \App\Models\CollabTask::STATUSES, true) ? $t->status : 'todo',
                'assignee_id' => $t->assignee_id,
                'assignee'    => $t->assignee_id ? ($assigneesByProject[(string) ($t->business_product_id ?? '')][$t->assignee_id] ?? null) : null,
                'project_id'  => $t->business_product_id,
                'project'     => $t->product?->name,
                'due_date'    => $t->due_date?->toDateString(),
                'due_label'   => $t->due_date?->translatedFormat('j M'),
                'overdue'     => $t->due_date && $t->due_date->isPast() && !$t->due_date->isToday() && $t->status !== 'done',
            ])
            ->groupBy('status');

        return view('pages.bisnis.tugas', compact('tasks', 'products', 'assigneesByProject', 'productNames'));
    }

    /** Validasi tugas lintas-bisnis (proyek opsional; assignee harus anggota proyek terpilih). */
    private function validateBoardTask(Request $request, int $userId): array
    {
        $ownedIds = BusinessProduct::where('user_id', $userId)->pluck('id')->all();

        $v = $request->validate([
            'title'               => 'required|string|max:200',
            'note'                => 'nullable|string|max:500',
            'status'              => 'required|in:' . implode(',', \App\Models\CollabTask::STATUSES),
            'due_date'            => 'nullable|date',
            'business_product_id' => ['nullable', 'integer', \Illuminate\Validation\Rule::in($ownedIds)],
            'assignee_id'         => 'nullable|integer',
        ]);

        // Assignee sah = anggota proyek terpilih; tugas umum hanya bisa ke diri sendiri.
        if (!empty($v['assignee_id'])) {
            $valid = empty($v['business_product_id'])
                ? [$userId]
                : array_keys(\App\Services\CollabService::assignees(BusinessProduct::find($v['business_product_id'])));
            if (!in_array((int) $v['assignee_id'], $valid, true)) $v['assignee_id'] = null;
        }

        return $v;
    }

    public function storeTask(Request $request)
    {
        $userId = auth()->id();
        \App\Models\CollabTask::create($this->validateBoardTask($request, $userId) + ['created_by' => $userId]);
        return redirect()->route('bisnis.tugas')->with('toast', __('Tugas ditambahkan.'));
    }

    public function updateTask(Request $request, string $id)
    {
        $userId = auth()->id();
        $this->taskScope($userId)->findOrFail($id)->update($this->validateBoardTask($request, $userId));
        return redirect()->route('bisnis.tugas')->with('toast', __('Tugas diperbarui.'));
    }

    /** Dipanggil via fetch saat kartu digeser antar kolom. */
    public function moveTask(Request $request, string $id)
    {
        $v = $request->validate(['status' => 'required|in:' . implode(',', \App\Models\CollabTask::STATUSES)]);
        $this->taskScope(auth()->id())->findOrFail($id)->update(['status' => $v['status']]);
        return response()->json(['ok' => true]);
    }

    public function destroyTask(string $id)
    {
        $this->taskScope(auth()->id())->findOrFail($id)->delete();
        return redirect()->route('bisnis.tugas')->with('toast', __('Tugas dihapus.'));
    }

    /* ── Ekspor / impor proposal & klien ── */

    private const CSV_HEADERS = ['Nama Klien', 'Bidang', 'Alamat', 'Kontak', 'Channel', 'Proyek', 'Nilai', 'Status', 'Tanggal Proposal', 'Catatan'];

    /** Label channel ↔ key internal (untuk impor yang toleran). */
    private static function channelFromLabel(string $value): ?string
    {
        $v = preg_replace('/[^a-z]/', '', strtolower($value));
        if ($v === '') return null;
        return match (true) {
            str_contains($v, 'email')                                  => 'email',
            str_contains($v, 'whatsapp') || str_contains($v, 'wa')     => 'whatsapp',
            str_contains($v, 'sosmed') || str_contains($v, 'sosial') || str_contains($v, 'instagram') || str_contains($v, 'tiktok') || str_contains($v, 'facebook') => 'sosmed',
            str_contains($v, 'rekomendasi') || str_contains($v, 'referral') || str_contains($v, 'partner') => 'rekomendasi',
            str_contains($v, 'telepon') || str_contains($v, 'telp') || str_contains($v, 'phone') => 'telepon',
            str_contains($v, 'website') || str_contains($v, 'web')     => 'website',
            str_contains($v, 'event') || str_contains($v, 'offline')   => 'event',
            default                                                    => 'lainnya',
        };
    }

    /** Label status Indonesia ↔ key internal (untuk impor yang toleran). */
    private static function statusFromLabel(string $value): string
    {
        $v = strtolower(trim($value));
        return match (true) {
            in_array($v, ['lead', 'prospek', 'prospect'])            => 'lead',
            in_array($v, ['sent', 'terkirim', 'dikirim'])            => 'sent',
            in_array($v, ['negotiation', 'negosiasi', 'nego'])       => 'negotiation',
            in_array($v, ['won', 'deal', 'menang', 'closing'])       => 'won',
            in_array($v, ['lost', 'batal', 'kalah', 'gagal'])        => 'lost',
            default                                                  => 'lead',
        };
    }

    public function export()
    {
        $statuses = BusinessService::statuses();
        $deals = BusinessDeal::where('user_id', auth()->id())->orderByDesc('proposal_date')->orderByDesc('id')->get();

        $q = fn($v) => '"' . str_replace('"', '""', (string) $v) . '"';
        $csv = "\xEF\xBB\xBF" . implode(',', self::CSV_HEADERS) . "\n";
        foreach ($deals as $d) {
            $csv .= implode(',', [
                $q($d->client_name), $q($d->industry), $q($d->address), $q($d->contact),
                $q(BusinessService::CHANNELS[$d->channel] ?? $d->channel),
                $q($d->product),
                (int) $d->value,
                $q($statuses[$d->status]['label'] ?? $d->status),
                optional($d->proposal_date)->format('Y-m-d') ?? '',
                $q($d->notes),
            ]) . "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="molife-bisnis-' . date('Y-m-d') . '.csv"');
    }

    /** File contoh untuk diisi user sebelum impor. */
    public function importTemplate()
    {
        $csv = "\xEF\xBB\xBF" . implode(',', self::CSV_HEADERS) . "\n"
            . '"PT Maju Jaya","F&B","Jl. Sudirman No. 1, Jakarta","08123456789","WhatsApp","Produk A",5000000,"Deal",2026-06-15,"Klien pertama, bayar lunas"' . "\n"
            . '"CV Sinar Abadi","Retail","","budi@sinarabadi.co.id","Email","Produk A",3500000,"Negosiasi",2026-07-01,"Minta revisi harga"' . "\n";

        return response($csv)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="contoh-impor-bisnis.csv"');
    }

    public function import(Request $request)
    {
        $request->validate(
            ['file' => 'required|file|max:5120|mimes:csv,txt,xlsx'],
            ['file.mimes' => __('Format harus CSV atau Excel (.xlsx).'), 'file.max' => __('Ukuran maksimal 5 MB.')]
        );

        $userId = auth()->id();
        $upload = $request->file('file');
        $parsed = \App\Support\SpreadsheetReader::read($upload->getRealPath(), $upload->getClientOriginalName());
        if ($parsed['error']) {
            return back()->with('toast', $parsed['error']);
        }

        $ok = 0; $skipped = 0;
        foreach ($parsed['rows'] as $row) {
            $client = \App\Support\SpreadsheetReader::pick($row, ['Nama Klien', 'Klien', 'Client', 'Perusahaan', 'Client Name', 'Nama']);
            if ($client === '') { $skipped++; continue; }

            $product = mb_substr(\App\Support\SpreadsheetReader::pick($row, ['Proyek', 'Project', 'Produk', 'Product', 'Produk Kita']), 0, 255);
            if ($product !== '') {
                BusinessProduct::firstOrCreate(['user_id' => $userId, 'name' => $product]);
            }

            $date = \App\Support\SpreadsheetReader::normalizeDate(
                \App\Support\SpreadsheetReader::pick($row, ['Tanggal Proposal', 'Tanggal', 'Date', 'Proposal Date'])
            );

            BusinessDeal::create([
                'user_id'       => $userId,
                'client_name'   => mb_substr($client, 0, 255),
                'industry'      => mb_substr(\App\Support\SpreadsheetReader::pick($row, ['Bidang', 'Industri', 'Industry', 'Bidang Klien']), 0, 120) ?: null,
                'address'       => mb_substr(\App\Support\SpreadsheetReader::pick($row, ['Alamat', 'Address']), 0, 255) ?: null,
                'contact'       => mb_substr(\App\Support\SpreadsheetReader::pick($row, ['Kontak', 'Contact', 'Narahubung']), 0, 255) ?: null,
                'channel'       => self::channelFromLabel(\App\Support\SpreadsheetReader::pick($row, ['Channel', 'Sumber', 'Via', 'Awal Komunikasi'])),
                'product'       => $product ?: null,
                'value'         => \App\Support\SpreadsheetReader::normalizeAmount(\App\Support\SpreadsheetReader::pick($row, ['Nilai', 'Value', 'Nilai Proposal', 'Harga'])),
                'status'        => self::statusFromLabel(\App\Support\SpreadsheetReader::pick($row, ['Status'])),
                'proposal_date' => $date ?: null,
                'notes'         => \App\Support\SpreadsheetReader::pick($row, ['Catatan', 'Notes', 'Respon Klien']) ?: null,
            ]);
            $ok++;
        }

        $msg = __(':n data berhasil diimpor.', ['n' => $ok]);
        if ($skipped > 0) $msg .= ' ' . __(':n baris dilewati (nama klien kosong).', ['n' => $skipped]);

        return redirect()->route('bisnis.deals')->with('toast', $msg);
    }

    public function destroy(string $id)
    {
        BusinessDeal::where('user_id', auth()->id())->findOrFail($id)->delete();
        return redirect()->route('bisnis.deals')->with('toast', __('Data dihapus.'));
    }
}
