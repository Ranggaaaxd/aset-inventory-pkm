<?php

use App\Http\Controllers\AssetLabelController;
use App\Http\Controllers\AssetBastController;
use App\Http\Controllers\OfficialReportController;
use App\Http\Middleware\RedirectGuestsToFilamentLogin;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::middleware(['web'])->group(function () {
    Route::get('/assets/{asset}/print-label', [AssetLabelController::class, 'printSingle'])->name('assets.print-label');
    Route::get('/assets/print-batch', [AssetLabelController::class, 'printBatch'])->name('assets.print-batch');

    Route::middleware(RedirectGuestsToFilamentLogin::class)->prefix('reports')->name('reports.')->group(function () {
        Route::get('/assets', [OfficialReportController::class, 'assets'])->name('assets');
        Route::get('/stock-mutations', [OfficialReportController::class, 'stockMutations'])->name('stock-mutations');
    });

    Route::middleware(RedirectGuestsToFilamentLogin::class)->get('/assets/{asset}/bast', [AssetBastController::class, 'show'])
        ->name('assets.bast');
});
