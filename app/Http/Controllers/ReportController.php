<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function profitDashboard(Request $request)
    {
        // default: 7 hari terakhir
        $start = $request->query('start_date', now()->subDays(6)->toDateString());
        $end   = $request->query('end_date', now()->toDateString());

        // validasi sederhana
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDT = $start . ' 00:00:00';
        $endDT   = $end . ' 23:59:59';

        // Summary
        $summary = DB::table('sales_order')
            ->selectRaw('
                COUNT(*) AS total_orders,
                SUM(net_amount) AS total_net_sales,
                SUM(total_profit) AS total_profit,
                SUM(net_amount - total_profit) AS total_hpp,
                (SUM(total_profit) / NULLIF(SUM(net_amount - total_profit), 0)) * 100 AS total_profit_percent
            ')
            ->whereBetween('order_date', [$startDT, $endDT])
            ->first();

        // Daily breakdown
        $daily = DB::table('sales_order')
            ->selectRaw('
                DATE(order_date) AS order_day,
                COUNT(*) AS total_orders,
                SUM(net_amount) AS total_net_sales,
                SUM(total_profit) AS total_profit,
                SUM(net_amount - total_profit) AS total_hpp,
                (SUM(total_profit) / NULLIF(SUM(net_amount - total_profit), 0)) * 100 AS profit_percent
            ')
            ->whereBetween('order_date', [$startDT, $endDT])
            ->groupByRaw('DATE(order_date)')
            ->orderBy('order_day')
            ->get();

        return view('dashboard.profit', [
            'start'   => $start,
            'end'     => $end,
            'summary' => $summary,
            'daily'   => $daily,
        ]);
    }
}
