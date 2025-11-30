<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SalesOrderController extends Controller
{
    
    public function create()
    {
        $productVariants = ProductVariant::with(['fragrance', 'variantType'])
            ->where('is_active', true)
            ->orderBy('fragrance_id')
            ->get();

        return view('sales_orders.create', compact('productVariants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_date'           => 'nullable|date',
            'customer_name'        => 'nullable|string|max:100',
            'payment_method'       => 'required|in:CASH,QRIS,TRANSFER',
            'notes'                => 'nullable|string',

            'items'                => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|integer|exists:product_variant,id',
            'items.*.quantity'     => 'required|numeric|min:0.01',
            'items.*.uom'          => 'required|in:ML,PCS',
            'items.*.unit_price'   => 'required|numeric|min:0',
            'items.*.discount_percent' => 'nullable|numeric|min:0',
            'items.*.discount_amount'  => 'nullable|numeric|min:0',
        ]);

        $orderDate = isset($data['order_date'])
            ? \Carbon\Carbon::parse($data['order_date'])
            : now();

        return DB::transaction(function () use ($data, $orderDate) {
            $gross         = 0;
            $totalDiscount = 0;
            $totalProfit = 0;
            $totalHpp    = 0;

            // Hitung nominal per item + siapkan data item lengkap
            $itemRows = [];

            foreach ($data['items'] as $itemInput) {
                $qty        = (float) $itemInput['quantity'];
                $unitPrice  = (float) $itemInput['unit_price'];
                $lineGross  = $qty * $unitPrice;

                $discountPercent = isset($itemInput['discount_percent'])
                    ? (float) $itemInput['discount_percent']
                    : 0.0;

                $discountAmount  = isset($itemInput['discount_amount'])
                    ? (float) $itemInput['discount_amount']
                    : 0.0;

                // Kalau diskon nominal belum diisi tapi persen ada, hitung nominal
                if ($discountAmount == 0 && $discountPercent > 0) {
                    $discountAmount = ($lineGross * $discountPercent) / 100;
                }

                $lineNet = $lineGross - $discountAmount;

                $gross         += $lineGross;
                $totalDiscount += $discountAmount;

                // Ambil variant untuk keperluan free botol + HPP
                $variant = ProductVariant::findOrFail($itemInput['product_variant_id']);

                // --- HPP & Profit ---

                // HPP dasar dari bibit (TODO: ganti ke perhitungan real dari pembelian / recipe)
                $baseCost = $this->getBaseCostForVariant($variant, $qty);

                $costOfGoods   = $baseCost;
                $profitAmount  = $lineNet - $costOfGoods;
                $totalProfit   += $profitAmount;
                $totalHpp      += $costOfGoods;
                $profitPercent = $costOfGoods > 0
                    ? ($profitAmount / $costOfGoods * 100)
                    : null;

                $itemRows[] = [
                    'product_variant_id' => $itemInput['product_variant_id'],
                    'item_type'          => 'FRAGRANCE',
                    'uom'                => $itemInput['uom'],
                    'quantity'           => $qty,
                    'unit_price'         => $unitPrice,
                    'discount_percent'   => $discountPercent,
                    'discount_amount'    => $discountAmount,
                    'line_total'         => $lineNet,
                    'cost_of_goods'      => $costOfGoods,
                    'profit_amount'      => $profitAmount,
                    'profit_percent'     => $profitPercent,
                    'is_free'            => 0
                ];
            }
            
            $net = $gross - $totalDiscount;
            $datePart = $orderDate->format('Ymd');
            // Buat nomor order
            $sequence = (SalesOrder::whereDate('order_date', $orderDate->toDateString())->count() + 1);
            $sequenceStr = str_pad($sequence, 8, '0', STR_PAD_LEFT);

            $orderNumber = "SO-{$datePart}-{$sequenceStr}";
            $totalProfitPercent = $totalHpp > 0
                ? ($totalProfit / $totalHpp * 100)
                : null;

            // Simpan header
            $order = SalesOrder::create([
                'order_number'         => $orderNumber,
                'order_date'           => $data['order_date'] ?? now(),
                'customer_name'        => $data['customer_name'] ?? null,
                'payment_method'       => $data['payment_method'],
                'notes'                => $data['notes'] ?? null,
                'total_before_discount'=> $gross,
                'total_discount'       => $totalDiscount,
                'total_tax'            => 0,
                'total_amount'         => $net,
                'total_profit'         => $totalProfit,
                'total_profit_percent' => $totalProfitPercent,
                'total_cost_of_goods'  => $totalHpp,
                'created_by'           => auth()->id() ?? 'system',
            ]);

            foreach ($itemRows as $row) {
                $row['sales_order_id'] = $order->id;
                SalesOrderItem::create($row);
            }

            return redirect()
                ->route('sales-orders.show', $order->id)
                ->with('success', 'Sales order created successfully.');
        });
    }

    public function index(Request $request)
    {
        $query = SalesOrder::query();

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('customer_name')) {
            $query->where('customer_name', 'like', '%'.$request->customer_name.'%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->date_to);
        }

        $orders = $query
            ->orderBy('order_date', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('sales_orders.index', compact('orders'));
    }

    public function show(SalesOrder $salesOrder)
    {
        // eager load items + product detail
        $salesOrder->load([
            'items.productVariant.fragrance',
            'items.productVariant.variantType',
        ]);

        return view('sales_orders.show', compact('salesOrder'));
    }

    public function destroy(SalesOrder $salesOrder)
    {
        // hapus detail dulu (kalau pakai ON DELETE CASCADE, boleh skip)
        $salesOrder->items()->delete();

        // hapus header
        $salesOrder->delete();

        return redirect()
            ->route('sales-orders.index')
            ->with('success', 'Sales order deleted successfully.');
    }

    protected function getBaseCostForVariant(ProductVariant $variant, float $qty): float
    {
        if ($variant->cost_ml === null) {
            return 0.0; // fallback kalau belum diisi
        }

        return $variant->cost_ml * $qty;
    }
}


