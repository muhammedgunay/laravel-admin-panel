<?php

use App\Http\Controllers\ImpersonateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ── Impersonation Routes ──────────────────────────────────────────────
Route::middleware(['web', 'auth'])->prefix('admin')->group(function () {
    Route::post('impersonate/leave', [ImpersonateController::class, 'leave'])->name('impersonate.leave');
});
