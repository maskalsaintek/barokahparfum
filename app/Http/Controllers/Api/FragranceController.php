<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fragrance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FragranceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'code' => ['nullable', 'string', 'max:50'],
            'name' => ['nullable', 'string', 'max:150'],
            'gender' => ['nullable', Rule::in(['MALE', 'FEMALE', 'UNISEX'])],
            'origin' => ['nullable', 'string', 'max:150'],
            'is_active' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $fragrances = Fragrance::query()
            ->when($filters['code'] ?? null, fn ($query, string $code) => $query->where('code', 'like', "%{$code}%"))
            ->when($filters['name'] ?? null, fn ($query, string $name) => $query->where('name', 'like', "%{$name}%"))
            ->when($filters['gender'] ?? null, fn ($query, string $gender) => $query->where('gender', $gender))
            ->when($filters['origin'] ?? null, fn ($query, string $origin) => $query->where('origin', 'like', "%{$origin}%"))
            ->when(array_key_exists('is_active', $filters), fn ($query) => $query->where('is_active', $filters['is_active']))
            ->orderBy('name')
            ->orderBy('id')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();

        return response()->json($fragrances);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);

        if (!isset($data['gender'])) {
            $data['gender'] = 'UNISEX';
        }

        $fragrance = Fragrance::create($data);

        return response()->json($fragrance, 201);
    }

    public function show(Fragrance $fragrance): JsonResponse
    {
        return response()->json($fragrance);
    }

    public function update(Request $request, Fragrance $fragrance): JsonResponse
    {
        $data = $this->validatedData($request, true, $fragrance);

        $fragrance->update($data);

        return response()->json($fragrance);
    }

    public function destroy(Fragrance $fragrance): JsonResponse
    {
        $fragrance->delete();

        return response()->json(null, 204);
    }

    private function validatedData(Request $request, bool $partial = false, ?Fragrance $fragrance = null): array
    {
        $presence = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'code' => [
                $presence,
                'string',
                'max:50',
                Rule::unique('fragrance', 'code')->ignore($fragrance?->id),
            ],
            'name' => [$presence, 'string', 'max:150'],
            'gender' => ['sometimes', 'nullable', Rule::in(['MALE', 'FEMALE', 'UNISEX'])],
            'description' => ['sometimes', 'nullable', 'string'],
            'origin' => ['sometimes', 'nullable', 'string', 'max:150'],
            'is_active' => ['sometimes', 'boolean'],
        ]);
    }
}
