<x-layout>
    <x-slot name="title">Invoice ID: {{ $invoice->id }}</x-slot>
    <div>
        @if($invoice->invoiceProductLines->isEmpty())
            <h3>Invoice has no Invoice Product Lines</h3>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Unit Price</th>
                        <th>Quantity</th>
                        <th>Total Unit Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->invoiceProductLines as $invoiceProductLine)
                        <tr>
                            <td>{{ $invoiceProductLine->product_name }}</td>
                            <td>{{ $invoiceProductLine->unit_price }}</td>
                            <td>{{ $invoiceProductLine->quantity }}</td>
                            <td>{{ $invoiceProductLine->getTotalUnitPrice() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        <div>
            <h3>Total Price: {{ $invoice->getTotalPrice() }}</h3>
        </div>
        @endif
        <div>
            <a href="{{ route('invoices.index') }}" class="btn">Back</a>
        </div>
    </div>
</x-layout>
