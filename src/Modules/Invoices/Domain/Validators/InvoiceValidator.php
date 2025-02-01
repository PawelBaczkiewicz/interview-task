<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain\Validators;

use Illuminate\Validation\Factory as ValidationFactory;

class InvoiceValidator
{
    public function getMaxQuantity(): int
    {
        return 1000000;
    }

    public function getMaxUnitPrice(): int
    {
        return 10000000;
    }

    public function __construct(private ValidationFactory $validationFactory)
    {
        //
    }

    public function validate(array $data)
    {
        $validator = $this->validationFactory->make($data, [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'invoiceProductLines' => 'array',
            'invoiceProductLines.*.product_name' => 'required|string|max:255',
            'invoiceProductLines.*.unit_price' => 'required|integer|min:1|max:' . $this->getMaxUnitPrice(),
            'invoiceProductLines.*.quantity' => 'required|integer|min:1|max:' . $this->getMaxQuantity(),
        ]);

        return $validator->validated();
    }
}
