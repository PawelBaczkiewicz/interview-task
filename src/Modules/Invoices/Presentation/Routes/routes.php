<?php

declare(strict_types=1);

require __DIR__ . '/invoices.php';

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('invoices.index');
})->name('home');
