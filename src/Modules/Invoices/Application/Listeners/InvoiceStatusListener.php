<?php

namespace Modules\Invoices\Application\Listeners;

use Illuminate\Support\Facades\Log;
use Modules\Invoices\Application\Services\InvoiceService;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;

class InvoiceStatusListener
{
    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    public function handle(ResourceDeliveredEvent $event)
    {
        Log::channel('devlogs')->info('InvoiceStatusListener: ResourceDeliveredEvent received:' . $event->resourceId);

        $invoiceId = $event->resourceId;
        $invoice = $this->invoiceService->findInvoice($invoiceId);;
        if ($invoice instanceof Invoice && $invoice->status === StatusEnum::Sending) {
            $this->invoiceService->updateInvoiceStatus($invoice, StatusEnum::SentToClient);
        }
    }
}
