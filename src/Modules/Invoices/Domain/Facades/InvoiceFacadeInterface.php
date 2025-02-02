<?php

declare(strict_types=1);

namespace Modules\Invoices\Domain\Facades;

use Illuminate\Support\Collection;
use Modules\Invoices\Application\DTOs\InvoiceData;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Enums\StatusEnum;

interface InvoiceFacadeInterface
{
    public function create(InvoiceData $data): Invoice;
    public function find(string $id): ?Invoice;
    public function getAll(): Collection;
    public function updateStatus(Invoice $invoice, StatusEnum $status);
}
