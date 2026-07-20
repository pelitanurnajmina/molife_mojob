<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Default bahasa aplikasi: Inggris; user bisa ganti ke Indonesia lewat switcher.
        app()->setLocale(session('locale', 'en'));
        return $next($request);
    }
}
