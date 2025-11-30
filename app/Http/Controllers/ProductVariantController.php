<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\Fragrance;
use App\Models\VariantType;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductVariant::with(['fragrance', 'variantType']);

        if ($request->filled('fragrance_id')) {
            $query->where('fragrance_id', $request->fragrance_id);
        }

        if ($request->filled('variant_type_id')) {
            $query->where('variant_type_id', $request->variant_type_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->is_active);
        }

        $variants = $query
            ->orderBy('fragrance_id')
            ->orderBy('bottle_size_ml')
            ->get();

        $fragrances   = Fragrance::orderBy('name')->get();
        $variantTypes = VariantType::orderBy('name')->get();

        return view('product_variants.index', compact('variants', 'fragrances', 'variantTypes'));
    }

    public function create()
    {
        $fragrances   = Fragrance::orderBy('name')->get();
        $variantTypes = VariantType::orderBy('name')->get();

        return view('product_variants.create', compact('fragrances', 'variantTypes'));
    }

    public function store(Request $request)
    {
         try {
            $data = $request->validate([
                'fragrance_id'    => 'required|integer|exists:fragrance,id',
                'variant_type_id' => 'required|integer|exists:variant_type,id',
                'bottle_size_ml'  => 'nullable|numeric|min:0.1',
                'base_price'      => 'required|numeric|min:0',
                'cost_ml'         => 'required|numeric|min:0',
                'mix_ratio'       => 'nullable|string|max:50',
                'is_active'       => 'nullable',
            ]);

            $data['is_active'] = $request->has('is_active');

            ProductVariant::create($data);
            return redirect()
            ->route('product-variants.index')
            ->with('success', 'Product variant updated successfully.');
        } catch (\Throwable $e) {
            dd('ERROR:', $e->getMessage(), $e);
        }
    }

    public function update(Request $request, ProductVariant $productVariant)
    {
        try {
            $data = $request->validate([
                'fragrance_id'    => 'required|integer|exists:fragrance,id',
                'variant_type_id' => 'required|integer|exists:variant_type,id',
                'bottle_size_ml'  => 'nullable|numeric|min:0.1',
                'base_price'      => 'required|numeric|min:0',
                'cost_ml'         => 'required|numeric|min:0',
                'mix_ratio'       => 'nullable|string|max:50',
                'is_active'       => 'nullable',
            ]);

            $data['is_active'] = $request->has('is_active');

            $productVariant->update($data);
        } catch (\Throwable $e) {
            dd('ERROR:', $e->getMessage(), $e);
        }

        return redirect()
            ->route('product-variants.index')
            ->with('success', 'Product variant updated successfully.');
    }



    public function edit(ProductVariant $productVariant)
    {
        $fragrances   = Fragrance::orderBy('name')->get();
        $variantTypes = VariantType::orderBy('name')->get();

        return view('product_variants.edit', [
            'variant'      => $productVariant,
            'fragrances'   => $fragrances,
            'variantTypes' => $variantTypes,
        ]);
    }

    public function destroy(ProductVariant $productVariant)
    {
        $productVariant->delete();

        return redirect()
            ->route('product-variants.index')
            ->with('success', 'Product variant deleted successfully.');
    }

    // show() nggak terlalu perlu untuk admin web, bisa dibiarkan kosong atau dihapus dari route
    public function show(ProductVariant $productVariant)
    {
        return redirect()->route('product-variants.edit', $productVariant);
    }
}
