<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Invoices\Presentation\Http\Controllers\InvoiceController;
use Ramsey\Uuid\Validator\GenericValidator;

Route::pattern('invoice', (new GenericValidator)->getPattern());

Route::prefix('invoices')
    ->name('invoices.')
    ->group(function () {
        Route::get('/create', [InvoiceController::class, 'create'])->name('create');
        Route::post('/', [InvoiceController::class, 'store'])->name('store');
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
    });
