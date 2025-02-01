<?php

declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Persistence\Seeders;

use Illuminate\Database\Seeder;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\InvoiceProductLine;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $invoices = Invoice::factory()
            ->count(30)
            ->create();

        foreach ($invoices as $invoice) {
            InvoiceProductLine::factory()
                ->count(rand(0, 5))
                ->create(['invoice_id' => $invoice->getKey()]);
            // override invoice_id default behaviour from InvoiceProductLineFactory
        }
    }
}
