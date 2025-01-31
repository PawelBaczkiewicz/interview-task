<?php

namespace Modules\Invoices\Application\DTOs;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final readonly class InvoiceProductLineData
{
    public function __construct(
        public ?UuidInterface $id,
        public string $product_name,
        public int $unit_price,
        public int $quantity
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: isset($data['id']) ? Uuid::fromString($data['id']) : null,
            product_name: $data['product_name'],
            quantity: $data['quantity'],
            unit_price: $data['unit_price']
        );
    }

    public function __toArray(): array
    {
        return [
            'id' => $this->id?->toString(),
            'product_name' => $this->product_name,
            'unit_price' => $this->unit_price,
            'quantity' => $this->quantity
        ];
    }
}
