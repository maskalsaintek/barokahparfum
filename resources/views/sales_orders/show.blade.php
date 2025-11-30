@extends('layouts.app')

@section('title', 'Sales Order Detail')

@section('content')
    <h1>Sales Order Detail</h1>

    <div class="mb-3">
        <a href="{{ route('sales-orders.index') }}" class="btn btn-sm btn-secondary">&laquo; Back to list</a>
    </div>

    {{-- HEADER INFO --}}
    <div class="card mb-3">
        <div class="card-body">
            <h3>Order {{ $salesOrder->order_number }}</h3>

            <p>
                <strong>Date:</strong>
                {{ optional($salesOrder->order_date)->format('Y-m-d H:i') }}<br>

                <strong>Customer:</strong>
                {{ $salesOrder->customer_name ?? 'Walk-in' }}<br>

                <strong>Payment Method:</strong>
                {{ $salesOrder->payment_method }}<br>

                @if($salesOrder->notes)
                    <strong>Notes:</strong>
                    {{ $salesOrder->notes }}<br>
                @endif

                @if($salesOrder->created_by)
                    <strong>Created By:</strong>
                    {{ $salesOrder->created_by }}
                @endif
            </p>
        </div>
    </div>

    {{-- ITEMS TABLE --}}
    <h3>Items</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Qty</th>
                <th>UoM</th>
                <th class="text-end">Unit Price</th>
                <th class="text-end">Disc %</th>
                <th class="text-end">Disc Rp</th>
                <th class="text-end">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesOrder->items as $item)
                @php
                    $pv = $item->productVariant;
                    $frag = $pv->fragrance ?? null;
                    $vt = $pv->variantType ?? null;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($frag)
                            {{ $frag->code }} - {{ $frag->name }}
                        @else
                            Product #{{ $item->product_variant_id }}
                        @endif

                        @if($vt && $pv->bottle_size_ml)
                            <br><small>{{ $vt->name }} ({{ $pv->bottle_size_ml }} ml)</small>
                        @endif
                    </td>
                    <td>{{ number_format($item->quantity, 2, ',', '.') }}</td>
                    <td>{{ $item->uom }}</td>
                    <td class="text-end">{{ number_format($item->unit_price, 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($item->discount_percent, 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($item->discount_amount, 2, ',', '.') }}</td>
                    <td class="text-end"><strong>{{ number_format($item->line_total, 2, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Tidak ada item.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SUMMARY --}}
    <div style="max-width: 400px; margin-left:auto;">
        <table class="table">
            <tr>
                <th>Subtotal</th>
                <td class="text-end">{{ number_format($salesOrder->total_before_discount, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Discount</th>
                <td class="text-end">{{ number_format($salesOrder->total_discount, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Tax</th>
                <td class="text-end">{{ number_format($salesOrder->total_tax, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Total</th>
                <td class="text-end"><strong>{{ number_format($salesOrder->total_amount, 2, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- kalau mau tombol print / export nanti bisa ditambah di sini --}}
@endsection
