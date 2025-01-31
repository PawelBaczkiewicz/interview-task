<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Invoices\Infrastructure\Persistence\Seeders\InvoiceSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(InvoiceSeeder::class);
    }
}
