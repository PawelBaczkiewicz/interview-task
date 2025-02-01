<?php

declare(strict_types=1);

namespace Modules\Invoices\Presentation\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Invoices\Application\DTOs\InvoiceData;
use Modules\Invoices\Application\Services\InvoiceService;
use Modules\Invoices\Domain\Entities\Invoice;
use Modules\Invoices\Domain\Validators\InvoiceValidator;

class InvoiceController
{
    public function __construct(
        protected InvoiceService $invoiceService,
        protected InvoiceValidator $invoiceValidator
    ) {
        //
    }

    protected function assertInvoice(string $id): Invoice
    {
        $invoice = $this->invoiceService->findInvoice($id);
        if (!$invoice) {
            throw new ModelNotFoundException("Invoice with ID {$id} not found.");
        }
        return $invoice;
    }

    public function index(Request $request): View
    {
        $invoices = $this->invoiceService->getAllInvoices();
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        return view('invoices.create', [
            'maxQuantity' => $this->invoiceValidator->getMaxQuantity(),
            'maxUnitPrice' => $this->invoiceValidator->getMaxUnitPrice(),
        ]);
    }

    public function send(string $id)
    {
        $invoice = $this->assertInvoice($id);

        try {
            $this->invoiceService->sendInvoiceNotification($invoice);
        } catch (\Exception $e) {
            return redirect()->route('invoices.index')->with('flash_error', $e->getMessage());
        };

        return redirect()->route('invoices.index');
    }

    public function store(Request $request)
    {
        $validatedData = $this->invoiceValidator->validate($request->all());
        $invoiceData = InvoiceData::fromArray($validatedData);

        try {
            $invoice = $this->invoiceService->createInvoice($invoiceData);
        } catch (\Exception $e) {
            return redirect()->route('invoices.create')->with('flash_error', $e->getMessage());
        }

        return redirect()->route('invoices.index')->with(
            'flash_success',
            "Invoice with ID: {$invoice->id} created successfully"
        );
    }

    public function show(string $id)
    {
        $invoice = $this->assertInvoice($id);
        return view('invoices.show', compact('invoice'));
    }
}
