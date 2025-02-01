<?php

declare(strict_types=1);

namespace Modules\Invoices\Application\DTOs;

use Modules\Invoices\Application\DTOs\InvoiceProductLineData;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final readonly class InvoiceData
{
    public UuidInterface $id;
    public StatusEnum $status;

    /**
     * @param string $customer_name
     * @param string $customer_email
     * @param InvoiceProductLineData[] $invoiceProductLines
     */
    public function __construct(
        public string $customer_name,
        public string $customer_email,
        public ?array $invoiceProductLines = []
    ) {
        $this->id = Uuid::uuid4();
        $this->status = StatusEnum::Draft;
    }

    public static function fromArray(array $data): self
    {
        $invoiceProductLines = array_map(function ($productLineData) {
            return InvoiceProductLineData::fromArray($productLineData);
        }, $data['invoiceProductLines'] ?? []);

        return new self(
            customer_name: $data['customer_name'],
            customer_email: $data['customer_email'],
            invoiceProductLines: $invoiceProductLines
        );
    }

    public function __toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'status' => $this->status->value,
            'invoiceProductLines' => $this->invoiceProductLines ?? []
        ];
    }
}
