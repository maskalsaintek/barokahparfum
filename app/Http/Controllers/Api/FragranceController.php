<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fragrance;
use Illuminate\Http\Request;

class FragranceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $fragrances = Fragrance::orderBy('name')->get();

        return response()->json($fragrances);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => 'required|string|max:50|unique:fragrances,code',
            'name'        => 'required|string|max:150',
            'gender'      => 'nullable|in:MALE,FEMALE,UNISEX',
            'description' => 'nullable|string',
            'origin'      => 'nullable|string|max:150',
            'is_active'   => 'boolean',
        ]);

        if (!isset($data['gender'])) {
            $data['gender'] = 'UNISEX';
        }

        $fragrance = Fragrance::create($data);

        return response()->json($fragrance, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response()->json($fragrance);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'code'        => 'sometimes|string|max:50|unique:fragrances,code,' . $fragrance->id,
            'name'        => 'sometimes|string|max:150',
            'gender'      => 'sometimes|in:MALE,FEMALE,UNISEX',
            'description' => 'nullable|string',
            'origin'      => 'nullable|string|max:150',
            'is_active'   => 'boolean',
        ]);

        $fragrance->update($data);

        return response()->json($fragrance);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fragrance->delete();

        return response()->json(null, 204);
    }
}
