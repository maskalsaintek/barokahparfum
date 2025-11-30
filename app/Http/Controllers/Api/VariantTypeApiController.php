<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VariantType;
use Illuminate\Http\Request;

class VariantTypeApiController extends Controller
{
    // GET /api/variant-types
    public function index(Request $request)
    {
        $query = VariantType::query();

        if ($search = $request->query('q')) {
            $query->where(function ($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // kalau nanti mau pagination bisa pakai paginate()
        $data = $query->orderBy('name')->get();

        return response()->json($data);
    }

    // POST /api/variant-types
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:variant_type,code',
        ]);

        $variantType = VariantType::create($validated);

        return response()->json($variantType, 201);
    }

    // GET /api/variant-types/{id}
    public function show($id)
    {
        $variantType = VariantType::findOrFail($id);
        return response()->json($variantType);
    }

    // PUT/PATCH /api/variant-types/{id}
    public function update(Request $request, $id)
    {
        $variantType = VariantType::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'code' => 'sometimes|required|string|max:50|unique:variant_type,code,' . $variantType->id,
        ]);

        $variantType->update($validated);

        return response()->json($variantType);
    }

    // DELETE /api/variant-types/{id}
    public function destroy($id)
    {
        $variantType = VariantType::findOrFail($id);
        $variantType->delete();

        return response()->json(null, 204);
    }
}
