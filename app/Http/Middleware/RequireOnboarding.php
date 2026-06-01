<?php

namespace App\Http\Middleware;

use App\Models\UserStorage;
use Closure;
use Illuminate\Http\Request;

class RequireOnboarding
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $profile = UserStorage::fromSession()->getProfile();
            if (!$profile['setup_done'] && !$request->routeIs('onboarding') && !$request->routeIs('onboarding.store')) {
                return redirect()->route('onboarding');
            }
        }
        return $next($request);
    }
}
