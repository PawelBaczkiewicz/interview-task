<?php

declare(strict_types=1);

namespace Tests\Unit\Invoice;

use Modules\Invoices\Domain\Entities\InvoiceProductLine;
use PHPUnit\Framework\TestCase;

class InvoiceProductLineTest extends TestCase
{
    public function testGetTotalUnitPrice(): void
    {
        $unitPrice = rand(1, 10000);
        $quantity = rand(1, 10000);

        $invoiceProductLine = $this->getMockBuilder(InvoiceProductLine::class)
            ->onlyMethods(['getAttribute'])
            ->getMock();

        $invoiceProductLine->method('getAttribute')
            ->willReturnMap([
                ['unit_price', $unitPrice],
                ['quantity', $quantity],
            ]);

        $expectedTotalUnitPrice = $unitPrice * $quantity;

        $this->assertEquals($expectedTotalUnitPrice, $invoiceProductLine->getTotalUnitPrice());
    }
}
