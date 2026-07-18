<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\SalesOrder;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesOrderApiController extends Controller
{
    private const RELATIONS = [
        'items.productVariant.fragrance',
        'items.productVariant.variantType',
    ];

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['nullable', 'in:CASH,QRIS,TRANSFER'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $orders = SalesOrder::query()
            ->with(self::RELATIONS)
            ->when($filters['q'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                });
            })
            ->when($filters['payment_method'] ?? null, fn ($query, string $method) => $query->where('payment_method', $method))
            ->when($filters['date_from'] ?? null, fn ($query, string $date) => $query->whereDate('order_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, string $date) => $query->whereDate('order_date', '<=', $date))
            ->latest('order_date')
            ->latest('id')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);
        $createdBy = $request->user()?->id ?? 'mobile';

        $order = DB::transaction(function () use ($createdBy, $data): SalesOrder {
            $orderDate = Carbon::parse($data['order_date'] ?? now());
            [$totals, $items] = $this->calculateItems($data['items']);

            $sequence = SalesOrder::whereDate('order_date', $orderDate->toDateString())->lockForUpdate()->count() + 1;
            $order = SalesOrder::create(array_merge($this->headerData($data), $totals, [
                'order_number' => 'SO-'.$orderDate->format('Ymd').'-'.str_pad((string) $sequence, 8, '0', STR_PAD_LEFT),
                'order_date' => $orderDate,
                'created_by' => $createdBy,
            ]));

            $order->items()->createMany($items);

            return $order;
        });

        return response()->json([
            'message' => 'Sales order created successfully.',
            'data' => $order->load(self::RELATIONS),
        ], 201);
    }

    public function show(SalesOrder $salesOrder): JsonResponse
    {
        return response()->json(['data' => $salesOrder->load(self::RELATIONS)]);
    }

    public function update(Request $request, SalesOrder $salesOrder): JsonResponse
    {
        $data = $this->validatedData($request, true);

        DB::transaction(function () use ($data, $salesOrder): void {
            $updates = $this->headerData($data);

            if (array_key_exists('order_date', $data)) {
                $updates['order_date'] = Carbon::parse($data['order_date']);
            }

            if (array_key_exists('items', $data)) {
                [$totals, $items] = $this->calculateItems($data['items']);
                $updates = array_merge($updates, $totals);
                $salesOrder->items()->delete();
                $salesOrder->items()->createMany($items);
            }

            $salesOrder->update($updates);
        });

        return response()->json([
            'message' => 'Sales order updated successfully.',
            'data' => $salesOrder->fresh()->load(self::RELATIONS),
        ]);
    }

    public function destroy(SalesOrder $salesOrder): JsonResponse
    {
        DB::transaction(function () use ($salesOrder): void {
            $salesOrder->items()->delete();
            $salesOrder->delete();
        });

        return response()->json(null, 204);
    }

    private function validatedData(Request $request, bool $partial = false): array
    {
        $presence = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'order_date' => ['sometimes', 'nullable', 'date'],
            'customer_name' => ['sometimes', 'nullable', 'string', 'max:100'],
            'payment_method' => [$presence, 'in:CASH,QRIS,TRANSFER'],
            'notes' => ['sometimes', 'nullable', 'string'],
            'items' => [$presence, 'array', 'min:1'],
            'items.*.product_variant_id' => ['required', 'integer', 'distinct', 'exists:product_variant,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.uom' => ['required', 'in:ML,PCS'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    private function headerData(array $data): array
    {
        return array_filter([
            'customer_name' => $data['customer_name'] ?? null,
            'payment_method' => $data['payment_method'] ?? null,
            'notes' => $data['notes'] ?? null,
        ], fn ($value, string $key) => array_key_exists($key, $data), ARRAY_FILTER_USE_BOTH);
    }

    private function calculateItems(array $inputs): array
    {
        $variants = ProductVariant::whereIn('id', collect($inputs)->pluck('product_variant_id'))
            ->get()
            ->keyBy('id');
        $gross = $discount = $profit = $cost = 0.0;
        $rows = [];

        foreach ($inputs as $index => $input) {
            $quantity = (float) $input['quantity'];
            $unitPrice = (float) $input['unit_price'];
            $lineGross = $quantity * $unitPrice;
            $discountPercent = (float) ($input['discount_percent'] ?? 0);
            $discountAmount = (float) ($input['discount_amount'] ?? 0);

            if ($discountAmount === 0.0 && $discountPercent > 0) {
                $discountAmount = $lineGross * $discountPercent / 100;
            }

            if ($discountAmount > $lineGross) {
                throw ValidationException::withMessages([
                    "items.{$index}.discount_amount" => ['The discount amount may not exceed the line total.'],
                ]);
            }

            $lineTotal = $lineGross - $discountAmount;
            $costOfGoods = (float) ($variants[$input['product_variant_id']]->cost_ml ?? 0) * $quantity;
            $profitAmount = $lineTotal - $costOfGoods;

            $gross += $lineGross;
            $discount += $discountAmount;
            $cost += $costOfGoods;
            $profit += $profitAmount;
            $rows[] = [
                'product_variant_id' => $input['product_variant_id'],
                'quantity' => $quantity,
                'uom' => $input['uom'],
                'unit_price' => $unitPrice,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'line_total' => $lineTotal,
                'cost_of_goods' => $costOfGoods,
                'profit_amount' => $profitAmount,
                'profit_percent' => $costOfGoods > 0 ? $profitAmount / $costOfGoods * 100 : null,
                'is_free' => false,
            ];
        }

        return [[
            'total_before_discount' => $gross,
            'total_discount' => $discount,
            'total_tax' => 0,
            'total_amount' => $gross - $discount,
            'total_profit' => $profit,
            'total_profit_percent' => $cost > 0 ? $profit / $cost * 100 : null,
            'total_cost_of_goods' => $cost,
        ], $rows];
    }
}
