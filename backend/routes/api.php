<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\ApplicationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// ======== ПУБЛИКӢ (бидуни аутентификация) ========
Route::post('/auth/login-admin', [AuthController::class, 'loginAdmin']);
Route::post('/auth/login-parent', [AuthController::class, 'loginParent']);
Route::post('/auth/register-parent', [AuthController::class, 'registerParent']);
Route::post('/applications/check-status', [ApplicationController::class, 'checkStatus']);

// Рӯйхати вилоятҳо/ноҳияҳо/мактабҳо (барои формаи ариза)
Route::get('/public/regions', [RegionController::class, 'index']);
Route::get('/public/districts', [DistrictController::class, 'index']);
Route::get('/public/schools', [SchoolController::class, 'index']);

// ======== МАҲФУЗ (бо Sanctum) ========
Route::middleware('auth:sanctum')->group(function () {

    // Аутентификация
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Аризаҳо
    Route::get('/applications', [ApplicationController::class, 'index']);
    Route::post('/applications', [ApplicationController::class, 'store']);
    Route::get('/applications/{application}', [ApplicationController::class, 'show']);
    Route::patch('/applications/{application}/status', [ApplicationController::class, 'updateStatus']);
    Route::post('/applications/{application}/documents', [ApplicationController::class, 'uploadDocument']);
    Route::get('/applications/stats/overview', [ApplicationController::class, 'statistics']);

    // Суперадмин маршрутҳо
    Route::middleware('can:superadmin')->group(function () {
        // Вилоятҳо
        Route::apiResource('regions', RegionController::class);
        // Ноҳияҳо
        Route::apiResource('districts', DistrictController::class);
        // Мактабҳо
        Route::apiResource('schools', SchoolController::class);
        // Корбарон (маъмурон)
        Route::apiResource('users', UserController::class);
    });
});
