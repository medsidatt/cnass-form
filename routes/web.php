<?php

use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\VerifyController;
use Illuminate\Support\Facades\Route;

// WhatsApp OTP verification — rate-limited per session/IP.
Route::middleware('throttle:otp-send')
    ->post('/verify/send', [VerifyController::class, 'send'])
    ->name('verify.send');

Route::middleware('throttle:otp-check')
    ->post('/verify/check', [VerifyController::class, 'check'])
    ->name('verify.check');

// Public form — the URL administrators share with employees.
Route::get('/fiche',   [SubmissionController::class, 'form'])->name('form');
Route::post('/submit', [SubmissionController::class, 'store'])
    ->middleware('throttle:submit')
    ->name('submit');

// Per-submission download (session-protected)
Route::get('/download/{submission}', [SubmissionController::class, 'download'])->name('download');
Route::get('/session/reset',         [SubmissionController::class, 'resetSession'])->name('session.reset');

// Authenticated upload-file proxy (admin or submission owner only).
Route::get('/files/{submission}/{key}', [SubmissionController::class, 'serveFile'])
    ->where('key', '[A-Za-z0-9_\-\/\.]+')
    ->name('files.show');

// Backwards-compatible alias for the previous /admin entry point.
Route::redirect('/admin', '/');

// Admin dashboard lives at the root domain (gated by ADMIN_PASSWORD).
// Accept POST too so the AdminAuth middleware can process the login form.
Route::match(['get', 'post'], '/', [SubmissionController::class, 'index'])
    ->middleware('admin')
    ->name('admin.index');

// Other admin actions stay namespaced under /admin/.
Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
    Route::post('/logout',                  [SubmissionController::class, 'adminLogout'])->name('logout');
    Route::get('/export/excel',             [SubmissionController::class, 'exportExcel'])->name('exportExcel');

    // Hidden URL — no link from the dashboard nav. Type it manually.
    Route::get('/users',                    [SubmissionController::class, 'users'])->name('users');

    Route::get('/{submission}/download',    [SubmissionController::class, 'adminDownload'])->name('download');
    Route::get('/{submission}',             [SubmissionController::class, 'show'])->name('show');
    Route::delete('/{submission}',          [SubmissionController::class, 'destroy'])->name('destroy');
});
