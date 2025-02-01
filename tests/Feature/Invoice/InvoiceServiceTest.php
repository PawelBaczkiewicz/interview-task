<?php

declare(strict_types=1);

namespace Tests\Feature\Invoice;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Invoices\Application\DTOs\InvoiceData;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Facades\InvoiceFacadeInterface;
use Modules\Notifications\Api\NotificationFacadeInterface;
use Modules\Invoices\Application\Services\InvoiceService;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Collection;

class InvoiceServiceTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
    }

    public function testCreateInvoice(): void
    {
        $invoiceFacade = $this->createMock(InvoiceFacadeInterface::class);
        $notificationFacade = $this->createMock(NotificationFacadeInterface::class);
        $invoiceService = new InvoiceService(
            $invoiceFacade,
            $notificationFacade
        );

        $invoiceData = new InvoiceData(
            customer_name: $this->faker->name(),
            customer_email: $this->faker->email()
        );

        $invoice = new Invoice();

        $invoiceFacade->expects($this->once())
            ->method('create')
            ->with($invoiceData)
            ->willReturn($invoice);


        $result = $invoiceService->createInvoice($invoiceData);

        $this->assertInstanceOf(Invoice::class, $result);
    }

    public function testFindInvoice(): void
    {
        $invoiceFacade = $this->createMock(InvoiceFacadeInterface::class);
        $notificationFacade = $this->createMock(NotificationFacadeInterface::class);
        $invoiceService = new InvoiceService(
            $invoiceFacade,
            $notificationFacade
        );

        $invoice = new Invoice();
        $invoiceId = Uuid::uuid4()->toString();

        $invoiceFacade->expects($this->once())
            ->method('find')
            ->with($invoiceId)
            ->willReturn($invoice);

        $result = $invoiceService->findInvoice($invoiceId);

        $this->assertInstanceOf(Invoice::class, $result);
    }

    public function testGetAllInvoices(): void
    {
        $invoiceFacade = $this->createMock(InvoiceFacadeInterface::class);
        $notificationFacade = $this->createMock(NotificationFacadeInterface::class);
        $invoiceService = new InvoiceService(
            $invoiceFacade,
            $notificationFacade
        );

        $invoices = new Collection([new Invoice(), new Invoice()]);

        $invoiceFacade->expects($this->once())
            ->method('getAll')
            ->willReturn($invoices);

        $result = $invoiceService->getAllInvoices();

        $this->assertCount(2, $result);
    }

    public function testSendInvoiceNotificationThrowsExceptionForInvalidStatus(): void
    {
        $invoiceFacade = $this->createMock(InvoiceFacadeInterface::class);
        $notificationFacade = $this->createMock(NotificationFacadeInterface::class);
        $invoiceService = new InvoiceService(
            $invoiceFacade,
            $notificationFacade
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Wrong Invoice status to be sent.');

        $invoice = $this->createPartialMock(Invoice::class, ['getAttribute']);
        $invoice->method('getAttribute')->willReturnMap([
            ['status', StatusEnum::Sending],
        ]);

        $invoiceService->sendInvoiceNotification($invoice);
    }

    public function testSendInvoiceNotificationThrowsExceptionForInvalidProductLines(): void
    {
        $invoiceFacade = $this->createMock(InvoiceFacadeInterface::class);
        $notificationFacade = $this->createMock(NotificationFacadeInterface::class);
        $invoiceService = new InvoiceService(
            $invoiceFacade,
            $notificationFacade
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invoice must contain valid product lines to be sent.');

        $invoice = $this->createPartialMock(Invoice::class, ['getAttribute', 'hasValidProductLines']);
        $invoice->method('getAttribute')->willReturnMap([
            ['status', StatusEnum::Draft],
        ]);

        $invoice->expects($this->once())
            ->method('hasValidProductLines')
            ->willReturn(false);

        $invoiceService->sendInvoiceNotification($invoice);
    }
}
