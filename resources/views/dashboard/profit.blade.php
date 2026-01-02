@extends('layouts.app')

@section('title', 'Profit Dashboard')

@section('content')
    <h1>Profit Dashboard</h1>

    <form method="GET" action="{{ route('dashboard.profit') }}" style="margin: 12px 0 18px;">
        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
            <div class="field">
                <label for="start_date">Start Date</label><br>
                <input type="date" id="start_date" name="start_date" value="{{ $start }}">
            </div>

            <div class="field">
                <label for="end_date">End Date</label><br>
                <input type="date" id="end_date" name="end_date" value="{{ $end }}">
            </div>

            <div>
                <button class="btn btn-primary" type="submit">Show</button>
                <a class="btn btn-secondary" href="{{ route('dashboard.profit') }}">Reset</a>
            </div>
        </div>

        @if ($errors->any())
            <div style="margin-top:10px; color:#b00020;">
                @foreach ($errors->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </div>
        @endif
    </form>

    {{-- Summary --}}
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:12px; margin-bottom:18px;">
        @php
            $totalOrders = (int)($summary->total_orders ?? 0);
            $netSales    = (float)($summary->total_net_sales ?? 0);
            $totalProfit = (float)($summary->total_profit ?? 0);
            $totalHpp    = (float)($summary->total_hpp ?? 0);
            $profitPct   = $summary->total_profit_percent;
        @endphp

        <div style="border:1px solid #ddd; border-radius:10px; padding:12px;">
            <div style="font-size:12px; opacity:.7;">Total Orders</div>
            <div style="font-size:24px; font-weight:700;">{{ number_format($totalOrders) }}</div>
        </div>

        <div style="border:1px solid #ddd; border-radius:10px; padding:12px;">
            <div style="font-size:12px; opacity:.7;">Total Net Sales</div>
            <div style="font-size:24px; font-weight:700;">{{ number_format($netSales, 0) }}</div>
        </div>

        <div style="border:1px solid #ddd; border-radius:10px; padding:12px;">
            <div style="font-size:12px; opacity:.7;">Total HPP</div>
            <div style="font-size:24px; font-weight:700;">{{ number_format($totalHpp, 0) }}</div>
        </div>

        <div style="border:1px solid #ddd; border-radius:10px; padding:12px;">
            <div style="font-size:12px; opacity:.7;">Total Profit</div>
            <div style="font-size:24px; font-weight:700;">{{ number_format($totalProfit, 0) }}</div>
            <div style="font-size:12px; opacity:.7;">
                Profit %:
                {{ $profitPct === null ? '-' : number_format((float)$profitPct, 2) . '%' }}
            </div>
        </div>
    </div>

    {{-- Daily Table --}}
    <h2 style="margin-top:0;">Daily Breakdown</h2>

    @if($daily->isEmpty())
        <p>Tidak ada transaksi pada range tanggal ini.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th style="text-align:right;">Orders</th>
                    <th style="text-align:right;">Net Sales</th>
                    <th style="text-align:right;">HPP</th>
                    <th style="text-align:right;">Profit</th>
                    <th style="text-align:right;">Profit %</th>
                </tr>
            </thead>
            <tbody>
                @foreach($daily as $row)
                    <tr>
                        <td>{{ $row->order_day }}</td>
                        <td style="text-align:right;">{{ number_format((int)$row->total_orders) }}</td>
                        <td style="text-align:right;">{{ number_format((float)$row->total_net_sales, 0) }}</td>
                        <td style="text-align:right;">{{ number_format((float)$row->total_hpp, 0) }}</td>
                        <td style="text-align:right;">{{ number_format((float)$row->total_profit, 0) }}</td>
                        <td style="text-align:right;">
                            {{ $row->profit_percent === null ? '-' : number_format((float)$row->profit_percent, 2) . '%' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
