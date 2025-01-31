<?php

namespace Modules\Invoices\Application\DTOs;

use Modules\Invoices\Application\DTOs\InvoiceProductLineData;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final readonly class InvoiceData
{
    public function __construct(
        public ?UuidInterface $id = null,
        public string $customer_name,
        public string $customer_email,
        public string $status,
        public ?array $invoiceProductLines = []
    ) {}

    public static function fromArray(array $data): self
    {
        $invoiceProductLines = array_map(function ($productLineData) {
            return InvoiceProductLineData::fromArray($productLineData);
        }, $data['invoiceProductLines'] ?? []);

        return new self(
            id: isset($data['id']) ? Uuid::fromString($data['id']) : null,
            customer_name: $data['customer_name'],
            customer_email: $data['customer_email'],
            status: $data['status'] ?? StatusEnum::Draft->value,
            invoiceProductLines: $invoiceProductLines
        );
    }

    public function __toArray(): array
    {
        return [
            'id' => $this->id?->toString(),
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'status' => $this->status,
            'invoiceProductLines' => $this->invoiceProductLines ?? []
        ];
    }
}
