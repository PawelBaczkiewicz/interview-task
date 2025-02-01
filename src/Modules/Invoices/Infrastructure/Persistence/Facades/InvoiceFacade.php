<?php

declare(strict_types=1);

namespace Modules\Invoices\Infrastructure\Persistence\Facades;

use Illuminate\Support\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\Invoices\Application\DTOs\InvoiceData;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Entities\InvoiceProductLine;
use Modules\Invoices\Domain\Enums\StatusEnum;
use Modules\Invoices\Domain\Facades\InvoiceFacadeInterface;

class InvoiceFacade implements InvoiceFacadeInterface
{
    public function create(InvoiceData $data): Invoice
    {
        try {
            return DB::transaction(function () use ($data) {
                $invoice = $this->createWithProductLines($data);
                return $invoice;
            });
        } catch (QueryException $e) {
            throw new \Exception('Database error: ' . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            throw new \Exception('Unexpected error: ' . $e->getMessage(), 0, $e);
        }
    }

    public function find(string $id): ?Invoice
    {
        return Invoice::with('invoiceProductLines')->find($id);
    }

    public function getAll(): Collection
    {
        // optimalization to avoid multiple queries to the database
        // in Invoice::getTotalPriceAttribute method using this total_price attribute
        return Invoice::with('invoiceProductLines')
            ->select(
                'invoices.*',
                DB::raw('(SELECT SUM(unit_price * quantity) FROM invoice_product_lines
                WHERE invoice_product_lines.invoice_id = invoices.id) as total_price')
            )
            ->get();
        //return Invoice::with('invoiceProductLines')->get();
    }
    public function updateStatus(Invoice $invoice, StatusEnum $status)
    {
        $invoice->status = $status;
        $invoice->save();
    }

    protected function createWithProductLines(InvoiceData $data): Invoice
    {
        $invoice = Invoice::create([
            'id' => $data->id,
            'customer_name' => $data->customer_name,
            'customer_email' => $data->customer_email,
            'status' => $data->status,
        ]);

        foreach ($data->invoiceProductLines as $productLineData) {
            InvoiceProductLine::create([
                'id' => $productLineData->id,
                'invoice_id' => $invoice->id,
                'product_name' => $productLineData->product_name,
                'quantity' => $productLineData->quantity,
                'unit_price' => $productLineData->unit_price,
            ]);
        }

        return $invoice;
    }
}
