<x-layout>
    <x-slot name="title">Invoices</x-slot>
    <div>

        <a href="{{ route('invoices.create') }}" class="btn">Create Invoice</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Customer E-mail</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->id }}</td>
                        <td>{{ $invoice->customer_name }}</td>
                        <td>{{ $invoice->customer_email }}</td>
                        <td>{{ $invoice->status }}</td>
                        <td>{{ $invoice->getTotalPrice() }}</td>
                        <td>
                            <a href="{{ route('invoices.show', $invoice) }}" class="btn">Show</a>
                            <form action="{{ route('invoices.send', $invoice) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn green">Send</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-layout>
