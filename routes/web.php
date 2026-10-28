<?php

use App\Http\Controllers\AssetLabelController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');

Route::middleware(['web'])->group(function () {
    Route::get('/assets/{asset}/print-label', [AssetLabelController::class, 'printSingle'])->name('assets.print-label');
    Route::get('/assets/print-batch', [AssetLabelController::class, 'printBatch'])->name('assets.print-batch');
});
