<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Ramsey\Uuid\Uuid;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'id' => Uuid::uuid4()->toString(),
            'customer_name' => $this->faker->name,
            'customer_email' => $this->faker->email,
            'status' => StatusEnum::Draft,
        ];
    }
}
