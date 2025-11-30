(function () {
    let rowIndex = 1; // karena row pertama index 0

    const itemsTableBody = document.querySelector('#itemsTable tbody');
    const btnAddRow = document.getElementById('btnAddRow');

    if (!itemsTableBody || !btnAddRow) {
        return;
    }

    function updateDiscountAmountFromPercent(row) {
        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const price = parseFloat(row.querySelector('.unit-price-input').value) || 0;
        const discPercentInput = row.querySelector('.disc-percent-input');
        const discAmountInput = row.querySelector('.disc-amount-input');

        const discPercent = parseFloat(discPercentInput.value) || 0;
        const base = qty * price;

        if (base > 0 && discPercent > 0) {
            const discAmount = base * (discPercent / 100);
            discAmountInput.value = discAmount.toFixed(2);
        } else {
            discAmountInput.value = 0;
        }
    }

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
        const totalDisc = discFromPercent + discAmount;
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
            totalDiscount += discFromPercent + discAmount;
        });

        const total = subtotal - totalDiscount;

        const subEl = document.getElementById('subtotal_display');
        const discEl = document.getElementById('total_discount_display');
        const totalEl = document.getElementById('total_amount_display');

        if (subEl) subEl.value = subtotal.toFixed(2);
        if (discEl) discEl.value = totalDiscount.toFixed(2);
        if (totalEl) totalEl.value = total.toFixed(2);
    }

    function onProductChange(e) {
        const select = e.target;
        const row = select.closest('tr');
        const selected = select.options[select.selectedIndex];
        const unitPrice = parseFloat(selected.getAttribute('data-unit-price')) || 0;
        const priceInput = row.querySelector('.unit-price-input');
        priceInput.value = unitPrice.toFixed(2);

        // kalau ganti produk, hitung ulang diskon (dari percent)
        updateDiscountAmountFromPercent(row);
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

        if (qtyInput) {
            qtyInput.addEventListener('input', function () {
                // qty berubah → hitung ulang disc_amount dari percent
                updateDiscountAmountFromPercent(row);
                recalcRow(row);
            });
        }

        if (priceInput) {
            priceInput.addEventListener('input', function () {
                // unit_price berubah → hitung ulang disc_amount dari percent
                updateDiscountAmountFromPercent(row);
                recalcRow(row);
            });
        }

        if (discPercentInput) {
            discPercentInput.addEventListener('input', function () {
                // disc % berubah → hitung ulang disc_amount dari percent
                updateDiscountAmountFromPercent(row);
                recalcRow(row);
            });
        }

        if (discAmountInput) {
            discAmountInput.addEventListener('input', function () {
                // disc Rp berubah → hitung ulang disc_percent dari amount
                updateDiscountPercentFromAmount(row);
                recalcRow(row);
            });
        }

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

    // Add row button
    btnAddRow.addEventListener('click', function () {
        const lastRow = document.querySelector('#itemsTable tbody tr.item-row:last-child');
        const newRow = lastRow.cloneNode(true);

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

        recalcSummary();
    });
})();
