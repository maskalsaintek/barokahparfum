<?php

namespace App\Http\Controllers;

use App\Models\Fragrance;
use Illuminate\Http\Request;

class FragranceController extends Controller
{
    public function index(Request $request)
    {
        $query = Fragrance::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('code', 'like', "%{$q}%")
                  ->orWhere('name', 'like', "%{$q}%")
                  ->orWhere('origin', 'like', "%{$q}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('is_active')) {
            // "1" atau "0"
            $query->where('is_active', $request->is_active == '1');
        }

        $fragrances = $query
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('fragrances.index', compact('fragrances'));
    }

    public function create()
    {
        return view('fragrances.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => 'required|string|max:50|unique:fragrances,code',
            'name'        => 'required|string|max:150',
            'gender'      => 'required|in:MALE,FEMALE,UNISEX',
            'origin'      => 'nullable|string|max:150',
            'description' => 'nullable|string',
            'is_active'   => 'nullable', // checkbox -> kita set manual
        ]);

        $data['is_active'] = $request->has('is_active');

        Fragrance::create($data);

        return redirect()
            ->route('fragrances.index')
            ->with('success', 'Fragrance created successfully.');
    }

    public function edit(Fragrance $fragrance)
    {
        return view('fragrances.edit', compact('fragrance'));
    }

    public function update(Request $request, Fragrance $fragrance)
    {
        $data = $request->validate([
            'code'        => 'required|string|max:50|unique:fragrances,code,' . $fragrance->id,
            'name'        => 'required|string|max:150',
            'gender'      => 'required|in:MALE,FEMALE,UNISEX',
            'origin'      => 'nullable|string|max:150',
            'description' => 'nullable|string',
            'is_active'   => 'nullable',
        ]);

        $data['is_active'] = $request->has('is_active');

        $fragrance->update($data);

        return redirect()
            ->route('fragrances.index')
            ->with('success', 'Fragrance updated successfully.');
    }

    public function destroy(Fragrance $fragrance)
    {
        $fragrance->delete();

        return redirect()
            ->route('fragrances.index')
            ->with('success', 'Fragrance deleted successfully.');
    }

    // show() optional untuk admin web
    public function show(Fragrance $fragrance)
    {
        return redirect()->route('fragrances.edit', $fragrance);
    }
}