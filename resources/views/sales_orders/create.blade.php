@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp

@extends('layouts.app')

@section('title', 'Create Sales Order')

@section('content')
    <h1>Create Sales Order</h1>

    {{-- Global error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi error:</strong>
            <ul>
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sales-orders.store') }}" method="POST" id="salesOrderForm">
        @csrf

        {{-- HEADER --}}
        <div class="card">
            <div class="card-body">
                <div class="field">
                    <label for="customer_name">Customer Name</label>
                    <input type="text"
                           name="customer_name"
                           id="customer_name"
                           value="{{ old('customer_name') }}"
                           placeholder="Walk-in">
                    @error('customer_name')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="order_date">Order Date &amp; Time</label>
                    <input
                        type="datetime-local"
                        name="order_date"
                        id="order_date"
                        value="{{ old('order_date', now()->format('Y-m-d\TH:i')) }}"
                    >
                    @error('order_date')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="payment_method">Payment Method</label>
                    <select name="payment_method" id="payment_method">
                        @php
                            $pm = old('payment_method', 'CASH');
                        @endphp
                        <option value="CASH" {{ $pm === 'CASH' ? 'selected' : '' }}>CASH</option>
                        <option value="QRIS" {{ $pm === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                        <option value="TRANSFER" {{ $pm === 'TRANSFER' ? 'selected' : '' }}>TRANSFER</option>
                    </select>
                    @error('payment_method')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" rows="2">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <hr>

        {{-- DETAIL ITEMS --}}
        <h3>Items</h3>
        @error('items')
            <div class="error">{{ $message }}</div>
        @enderror
        @error('items.*.product_variant_id')
            <div class="error">{{ $message }}</div>
        @enderror

        <table class="table" id="itemsTable">
            <thead>
                <tr>
                    <th style="width: 30%;">Product</th>
                    <th style="width: 10%;">Qty</th>
                    <th style="width: 10%;">UoM</th>
                    <th style="width: 15%;">Unit Price</th>
                    <th style="width: 10%;">Disc %</th>
                    <th style="width: 10%;">Disc Rp</th>
                    <th style="width: 15%;">Line Total</th>
                    <th style="width: 5%;"></th>
                </tr>
            </thead>
            <tbody>
                {{-- initial row --}}
                <tr class="item-row">
                    <td>
                        <select name="items[0][product_variant_id]" class="product-select">
                            <option value="">-- pilih product --</option>
                            @foreach($productVariants as $pv)
                                <option value="{{ $pv->id }}"
                                        data-unit-price="{{ $pv->base_price ?? 0 }}">
                                    {{ $pv->fragrance->code ?? '' }} - {{ $pv->fragrance->name ?? '' }}
                                    @if($pv->bottle_size_ml)
                                        ({{ $pv->bottle_size_ml }} ml)
                                    @endif
                                     - Rp {{ number_format($pv->base_price ?? 0, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0.01"
                               name="items[0][quantity]"
                               class="qty-input"
                               value="1">
                    </td>
                    <td>
                        <select name="items[0][uom]" class="uom-select">
                            <option value="ML">ML</option>
                            <option value="PCS">PCS</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0"
                               name="items[0][unit_price]"
                               class="unit-price-input"
                               value="0">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0"
                               name="items[0][discount_percent]"
                               class="disc-percent-input"
                               value="0">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0"
                               name="items[0][discount_amount]"
                               class="disc-amount-input"
                               value="0">
                    </td>
                    <td>
                        <input type="number" step="0.01" min="0"
                               class="line-total-input"
                               value="0"
                               readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger btn-remove-row">&times;</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <button type="button" class="btn btn-sm btn-secondary" id="btnAddRow">+ Add Item</button>

        <hr>

        {{-- TOTAL SUMMARY (display only, dihitung dari JS, server tetap hitung ulang di controller) --}}
        <div style="max-width: 400px; margin-left:auto;">
            <div class="field">
                <label>Subtotal</label>
                <input type="number" step="0.01" id="subtotal_display" readonly>
            </div>
            <div class="field">
                <label>Total Discount</label>
                <input type="number" step="0.01" id="total_discount_display" readonly>
            </div>
            <div class="field">
                <label>Total Amount</label>
                <input type="number" step="0.01" id="total_amount_display" readonly>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Order</button>
        <a href="#" onclick="history.back();" class="btn btn-secondary">Cancel</a>
    </form>

    <script>
        (function () {
            let rowIndex = 1; // karena row pertama index 0

            const itemsTableBody = document.querySelector('#itemsTable tbody');
            const btnAddRow = document.getElementById('btnAddRow');

            function updateDiscountPercentFromAmount(row) {
                const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                const discAmountInput = row.querySelector('.disc-amount-input');
                const discPercentInput = row.querySelector('.disc-percent-input');

                const discAmount = parseFloat(discAmountInput.value) || 0;
                const base = qty * price;

                if (base > 0 && discAmount > 0) {
                    const percent = (discAmount / base) * 100;
                    discPercentInput.value = percent.toFixed(2);
                } else {
                    discPercentInput.value = 0;
                }
            }

            function recalcRow(row) {
                const qtyInput = row.querySelector('.qty-input');
                const priceInput = row.querySelector('.unit-price-input');
                const discPercentInput = row.querySelector('.disc-percent-input');
                const discAmountInput = row.querySelector('.disc-amount-input');
                const totalInput = row.querySelector('.line-total-input');

                const qty = parseFloat(qtyInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const discPercent = parseFloat(discPercentInput.value) || 0;
                const discAmount = parseFloat(discAmountInput.value) || 0;

                const base = qty * price;
                const discFromPercent = base * (discPercent / 100);
                const totalDisc = discAmount;
                const lineTotal = Math.max(base - totalDisc, 0);

                totalInput.value = lineTotal.toFixed(2);

                recalcSummary();
            }

            function recalcSummary() {
                let subtotal = 0;
                let totalDiscount = 0;

                document.querySelectorAll('#itemsTable tbody tr.item-row').forEach(function (row) {
                    const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
                    const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                    const discPercent = parseFloat(row.querySelector('.disc-percent-input').value) || 0;
                    const discAmount = parseFloat(row.querySelector('.disc-amount-input').value) || 0;
                    const base = qty * price;
                    const discFromPercent = base * (discPercent / 100);
                    subtotal += base;
                    totalDiscount += discAmount;
                });

                const total = subtotal - totalDiscount;

                document.getElementById('subtotal_display').value = subtotal.toFixed(2);
                document.getElementById('total_discount_display').value = totalDiscount.toFixed(2);
                document.getElementById('total_amount_display').value = total.toFixed(2);
            }

            function onProductChange(e) {
                const select = e.target;
                const row = select.closest('tr');
                const selected = select.options[select.selectedIndex];
                const unitPrice = parseFloat(selected.getAttribute('data-unit-price')) || 0;
                const priceInput = row.querySelector('.unit-price-input');
                priceInput.value = unitPrice.toFixed(2);
                recalcRow(row);
            }

            function attachRowEvents(row) {
                const productSelect = row.querySelector('.product-select');
                const qtyInput = row.querySelector('.qty-input');
                const priceInput = row.querySelector('.unit-price-input');
                const discPercentInput = row.querySelector('.disc-percent-input');
                const discAmountInput = row.querySelector('.disc-amount-input');
                const btnRemove = row.querySelector('.btn-remove-row');

                if (productSelect) {
                    productSelect.addEventListener('change', onProductChange);
                }
                [qtyInput, priceInput, discPercentInput, discAmountInput].forEach(function (input) {
                    if (productSelect) {
                        productSelect.addEventListener('change', onProductChange);
                    }

                    if (qtyInput) {
                        qtyInput.addEventListener('input', function () {
                            recalcRow(row);
                        });
                    }

                    if (priceInput) {
                        priceInput.addEventListener('input', function () {
                            recalcRow(row);
                        });
                    }

                    if (discAmountInput) {
                        discAmountInput.addEventListener('input', function () {
                            // DI SINI BEDANYA:
                            updateDiscountPercentFromAmount(row);
                            recalcRow(row);
                        });
                    }
                });

                if (btnRemove) {
                    btnRemove.addEventListener('click', function () {
                        const allRows = document.querySelectorAll('#itemsTable tbody tr.item-row');
                        if (allRows.length > 1) {
                            row.remove();
                            recalcSummary();
                        }
                    });
                }
            }

            // Attach events to initial row
            document.querySelectorAll('#itemsTable tbody tr.item-row').forEach(attachRowEvents);

            btnAddRow.addEventListener('click', function () {
                const lastRow = document.querySelector('#itemsTable tbody tr.item-row:last-child');
                const newRow = lastRow.cloneNode(true);

                // Update name attributes dengan index baru
                newRow.querySelectorAll('select, input').forEach(function (el) {
                    const name = el.getAttribute('name');
                    if (name) {
                        const newName = name.replace(/\[\d+\]/, '[' + rowIndex + ']');
                        el.setAttribute('name', newName);
                    }

                    if (el.classList.contains('product-select')) {
                        el.value = '';
                    } else if (el.classList.contains('uom-select')) {
                        el.value = 'ML';
                    } else if (el.classList.contains('qty-input')) {
                        el.value = '1';
                    } else if (el.classList.contains('unit-price-input') ||
                               el.classList.contains('disc-percent-input') ||
                               el.classList.contains('disc-amount-input') ||
                               el.classList.contains('line-total-input')) {
                        el.value = '0';
                    }
                });

                itemsTableBody.appendChild(newRow);
                attachRowEvents(newRow);
                rowIndex++;
            });
        })();
    </script>
@endsection
