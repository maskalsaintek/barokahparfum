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

    public function bestSellerFragrances(Request $request)
    {
        // default value (kalau belum diisi)
        $dateFrom = $request->input('date_from', '2026-02-25');
        $dateTo   = $request->input('date_to', '2026-03-31');
        $limit    = (int) $request->input('limit', 10);

        if ($limit <= 0) {
            $limit = 10;
        }

        // FORMAT HARUS SAMA DENGAN QUERY ORIGINAL
        $start = $dateFrom . ' 00:00:00';
        $end   = $dateTo . ' 23:59:59';

        $rows = DB::select(
            "
            SELECT
                f.id AS fragrance_id,
                f.code AS fragrance_code,
                f.name AS fragrance_name,
                SUM(soi.quantity) AS total_ml_sold,
                COUNT(DISTINCT so.id) AS total_orders
            FROM sales_order_item soi
            JOIN sales_order so ON so.id = soi.sales_order_id
            JOIN product_variant pv ON pv.id = soi.product_variant_id
            JOIN fragrance f ON f.id = pv.fragrance_id
            WHERE so.order_date BETWEEN ? AND ?
            GROUP BY f.id, f.code, f.name
            ORDER BY total_ml_sold DESC
            LIMIT {$limit}
            ",
            [$start, $end] // parameter binding
        );

        return view('report.best_seller_fragrances', [
            'rows' => $rows,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'limit' => $limit,
        ]);
    }

    public function totalProfit(Request $request)
    {
        $dateFrom = $request->input('date_from', '2026-03-08');
        $dateTo   = $request->input('date_to', '2026-04-07');

        $result = DB::selectOne(
            "
            SELECT SUM(total_profit) AS total_profit
            FROM sales_order
            WHERE order_date BETWEEN ? AND ?
            ",
            [$dateFrom, $dateTo]
        );

        $totalProfit = $result->total_profit ?? 0;

        return view('report.total_profit', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalProfit' => $totalProfit,
        ]);
    }
}
