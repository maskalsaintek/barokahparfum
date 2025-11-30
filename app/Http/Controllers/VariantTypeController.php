<?php

namespace App\Http\Controllers;

use App\Models\VariantType;
use Illuminate\Http\Request;

class VariantTypeController extends Controller
{
    public function index()
    {
        $variantTypes = VariantType::orderBy('name')->get();
        return view('variant_types.index', compact('variantTypes'));
    }

    public function create()
    {
        return view('variant_types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:variant_type,code',
        ]);

        VariantType::create($data);

        return redirect()
            ->route('variant-types.index')
            ->with('success', 'Variant type created successfully.');
    }

    public function edit(VariantType $variantType)
    {
        return view('variant_types.edit', compact('variantType'));
    }

    public function update(Request $request, VariantType $variantType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:variant_type,code,' . $variantType->id,
        ]);

        $variantType->update($data);

        return redirect()
            ->route('variant-types.index')
            ->with('success', 'Variant type updated successfully.');
    }

    public function destroy(VariantType $variantType)
    {
        $variantType->delete();

        return redirect()
            ->route('variant-types.index')
            ->with('success', 'Variant type deleted successfully.');
    }
}

