<x-layout>
    <x-slot name="title">Create Invoice</x-slot>

    <form method="POST" action="{{ route('invoices.store') }}" x-data="invoiceForm({{ json_encode(old('invoiceProductLines', [])) }})" class="flex flexColumn">
        @csrf

        <!-- for debugging only (dev) -->
        @if (app()->environment('local'))
            @if ($errors->any())
                <div style="color:red;">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif

        <div>
            <label for="customer_name">Customer Name *</label>
            <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', '') }}" required>
        </div>
        <div>
            <label for="customer_email">Customer E-mail *</label>
            <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email', '') }}" required>
        </div>

        <div x-show="productLines.length > 0">
            Product Lines
        </div>
        <template x-for="(line, index) in productLines" :key="index">
            <div>
                <input type="text" x-bind:name="'invoiceProductLines[' + index + '][product_name]'" x-model="line.productName" placeholder="Product Name" required>
                <input type="number" x-bind:name="'invoiceProductLines[' + index + '][unit_price]'" x-model.number="line.unitPrice" @input="validateAndUpdateTotal(line)" placeholder="Price" required min="1" step="1" :max="{{ $maxUnitPrice }}">
                <input type="number" x-bind:name="'invoiceProductLines[' + index + '][quantity]'" x-model.number="line.quantity" @input="validateAndUpdateTotal(line)" placeholder="Quantity" required min="1" step="1" :max="{{ $maxQuantity }}">
                <input type="number" x-model.number="line.total" placeholder="Total" readonly style="cursor: not-allowed;" @mousedown="preventFocus">
                <button type="button" @click="removeLine(index)" x-show="productLines.length > 0"><i class="fas fa-trash-alt"></i></button>
            </div>
        </template>

        <div>
            <label for="grand_total">Total</label>
            <input type="number" x-model.number="grandTotal" placeholder="Total" readonly style="cursor: not-allowed;" @mousedown="preventFocus">
            <button type="button" @click="addLine()" x-show="productLines.length < 100">Add New Line</button>
        </div>

        <div class="flex flexRow">
            <div>
                <button type="submit">Create Invoice</button>
            </div>
            <div>
                <a href="{{ route('invoices.index') }}" class="btn">Back</a>
             </div>
        </div>
    </form>

    <script>



    function invoiceForm(oldProductLines) {
        return {
            productLines: oldProductLines.length ? oldProductLines.map(line => ({
                productName: line.product_name || '',
                quantity: line.quantity || 1,
                unitPrice: line.unit_price || 1,
            })) : [],
            grandTotal: 0,
            addLine() {
                const line = { productName: '', quantity: 0, unitPrice: 0, total: 0 };
                this.productLines.push(line);
                this.validateAndUpdateTotal(line);
                this.updateGrandTotal();
            },
            removeLine(index) {
                this.productLines.splice(index, 1);
                this.updateGrandTotal();
            },
            validateAndUpdateTotal(line) {
                const maxQuantity = @json($maxQuantity);
                const maxUnitPrice = @json($maxUnitPrice);

                line.quantity = line.quantity.toString().replace(/[^0-9]/g, '');
                line.unitPrice = line.unitPrice.toString().replace(/[^0-9]/g, '');

                line.quantity = parseInt(line.quantity, 10) || 0;
                line.unitPrice = parseInt(line.unitPrice, 10) || 0;

                line.quantity = Math.min(Math.max(0, line.quantity), maxQuantity);
                line.unitPrice = Math.min(Math.max(0, line.unitPrice), maxUnitPrice);

                if (line.quantity < 0) {
                    line.quantity = 0;
                }
                if (line.unitPrice < 0) {
                    line.unitPrice = 0;
                }

                line.total = Math.round(line.quantity * line.unitPrice);
                this.updateGrandTotal();
            },
            updateGrandTotal() {
                this.grandTotal = this.productLines.reduce((sum, line) => sum + line.total, 0);
            },
            preventFocus(event) {
                event.preventDefault();
                event.target.blur();
            }
        };
    }
    </script>






</x-layout>
