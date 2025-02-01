<?php

declare(strict_types=1);

namespace Modules\Notifications\Api\Dtos;

use Ramsey\Uuid\UuidInterface;

final readonly class NotifyData
{
    public function __construct(
        public UuidInterface $resourceId,
        public string $toEmail,
        public string $subject,
        public string $message,
    ) {
        //
    }
}
