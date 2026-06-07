<?php

use Illuminate\Support\Facades\Route;

// Системаи қабул — API-only (SPA алоҳида). Маршрути health:
Route::get('/', function () {
    return response()->json([
        'service' => 'МТМУ — Системаи қабули хонандагон',
        'status' => 'ok',
        'api' => '/api',
    ]);
});
