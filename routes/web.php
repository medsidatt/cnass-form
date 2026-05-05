<?php

use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

// Public form
Route::get('/',        [SubmissionController::class, 'form'])->name('form');
Route::post('/submit', [SubmissionController::class, 'store'])->name('submit');

// Per-submission download (session-protected)
Route::get('/download/{submission}', [SubmissionController::class, 'download'])->name('download');
Route::get('/session/reset',         [SubmissionController::class, 'resetSession'])->name('session.reset');

// Admin (protect with auth middleware in production)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/',             [SubmissionController::class, 'index'])->name('index');
    Route::get('/export/excel', [SubmissionController::class, 'exportExcel'])->name('exportExcel');
    Route::get('/{submission}', [SubmissionController::class, 'show'])->name('show');
});
