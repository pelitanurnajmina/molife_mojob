<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BisnisDocController;
use App\Models\BusinessCollaborator;
use App\Models\BusinessDeal;
use App\Models\BusinessDoc;
use App\Models\BusinessProduct;
use App\Services\BusinessService;
use App\Services\CollabService;
use Illuminate\Http\Request;

class BisnisCollabController extends Controller
{
    /* ═══════════ Sisi owner (di dalam paywall) ═══════════ */

    public function invite(Request $request, string $productId)
    {
        $product = BusinessProduct::where('user_id', auth()->id())->findOrFail($productId);
        $r = $request->validate(['email' => 'required|email|max:255']);

        [$ok, $message] = CollabService::invite(auth()->id(), $product, $r['email']);

        // Kembali ke halaman asal (kartu folder di Proposal & Klien, atau folder produk).
        return back()->with('toast', $message);
    }

    public function removeMember(string $collabId)
    {
        BusinessCollaborator::where('owner_id', auth()->id())->findOrFail($collabId)->delete();

        return back()->with('toast', __('Kolaborator dihapus.'));
    }

    /* ═══════════ Sisi kolaborator (di luar paywall) ═══════════ */

    /** Terima undangan via link email. Boleh diakses guest: diarahkan login dulu. */
    public function accept(Request $request, string $token)
    {
        if (!auth()->check()) {
            session(['url.intended' => $request->fullUrl()]);
            return redirect()->route('login')->with('status', __('Masuk atau daftar dulu dengan email yang diundang, lalu undangan otomatis diterima.'));
        }

        [$collab, $error] = CollabService::acceptByToken($token, auth()->user());
        if ($error) {
            return redirect()->route('kolaborasi.index')->with('toast', $error);
        }

        return redirect()->route('kolaborasi.workspace', $collab->business_product_id)
            ->with('toast', __('Undangan diterima. Selamat berkolaborasi!'));
    }

    /** Daftar produk yang dibagikan ke saya + undangan pending. */
    public function index()
    {
        $user     = auth()->user();
        $shared   = CollabService::productsFor($user->id);
        $pending  = CollabService::pendingFor($user);

        return view('pages.bisnis.collab.index', compact('shared', 'pending'));
    }

    /** Workspace satu produk: statistik + proposal + template. */
    public function workspace(string $productId)
    {
        $product = CollabService::access(auth()->id(), (int) $productId);
        abort_if(!$product, 403, 'Kamu tidak punya akses ke proyek ini.');

        $ownerId  = $product->user_id;
        $isOwner  = $ownerId === auth()->id();
        $a        = BusinessService::analytics($ownerId, $product->name);
        $statuses = BusinessService::statuses();

        $deals = BusinessDeal::where('user_id', $ownerId)->where('product', $product->name)
            ->orderByDesc('proposal_date')->orderByDesc('id')->get()->map(fn($d) => [
                'id' => $d->id, 'client_name' => $d->client_name, 'industry' => $d->industry,
                'address' => $d->address, 'contact' => $d->contact,
                'value' => (int) $d->value, 'status' => $d->status,
                'proposal_date' => optional($d->proposal_date)->format('Y-m-d'), 'notes' => $d->notes,
            ])->toArray();

        $templates = BusinessDoc::where('user_id', $ownerId)->where('kind', 'template')
            ->where('business_product_id', $product->id)
            ->latest('updated_at')->get()->map(fn($d) => [
                'id' => $d->id, 'title' => $d->title, 'category' => $d->category,
                'content' => $d->content, 'updated_at' => $d->updated_at->format('Y-m-d H:i'),
            ])->toArray();

        $members = $product->collaborators()->where('status', 'active')->with('user')->get()
            ->map(fn($c) => $c->user?->username ?: $c->email)->toArray();

        // Owner mengelola kolaborator langsung di folder produk ini.
        $collabRows = $isOwner
            ? $product->collaborators()->latest('id')->get()
            : collect();

        $ownerName     = $product->user?->username ?: 'Pemilik';
        $tplCategories = BisnisDocController::TPL_CATEGORIES;

        return view('pages.bisnis.collab.workspace', array_merge($a, compact(
            'product', 'isOwner', 'statuses', 'deals', 'templates', 'members', 'collabRows', 'ownerName', 'tplCategories'
        )));
    }

    /* ── Proposal (scoped ke produk, ditulis atas nama owner) ── */

    private array $dealRules = [
        'client_name'   => 'required|string|max:255',
        'industry'      => 'nullable|string|max:120',
        'address'       => 'nullable|string|max:255',
        'contact'       => 'nullable|string|max:255',
        'value'         => 'nullable|integer|min:0',
        'status'        => 'required|in:lead,sent,negotiation,won,lost',
        'proposal_date' => 'nullable|date',
        'notes'         => 'nullable|string',
    ];

    public function storeDeal(Request $request, string $productId)
    {
        $product = CollabService::access(auth()->id(), (int) $productId);
        abort_if(!$product, 403);

        $data = $request->validate($this->dealRules);
        $data['user_id'] = $product->user_id;
        $data['product'] = $product->name;
        BusinessDeal::create($data);

        return redirect()->route('kolaborasi.workspace', $product->id)->with('toast', __('Proposal/klien ditambahkan.'));
    }

    public function updateDeal(Request $request, string $productId, string $id)
    {
        $product = CollabService::access(auth()->id(), (int) $productId);
        abort_if(!$product, 403);

        $deal = BusinessDeal::where('user_id', $product->user_id)->where('product', $product->name)->findOrFail($id);
        $deal->update($request->validate($this->dealRules));

        return redirect()->route('kolaborasi.workspace', $product->id)->with('toast', __('Data diperbarui.'));
    }

    public function destroyDeal(string $productId, string $id)
    {
        $product = CollabService::access(auth()->id(), (int) $productId);
        abort_if(!$product, 403);

        BusinessDeal::where('user_id', $product->user_id)->where('product', $product->name)->findOrFail($id)->delete();

        return redirect()->route('kolaborasi.workspace', $product->id)->with('toast', __('Data dihapus.'));
    }

    /* ── Template pesan (scoped ke produk) ── */

    private function tplRules(): array
    {
        return [
            'title'    => 'required|string|max:255',
            'category' => 'nullable|in:' . implode(',', array_keys(BisnisDocController::TPL_CATEGORIES)),
            'content'  => 'required|string',
        ];
    }

    public function storeTemplate(Request $request, string $productId)
    {
        $product = CollabService::access(auth()->id(), (int) $productId);
        abort_if(!$product, 403);

        BusinessDoc::create([
            'user_id'             => $product->user_id,
            'business_product_id' => $product->id,
            'kind'                => 'template',
        ] + $request->validate($this->tplRules()));

        return redirect()->route('kolaborasi.workspace', $product->id)->with('toast', __('Template disimpan.'));
    }

    public function updateTemplate(Request $request, string $productId, string $id)
    {
        $product = CollabService::access(auth()->id(), (int) $productId);
        abort_if(!$product, 403);

        $doc = BusinessDoc::where('user_id', $product->user_id)->where('kind', 'template')
            ->where('business_product_id', $product->id)->findOrFail($id);
        $doc->update($request->validate($this->tplRules()));

        return redirect()->route('kolaborasi.workspace', $product->id)->with('toast', __('Template diperbarui.'));
    }

    public function destroyTemplate(string $productId, string $id)
    {
        $product = CollabService::access(auth()->id(), (int) $productId);
        abort_if(!$product, 403);

        BusinessDoc::where('user_id', $product->user_id)->where('kind', 'template')
            ->where('business_product_id', $product->id)->findOrFail($id)->delete();

        return redirect()->route('kolaborasi.workspace', $product->id)->with('toast', __('Template dihapus.'));
    }
}
