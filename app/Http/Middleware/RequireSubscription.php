<?php

namespace App\Http\Middleware;

use App\Services\SubscriptionService;
use App\Support\Profile;
use Closure;
use Illuminate\Http\Request;

class RequireSubscription
{
    public function handle(Request $request, Closure $next)
    {
        $userId = auth()->id();
        // Onboarded users without an active subscription are locked to the subscribe page.
        if ($userId && Profile::model($userId)->setup_done && !SubscriptionService::isSubscribed($userId)) {
            return redirect()->route('subscribe');
        }
        return $next($request);
    }
}
