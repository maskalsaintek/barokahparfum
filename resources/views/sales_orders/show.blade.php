@extends('layouts.app')

@section('title', 'Detail Sales Order')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted mb-1">Sales Order</p>
            <h2 class="mb-0">{{ $salesOrder->order_number }}</h2>
        </div>
        <a href="{{ route('sales-orders.index') }}" class="btn btn-outline-secondary mt-2 mt-sm-0">
            <i class="fa fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Item Pesanan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="pl-4">#</th>
                                    <th>Produk</th>
                                    <th class="text-right">Harga</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-right">Diskon</th>
                                    <th class="text-right pr-4">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($salesOrder->items as $item)
                                    @php
                                        $variant = $item->productVariant;
                                        $fragrance = optional($variant)->fragrance;
                                        $variantType = optional($variant)->variantType;
                                    @endphp
                                    <tr>
                                        <td class="pl-4">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>
                                                {{ $fragrance
                                                    ? trim($fragrance->code.' - '.$fragrance->name, ' -')
                                                    : 'Produk #'.$item->product_variant_id }}
                                            </strong>
                                            @if ($variantType || optional($variant)->bottle_size_ml)
                                                <div class="small text-muted mt-1">
                                                    {{ optional($variantType)->name ?? 'Varian' }}
                                                    @if (optional($variant)->bottle_size_ml)
                                                        &middot; {{ number_format($variant->bottle_size_ml, 0, ',', '.') }} ml
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-right text-nowrap">Rp {{ number_format((float) $item->unit_price, 0, ',', '.') }}</td>
                                        <td class="text-center text-nowrap">
                                            {{ number_format((float) $item->quantity, 2, ',', '.') }} {{ $item->uom }}
                                        </td>
                                        <td class="text-right text-nowrap">
                                            @if ((float) $item->discount_amount > 0)
                                                Rp {{ number_format((float) $item->discount_amount, 0, ',', '.') }}
                                                @if ((float) $item->discount_percent > 0)
                                                    <div class="small text-muted">{{ number_format((float) $item->discount_percent, 2, ',', '.') }}%</div>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="text-right text-nowrap pr-4">
                                            <strong>Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">Tidak ada item pada pesanan ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informasi Pesanan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="small text-muted">Tanggal</div>
                        <strong>{{ optional($salesOrder->order_date)->format('d M Y, H:i') ?? '-' }}</strong>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Pelanggan</div>
                        <strong>{{ $salesOrder->customer_name ?: 'Pelanggan umum' }}</strong>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Metode Pembayaran</div>
                        <span class="badge badge-primary">{{ $salesOrder->payment_method }}</span>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Dibuat Oleh</div>
                        <strong>{{ $salesOrder->created_by ?: '-' }}</strong>
                    </div>
                    @if ($salesOrder->notes)
                        <div>
                            <div class="small text-muted">Catatan</div>
                            <p class="mb-0">{{ $salesOrder->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Ringkasan Pembayaran</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span>Rp {{ number_format((float) $salesOrder->total_before_discount, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Diskon</span>
                        <span>- Rp {{ number_format((float) $salesOrder->total_discount, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Pajak</span>
                        <span>Rp {{ number_format((float) $salesOrder->total_tax, 0, ',', '.') }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Total</strong>
                        <h4 class="text-primary mb-0">Rp {{ number_format((float) $salesOrder->total_amount, 0, ',', '.') }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
