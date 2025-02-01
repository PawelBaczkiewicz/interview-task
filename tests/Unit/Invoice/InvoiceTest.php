<?php

declare(strict_types=1);

namespace Tests\Unit\Invoice;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\InvoiceProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use PHPUnit\Framework\TestCase;

class InvoiceTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    protected function setUp(): void
    {
        $this->setUpFaker();
    }

    public function testInvoiceTotalPrice(): void
    {
        $randomNumbers = array_map(fn() => rand(1, 1000), range(1, rand(3, 10)));
        $sumValues = array_sum($randomNumbers);

        $invoiceProductLines = [];
        foreach ($randomNumbers as $randomNumber) {
            $invoiceProductLine = $this->createMock(InvoiceProductLine::class);
            $invoiceProductLine->method('getTotalUnitPrice')->willReturn($randomNumber);
            $invoiceProductLines[] = $invoiceProductLine;
        }

        $hasManyMock = $this->createMock(HasMany::class);
        $hasManyMock->method('get')->willReturn(new Collection($invoiceProductLines));

        $invoice = $this->getMockBuilder(Invoice::class)
            ->setConstructorArgs([[
                'customer_name' => $this->faker->name(),
                'customer_email' => $this->faker->email(),
                'status' => StatusEnum::Draft,
            ]])
            ->onlyMethods(['invoiceProductLines'])
            ->getMock();

        $invoice->method('invoiceProductLines')->willReturn($hasManyMock);

        $this->assertEquals($sumValues, $invoice->getTotalPrice());
    }

    public function testHasValidProductLinesWithEmptyLines(): void
    {
        $invoice = $this->getMockBuilder(Invoice::class)
            ->setConstructorArgs([[
                'customer_name' => $this->faker->name(),
                'customer_email' => $this->faker->email(),
                'status' => StatusEnum::Draft,
            ]])
            ->onlyMethods(['invoiceProductLines'])
            ->getMock();

        $hasManyMock = $this->createMock(HasMany::class);
        $hasManyMock->method('get')->willReturn(new Collection());

        $invoice->method('invoiceProductLines')->willReturn($hasManyMock);

        $this->assertFalse($invoice->hasValidProductLines());
    }

    public function testHasValidProductLinesWithInvalidLines(): void
    {
        $invoiceProductLine = $this->createMock(InvoiceProductLine::class);
        $invoiceProductLine->method('getTotalUnitPrice')->willReturn(0);

        $hasManyMock = $this->createMock(HasMany::class);
        $hasManyMock->method('get')->willReturn(new Collection([$invoiceProductLine]));

        $invoice = $this->getMockBuilder(Invoice::class)
            ->setConstructorArgs([[
                'customer_name' => $this->faker->name(),
                'customer_email' => $this->faker->email(),
                'status' => StatusEnum::Draft,
            ]])
            ->onlyMethods(['invoiceProductLines'])
            ->getMock();

        $invoice->method('invoiceProductLines')->willReturn($hasManyMock);

        $this->assertFalse($invoice->hasValidProductLines());
    }

    public function testHasValidProductLinesWithValidLines(): void
    {
        $invoiceProductLine = $this->createMock(InvoiceProductLine::class);
        $invoiceProductLine->method('getTotalUnitPrice')->willReturn(100);

        $hasManyMock = $this->createMock(HasMany::class);
        $hasManyMock->method('get')->willReturn(new Collection([$invoiceProductLine]));

        $invoice = $this->getMockBuilder(Invoice::class)
            ->setConstructorArgs([[
                'customer_name' => $this->faker->name(),
                'customer_email' => $this->faker->email(),
                'status' => StatusEnum::Draft,
            ]])
            ->onlyMethods(['invoiceProductLines'])
            ->getMock();

        $invoice->method('invoiceProductLines')->willReturn($hasManyMock);

        $this->assertTrue($invoice->hasValidProductLines());
    }
}
