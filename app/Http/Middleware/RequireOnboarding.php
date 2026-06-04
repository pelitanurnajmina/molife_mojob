<?php

namespace App\Http\Middleware;

use App\Support\Profile;
use Closure;
use Illuminate\Http\Request;

class RequireOnboarding
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $done = Profile::model()->setup_done;
            if (!$done && !$request->routeIs('onboarding') && !$request->routeIs('onboarding.store')) {
                return redirect()->route('onboarding');
            }
        }
        return $next($request);
    }
}
