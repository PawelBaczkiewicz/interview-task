<?php

namespace Modules\Invoices\Infrastructure\Persistence\Facades;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\Invoices\Application\DTOs\InvoiceData;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\InvoiceProductLine;
use Modules\Invoices\Domain\Facades\InvoiceFacadeInterface;

class InvoiceFacade implements InvoiceFacadeInterface
{
    protected function createInvoiceAndProductLines(InvoiceData $data): Invoice
    {
        $invoice = Invoice::create([
            'customer_name' => $data->customer_name,
            'customer_email' => $data->customer_email,
            'status' => $data->status,
        ]);

        foreach ($data->invoiceProductLines as $productLineData) {
            $invoiceProductLine = new InvoiceProductLine([
                'invoice_id' => $invoice->id,
                'product_name' => $productLineData->product_name,
                'quantity' => $productLineData->quantity,
                'unit_price' => $productLineData->unit_price,
            ]);
            $invoiceProductLine->save();
        }

        return $invoice;
    }


    public function create(InvoiceData $data): Invoice
    {
        try {
            return DB::transaction(function () use ($data) {
                $invoice = $this->createInvoiceAndProductLines($data);
                return $invoice;
            });
        } catch (QueryException $e) {
            throw new \RuntimeException('Database error: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            throw new \RuntimeException('Unexpected error: ' . $e->getMessage(), 0, $e);
        }
    }

    public function find(string $id): ?Invoice
    {
        return Invoice::with('invoiceProductLines')->find($id);
    }

    public function getAll(): Collection
    {
        return Invoice::with('invoiceProductLines')->get();
    }
}
