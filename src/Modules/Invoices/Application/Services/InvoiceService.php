<?php

namespace Modules\Invoices\Application\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\Invoices\Application\DTOs\InvoiceData;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Facades\InvoiceFacadeInterface;

final readonly class InvoiceService
{
    public function __construct(private InvoiceFacadeInterface $invoiceFacadeInterface) {}

    public function createInvoice(InvoiceData $dto): Invoice
    {
        return $this->invoiceFacadeInterface->create($dto);
    }

    public function findInvoice(string $id): ?Invoice
    {
        return $this->invoiceFacadeInterface->find($id);
    }

    public function getAllInvoices(): Collection
    {
        return $this->invoiceFacadeInterface->getAll();
    }
}
