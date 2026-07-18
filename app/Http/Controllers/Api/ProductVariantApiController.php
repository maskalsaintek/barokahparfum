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
            'q' => ['nullable', 'string', 'max:100'],
            'fragrance_id' => ['nullable', 'integer', 'exists:fragrance,id'],
            'variant_type_id' => ['nullable', 'integer', 'exists:variant_type,id'],
            'is_active' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $variants = ProductVariant::query()
            ->with(['fragrance', 'variantType'])
            ->when($filters['q'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->whereHas('fragrance', function ($query) use ($search): void {
                        $query->where(function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                    })->orWhereHas('variantType', function ($query) use ($search): void {
                        $query->where(function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                    });
                });
            })
            ->when(isset($filters['fragrance_id']), fn ($query) => $query->where('fragrance_id', $filters['fragrance_id']))
            ->when(isset($filters['variant_type_id']), fn ($query) => $query->where('variant_type_id', $filters['variant_type_id']))
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
