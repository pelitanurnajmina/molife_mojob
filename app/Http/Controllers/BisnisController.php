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

    public function destroy(string $id)
    {
        BusinessDeal::where('user_id', auth()->id())->findOrFail($id)->delete();
        return redirect()->route('bisnis.deals')->with('toast', __('Data dihapus.'));
    }
}
