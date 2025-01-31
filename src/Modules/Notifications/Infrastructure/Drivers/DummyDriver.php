<?php

declare(strict_types=1);

namespace Modules\Notifications\Infrastructure\Drivers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DummyDriver implements DriverInterface
{
    public function send(
        string $toEmail,
        string $subject,
        string $message,
        string $reference,
    ): bool {

        Log::channel('devlogs')->info('DummyDriver successful, calling webhook to dispatch event for invoice ' . $reference);

        $action = 'delivered';
        $url = route('notification.hook', ['action' => $action, 'reference' => $reference]);

        $response = Http::get($url);

        // HTTP_OK included
        return $response->successful();
    }
}
