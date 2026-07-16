<?php

namespace App\Services;

use App\Mail\CollabInviteMail;
use App\Models\BusinessCollaborator;
use App\Models\BusinessProduct;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CollabService
{
    public const MAX_PER_PRODUCT = 10;

    /** Undang email ke satu produk. Mengembalikan [ok, pesan]. */
    public static function invite(int $ownerId, BusinessProduct $product, string $email): array
    {
        $email = strtolower(trim($email));
        $owner = User::find($ownerId);

        if ($owner && strtolower((string) $owner->email) === $email) {
            return [false, __('Itu email kamu sendiri.')];
        }

        $count = BusinessCollaborator::where('business_product_id', $product->id)->count();
        $existing = BusinessCollaborator::where('business_product_id', $product->id)
            ->where('email', $email)->first();

        if (!$existing && $count >= self::MAX_PER_PRODUCT) {
            return [false, __('Maksimal :n kolaborator per produk.', ['n' => self::MAX_PER_PRODUCT])];
        }
        if ($existing && $existing->status === 'active') {
            return [false, __('Email ini sudah jadi kolaborator.')];
        }

        // Undangan baru, atau kirim ulang undangan pending dengan token baru.
        $collab = $existing ?: new BusinessCollaborator([
            'business_product_id' => $product->id,
            'owner_id'            => $ownerId,
            'email'               => $email,
        ]);
        $collab->token  = Str::random(48);
        $collab->status = 'pending';
        $collab->save();

        try {
            Mail::to($email)->send(new CollabInviteMail($collab->fresh(), $product, $owner));
        } catch (\Throwable $e) {
            Log::error('Collab invite mail failed', ['email' => $email, 'error' => $e->getMessage()]);
            return [true, __('Undangan tersimpan, tapi email gagal terkirim. Bagikan link undangan secara manual.')];
        }

        return [true, $existing ? __('Undangan dikirim ulang.') : __('Undangan terkirim ke :email.', ['email' => $email])];
    }

    /**
     * Terima undangan via token untuk user yang sedang login.
     * Token adalah otorisasi (link hanya dikirim ke email yang diundang), jadi
     * akun ber-email lain pun boleh menerima — asal undangan belum dipakai akun lain.
     * Mengembalikan [collab|null, pesanError].
     */
    public static function acceptByToken(string $token, User $user): array
    {
        $collab = BusinessCollaborator::where('token', $token)->first();
        if (!$collab) {
            return [null, __('Undangan tidak ditemukan atau sudah dicabut.')];
        }
        if ($collab->user_id && $collab->user_id !== $user->id) {
            return [null, __('Undangan ini sudah dipakai akun lain.')];
        }
        if ($collab->owner_id === $user->id) {
            return [null, __('Kamu pemilik proyek ini, tidak perlu menerima undangan sendiri.')];
        }

        $collab->forceFill([
            'user_id'     => $user->id,
            'status'      => 'active',
            'accepted_at' => $collab->accepted_at ?? now(),
        ])->save();

        return [$collab, null];
    }

    /** Produk yang dibagikan ke user ini (kolaborasi aktif). */
    public static function productsFor(int $userId)
    {
        return BusinessCollaborator::with(['product', 'owner'])
            ->where('user_id', $userId)->where('status', 'active')
            ->get()->filter(fn($c) => $c->product !== null)->values();
    }

    /** Undangan pending yang cocok dengan email user (belum diterima). */
    public static function pendingFor(User $user)
    {
        if (!$user->email) return collect();
        return BusinessCollaborator::with(['product', 'owner'])
            ->where('email', strtolower($user->email))->where('status', 'pending')
            ->get()->filter(fn($c) => $c->product !== null)->values();
    }

    /** Akses user ke produk: kolaborator aktif ATAU pemilik produk. Null bila tidak berhak. */
    public static function access(int $userId, int $productId): ?BusinessProduct
    {
        $product = BusinessProduct::find($productId);
        if (!$product) return null;
        if ($product->user_id === $userId) return $product;

        $ok = BusinessCollaborator::where('business_product_id', $productId)
            ->where('user_id', $userId)->where('status', 'active')->exists();

        return $ok ? $product : null;
    }

    /** Peta user yang bisa di-assign tugas proyek: owner + kolaborator aktif. [user_id => nama] */
    public static function assignees(BusinessProduct $product): array
    {
        $list = [$product->user_id => $product->user?->username ?: __('Pemilik')];
        foreach ($product->collaborators()->where('status', 'active')->whereNotNull('user_id')->with('user')->get() as $c) {
            $list[$c->user_id] = $c->user?->username ?: $c->email;
        }
        return $list;
    }

    /** Apakah user punya kolaborasi aktif / undangan pending (untuk nav & paywall). */
    public static function hasAny(int $userId): bool
    {
        static $cache = [];
        if (isset($cache[$userId])) return $cache[$userId];

        $has = BusinessCollaborator::where('user_id', $userId)->where('status', 'active')->exists();
        if (!$has) {
            $email = User::find($userId)?->email;
            $has = $email && BusinessCollaborator::where('email', strtolower($email))->where('status', 'pending')->exists();
        }
        return $cache[$userId] = $has;
    }
}
