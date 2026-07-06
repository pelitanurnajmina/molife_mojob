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
            'address' => $d->address, 'contact' => $d->contact, 'product' => $d->product,
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
        return redirect()->route('bisnis.deals')->with('toast', __('Produk ditambahkan.'));
    }

    public function destroyProduct(string $id)
    {
        BusinessProduct::where('user_id', auth()->id())->where('id', $id)->delete();
        return redirect()->route('bisnis.deals')->with('toast', __('Produk dihapus.'));
    }

    public function update(Request $request, string $id)
    {
        $deal = BusinessDeal::where('user_id', auth()->id())->findOrFail($id);
        $deal->update($request->validate($this->rules));
        return redirect()->route('bisnis.deals')->with('toast', __('Data diperbarui.'));
    }

    /* ── Ekspor / impor proposal & klien ── */

    private const CSV_HEADERS = ['Nama Klien', 'Bidang', 'Alamat', 'Kontak', 'Produk', 'Nilai', 'Status', 'Tanggal Proposal', 'Catatan'];

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
                $q($d->client_name), $q($d->industry), $q($d->address), $q($d->contact), $q($d->product),
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
            . '"PT Maju Jaya","F&B","Jl. Sudirman No. 1, Jakarta","08123456789","Produk A",5000000,"Deal",2026-06-15,"Klien pertama, bayar lunas"' . "\n"
            . '"CV Sinar Abadi","Retail","","budi@sinarabadi.co.id","Produk A",3500000,"Negosiasi",2026-07-01,"Minta revisi harga"' . "\n";

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

            $product = mb_substr(\App\Support\SpreadsheetReader::pick($row, ['Produk', 'Product', 'Produk Kita']), 0, 255);
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
                'contact'       => mb_substr(\App\Support\SpreadsheetReader::pick($row, ['Kontak', 'Contact', 'Narahubung', 'Telepon', 'Email']), 0, 255) ?: null,
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
