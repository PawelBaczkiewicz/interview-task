<?php

declare(strict_types=1);

namespace Modules\Invoices\Application\DTOs;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final readonly class InvoiceProductLineData
{
    public UuidInterface $id;

    public function __construct(
        public string $product_name,
        public int $unit_price,
        public int $quantity
    ) {
        $this->id = Uuid::uuid4();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            product_name: $data['product_name'],
            quantity: (int) $data['quantity'],
            unit_price: (int) $data['unit_price']
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
