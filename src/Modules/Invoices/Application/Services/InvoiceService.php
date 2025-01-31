<?php

namespace Modules\Invoices\Application\Services;

use Illuminate\Database\Eloquent\Collection;
use Modules\Invoices\Application\DTOs\InvoiceData;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Facades\InvoiceFacadeInterface;
use Modules\Notifications\Application\Facades\NotificationFacade;
use Modules\Notifications\Api\Dtos\NotifyData;
use Ramsey\Uuid\Uuid;

class InvoiceService
{
    public function __construct(
        private InvoiceFacadeInterface $invoiceFacadeInterface,
        private NotificationFacade $notificationFacade
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

        $this->notificationFacade->notify($notifyData);
        $this->updateInvoiceStatus($invoice, StatusEnum::Sending);
    }

    public function updateInvoiceStatus(Invoice $invoice, StatusEnum $status)
    {
        $this->invoiceFacadeInterface->updateStatus($invoice, $status);
    }
}
