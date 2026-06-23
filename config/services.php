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

];
