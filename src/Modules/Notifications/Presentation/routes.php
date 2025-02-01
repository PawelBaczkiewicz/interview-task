<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Notifications\Presentation\Http\NotificationController;
use Ramsey\Uuid\Validator\GenericValidator;

Route::pattern('action', '^[a-zA-Z]+$');
Route::pattern('reference', (new GenericValidator())->getPattern());

Route::get('/notification/hook/{action}/{reference}', [NotificationController::class, 'hook'])->name('notification.hook');
