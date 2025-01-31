<?php

declare(strict_types=1);

namespace Tests\Feature\Invoice;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Invoices\Application\DTOs\InvoiceData;
use Modules\Invoices\Application\DTOs\InvoiceProductLineData;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Infrastructure\Persistence\Facades\InvoiceFacade;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InvoiceFacadeTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    protected InvoiceFacade $invoiceFacade;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpFaker();
        $this->invoiceFacade = $this->app->make(InvoiceFacade::class);
    }

    #[DataProvider('hookInvoiceProductLinesProvider')]
    public function testCreateInvoice(array $invoiceProductLines): void
    {
        $name = $this->faker->name();
        $email = $this->faker->email();

        $invoiceProductLinesData = array_map(function ($productLineData) {
            return
                new InvoiceProductLineData(
                    product_name: $this->faker->word(),
                    unit_price: $productLineData['unit_price'],
                    quantity: $productLineData['quantity']
                );
        }, $invoiceProductLines);

        $invoiceData = new InvoiceData(
            customer_name: $name,
            customer_email: $email,
            invoiceProductLines: $invoiceProductLinesData
        );

        $invoice = $this->invoiceFacade->create($invoiceData);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertCount(count($invoiceProductLines), $invoice->invoiceProductLines);
        $this->assertEquals($name, $invoice->customer_name);
        $this->assertEquals($email, $invoice->customer_email);
        $this->assertEquals(StatusEnum::Draft, $invoice->status);
    }

    public static function hookInvoiceProductLinesProvider(): array
    {
        return [
            [[['unit_price' => 100, 'quantity' => 2], ['unit_price' => 200, 'quantity' => 1]]],
            [[['unit_price' => 250, 'quantity' => 2]]],
            [[]],
        ];
    }

    public function testFindInvoice(): void
    {
        $invoice = Invoice::factory()->create();

        $foundInvoice = $this->invoiceFacade->find($invoice->id);

        $this->assertInstanceOf(Invoice::class, $foundInvoice);
        $this->assertEquals($invoice->id, $foundInvoice->id);
    }

    public function testGetAllInvoices(): void
    {
        Invoice::factory()->count(3)->create();

        $invoices = $this->invoiceFacade->getAll();

        $this->assertCount(3, $invoices);
    }

    public function testUpdateInvoiceStatus(): void
    {
        $invoice = Invoice::factory()->create(['status' => StatusEnum::Draft]);

        $this->invoiceFacade->updateStatus($invoice, StatusEnum::SentToClient);

        $this->assertEquals(StatusEnum::SentToClient, $invoice->status);
    }
}
