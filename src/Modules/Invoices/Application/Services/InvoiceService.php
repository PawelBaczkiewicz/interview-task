<?php

namespace Modules\Invoices\Application\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Invoices\Application\DTOs\InvoiceData;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Facades\InvoiceFacadeInterface;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Api\NotificationFacadeInterface;
use Ramsey\Uuid\Uuid;

class InvoiceService
{
    public function __construct(
        private InvoiceFacadeInterface $invoiceFacadeInterface,
        private NotificationFacadeInterface $notificationFacade
    ) {}

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

    public function sendInvoiceNotification(Invoice $invoice)
    {
        if ($invoice->status !== StatusEnum::Draft) {
            throw new \Exception('Wrong Invoice status to be sent.');
        }

        if (!$invoice->hasValidProductLines()) {
            throw new \Exception('Invoice must contain valid product lines to be sent.');
        }

        $message =
            "Your invoice is ready.\n" .
            "There are {$invoice->invoiceProductLines->count()} Invoice Product Lines in the invoice.\n" .
            "Total amount: {$invoice->getTotalPrice()}";

        $notifyData = new NotifyData(
            resourceId: Uuid::fromString($invoice->id),
            toEmail: $invoice->customer_email,
            subject: "Invoice Notification: {$invoice->id}",
            message: $message
        );

        Log::channel('devlogs')->info("Sending notification for invoice ID: {$invoice->id}");

        $this->updateInvoiceStatus($invoice, StatusEnum::Sending);

        $this->notificationFacade->notify($notifyData);
    }

    public function updateInvoiceStatus(Invoice $invoice, StatusEnum $status)
    {
        $this->invoiceFacadeInterface->updateStatus($invoice, $status);
    }
}
