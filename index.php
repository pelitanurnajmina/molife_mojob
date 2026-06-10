<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel...
$app = require_once __DIR__.'/bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Paksa Laravel memakai folder /public sebagai web root
|--------------------------------------------------------------------------
| Memungkinkan project diakses tanpa /public di URL, sementara helper
| asset() tetap menunjuk ke folder public.
*/
$app->usePublicPath(__DIR__.'/public');

$app->handleRequest(Request::capture());
