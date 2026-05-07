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

// Public form (requires phone verification via session)
Route::get('/',        [SubmissionController::class, 'form'])->name('form');
Route::post('/submit', [SubmissionController::class, 'store'])
    ->middleware('throttle:submit')
    ->name('submit');

// Per-submission download (session-protected)
Route::get('/download/{submission}', [SubmissionController::class, 'download'])->name('download');
Route::get('/session/reset',         [SubmissionController::class, 'resetSession'])->name('session.reset');

// Authenticated upload-file proxy: lets the form / admin view fetch files
// without exposing them at a guessable public path.
Route::get('/files/{submission}/{key}', [SubmissionController::class, 'serveFile'])
    ->where('key', '[A-Za-z0-9_\-\/\.]+')
    ->name('files.show');

// Admin (gated by env-configured password)
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    Route::get('/',                         [SubmissionController::class, 'index'])->name('index');
    Route::post('/logout',                  [SubmissionController::class, 'adminLogout'])->name('logout');
    Route::get('/export/excel',             [SubmissionController::class, 'exportExcel'])->name('exportExcel');
    Route::get('/{submission}/download',    [SubmissionController::class, 'adminDownload'])->name('download');
    Route::get('/{submission}',             [SubmissionController::class, 'show'])->name('show');
});
