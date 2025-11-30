@extends('layouts.app')

@section('title', 'Sales Orders')

@section('content')
    <h1>Sales Orders</h1>

    {{-- Flash success --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('sales-orders.index') }}" class="mb-3">
        <div class="field">
            <label for="customer_name">Customer</label>
            <input type="text"
                   name="customer_name"
                   id="customer_name"
                   value="{{ request('customer_name') }}"
                   placeholder="Nama customer">
        </div>

        <div class="field">
            <label for="payment_method">Payment Method</label>
            <select name="payment_method" id="payment_method">
                <option value="">-- semua --</option>
                <option value="CASH" {{ request('payment_method') === 'CASH' ? 'selected' : '' }}>CASH</option>
                <option value="QRIS" {{ request('payment_method') === 'QRIS' ? 'selected' : '' }}>QRIS</option>
                <option value="TRANSFER" {{ request('payment_method') === 'TRANSFER' ? 'selected' : '' }}>TRANSFER</option>
            </select>
        </div>

        <div class="field">
            <label for="date_from">Date From</label>
            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}">
        </div>

        <div class="field">
            <label for="date_to">Date To</label>
            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}">
        </div>

        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        <a href="{{ route('sales-orders.index') }}" class="btn btn-sm btn-secondary">Reset</a>
    </form>

    <div class="mb-3">
        <a href="{{ route('sales-orders.create') }}" class="btn btn-primary">+ New Sales Order</a>
    </div>

    {{-- Tabel List --}}
    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Order No</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Payment</th>
                <th class="text-end">Before Disc</th>
                <th class="text-end">Disc</th>
                <th class="text-end">Tax</th>
                <th class="text-end">Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ optional($order->order_date)->format('Y-m-d H:i') }}</td>
                    <td>{{ $order->customer_name ?? '-' }}</td>
                    <td>{{ $order->payment_method }}</td>
                    <td class="text-end">{{ number_format($order->total_before_discount, 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($order->total_discount, 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($order->total_tax, 2, ',', '.') }}</td>
                    <td class="text-end"><strong>{{ number_format($order->total_amount, 2, ',', '.') }}</strong></td>
                    <td>
                        {{-- View --}}
                        <a href="{{ route('sales-orders.show', $order) }}" 
                            class="btn btn-sm btn-outline-primary">
                                View
                        </a>

                        {{-- Delete --}}
                        <form action="{{ route('sales-orders.destroy', $order) }}" 
                                method="POST" 
                                style="display:inline-block;"
                                onsubmit="return confirm('Delete this order? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10">Belum ada sales order.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div>
        {{ $orders->links() }}
    </div>
@endsection
