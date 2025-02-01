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

        $invoices->each(function ($invoice) {
            InvoiceProductLine::factory()
                ->count(rand(0, 5))
                ->create(['invoice_id' => $invoice->id]);
            // override invoice_id default behaviour from InvoiceProductLineFactory
        });
    }
}
