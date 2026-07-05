<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        // Accept either GOOGLE_REDIRECT or GOOGLE_REDIRECT_URI; fall back to current host.
        'redirect'      => env('GOOGLE_REDIRECT', env('GOOGLE_REDIRECT_URI')) ?: url('/auth/google/callback'),
    ],

    'midtrans' => [
        'server_key'    => env('MIDTRANS_SERVER_KEY'),
        'client_key'    => env('MIDTRANS_CLIENT_KEY'),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    ],

    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        // Model flash dengan free tier; bisa dioverride bila Google memensiunkan model.
        'model'   => env('GEMINI_MODEL', 'gemini-3.5-flash'),
    ],

    // Provider fitur Scan Struk AI: 'gemini' (free tier) atau 'claude' (berbayar).
    'receipt_scan' => [
        'provider' => env('RECEIPT_SCAN_PROVIDER', 'gemini'),
    ],

];
