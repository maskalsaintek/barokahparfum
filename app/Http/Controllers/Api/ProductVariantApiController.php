<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductVariantApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'fragrance_id' => ['nullable', 'integer', 'exists:fragrance,id'],
            'fragrance_code' => ['nullable', 'string', 'max:50'],
            'fragrance_name' => ['nullable', 'string', 'max:150'],
            'variant_type_id' => ['nullable', 'integer', 'exists:variant_type,id'],
            'variant_type_code' => ['nullable', 'string', 'max:50'],
            'variant_type_name' => ['nullable', 'string', 'max:100'],
            'bottle_size_ml' => ['nullable', 'numeric', 'min:0.1'],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'cost_ml' => ['nullable', 'numeric', 'min:0'],
            'mix_ratio' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $variants = ProductVariant::query()
            ->with(['fragrance', 'variantType'])
            ->when(isset($filters['fragrance_id']), fn ($query) => $query->where('fragrance_id', $filters['fragrance_id']))
            ->when($filters['fragrance_code'] ?? null, function ($query, string $code): void {
                $query->whereHas('fragrance', fn ($query) => $query->where('code', 'like', "%{$code}%"));
            })
            ->when($filters['fragrance_name'] ?? null, function ($query, string $name): void {
                $query->whereHas('fragrance', fn ($query) => $query->where('name', 'like', "%{$name}%"));
            })
            ->when(isset($filters['variant_type_id']), fn ($query) => $query->where('variant_type_id', $filters['variant_type_id']))
            ->when($filters['variant_type_code'] ?? null, function ($query, string $code): void {
                $query->whereHas('variantType', fn ($query) => $query->where('code', 'like', "%{$code}%"));
            })
            ->when($filters['variant_type_name'] ?? null, function ($query, string $name): void {
                $query->whereHas('variantType', fn ($query) => $query->where('name', 'like', "%{$name}%"));
            })
            ->when(isset($filters['bottle_size_ml']), fn ($query) => $query->where('bottle_size_ml', $filters['bottle_size_ml']))
            ->when(isset($filters['base_price']), fn ($query) => $query->where('base_price', $filters['base_price']))
            ->when(isset($filters['cost_ml']), fn ($query) => $query->where('cost_ml', $filters['cost_ml']))
            ->when($filters['mix_ratio'] ?? null, fn ($query, string $mixRatio) => $query->where('mix_ratio', 'like', "%{$mixRatio}%"))
            ->when(array_key_exists('is_active', $filters), fn ($query) => $query->where('is_active', $filters['is_active']))
            ->orderByCodes()
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();

        return response()->json($variants);
    }

    public function store(Request $request): JsonResponse
    {
        $variant = ProductVariant::create($this->validatedData($request));

        return response()->json([
            'message' => 'Product variant created successfully.',
            'data' => $variant->load(['fragrance', 'variantType']),
        ], 201);
    }

    public function show(ProductVariant $productVariant): JsonResponse
    {
        return response()->json([
            'data' => $productVariant->load(['fragrance', 'variantType']),
        ]);
    }

    public function update(Request $request, ProductVariant $productVariant): JsonResponse
    {
        $productVariant->update($this->validatedData($request, true));

        return response()->json([
            'message' => 'Product variant updated successfully.',
            'data' => $productVariant->fresh()->load(['fragrance', 'variantType']),
        ]);
    }

    public function destroy(ProductVariant $productVariant): JsonResponse
    {
        $productVariant->delete();

        return response()->json(null, 204);
    }

    private function validatedData(Request $request, bool $partial = false): array
    {
        $presence = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'fragrance_id' => [$presence, 'integer', Rule::exists('fragrance', 'id')],
            'variant_type_id' => [$presence, 'integer', Rule::exists('variant_type', 'id')],
            'bottle_size_ml' => ['sometimes', 'nullable', 'numeric', 'min:0.1'],
            'base_price' => [$presence, 'numeric', 'min:0'],
            'cost_ml' => [$presence, 'numeric', 'min:0'],
            'mix_ratio' => ['sometimes', 'nullable', 'string', 'max:50'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
