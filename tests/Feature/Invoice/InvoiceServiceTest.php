<?php

declare(strict_types=1);

namespace Tests\Feature\Invoice;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Invoices\Application\DTOs\InvoiceData;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Facades\InvoiceFacadeInterface;
use Modules\Notifications\Api\Dtos\NotifyData;
use Modules\Notifications\Api\NotificationFacadeInterface;
use Modules\Invoices\Application\Services\InvoiceService;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use PHPUnit\Framework\Attributes\DataProvider;

class InvoiceServiceTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    protected InvoiceFacadeInterface $invoiceFacade;
    protected NotificationFacadeInterface $notificationFacade;
    protected InvoiceService $invoiceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();

        $this->invoiceFacade = $this->createMock(InvoiceFacadeInterface::class);
        $this->notificationFacade = $this->createMock(NotificationFacadeInterface::class);

        $this->invoiceService = new InvoiceService(
            $this->invoiceFacade,
            $this->notificationFacade
        );
    }

    public function testCreateInvoice(): void
    {
        $invoiceData = new InvoiceData(
            customer_name: $this->faker->name(),
            customer_email: $this->faker->email()
        );

        $invoice = new Invoice();
        $this->invoiceFacade->expects($this->once())
            ->method('create')
            ->with($invoiceData)
            ->willReturn($invoice);

        $result = $this->invoiceService->createInvoice($invoiceData);

        $this->assertInstanceOf(Invoice::class, $result);
    }

    public function testFindInvoice(): void
    {
        $invoice = new Invoice();
        $invoiceId = Uuid::uuid4()->toString();

        $this->invoiceFacade->expects($this->once())
            ->method('find')
            ->with($invoiceId)
            ->willReturn($invoice);

        $result = $this->invoiceService->findInvoice($invoiceId);

        $this->assertInstanceOf(Invoice::class, $result);
    }

    public function testGetAllInvoices(): void
    {
        $invoices = new EloquentCollection([new Invoice(), new Invoice()]);

        $this->invoiceFacade->expects($this->once())
            ->method('getAll')
            ->willReturn($invoices);

        $result = $this->invoiceService->getAllInvoices();

        $this->assertCount(2, $result);
    }

    public function testSendInvoiceNotificationThrowsExceptionForInvalidStatus(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Wrong Invoice status to be sent.');

        $invoice = $this->createPartialMock(Invoice::class, ['getAttribute']);
        $invoice->method('getAttribute')->willReturnMap([
            ['status', StatusEnum::Sending],
        ]);

        $this->invoiceService->sendInvoiceNotification($invoice);
    }

    public function testSendInvoiceNotificationThrowsExceptionForInvalidProductLines(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invoice must contain valid product lines to be sent.');

        $invoice = $this->createPartialMock(Invoice::class, ['getAttribute', 'hasValidProductLines']);
        $invoice->method('getAttribute')->willReturnMap([
            ['status', StatusEnum::Draft],
        ]);

        $invoice->expects($this->once())
            ->method('hasValidProductLines')
            ->willReturn(false);

        $this->invoiceService->sendInvoiceNotification($invoice);
    }
}
