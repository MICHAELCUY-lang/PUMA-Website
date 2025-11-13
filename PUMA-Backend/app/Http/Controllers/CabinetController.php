<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use App\Http\Requests\StoreCabinetRequest;
use App\Http\Requests\UpdateCabinetRequest;
use Illuminate\Support\Facades\Storage;

class CabinetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cabinets = Cabinet::with('divisions')->get();

        $transformedCabinets = $cabinets->map(function ($cabinet) {
            return [
                'id' => $cabinet->id,
                'name' => $cabinet->name,
                'description' => $cabinet->description,
                'logo' => $cabinet->logo,
                'theme_color' => $cabinet->theme_color,
                'year' => $cabinet->year,
                'status' => $cabinet->status,
                'divisions_count' => $cabinet->divisions->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformedCabinets
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCabinetRequest $request)
    {
        $data = $request->validated();
        $divisionIds = $data['division_ids'] ?? [];
        unset($data['division_ids']);

        // Ensure required DB fields have sensible defaults
        if (empty($data['year'])) {
            $data['year'] = date('Y');
        }
        if (empty($data['status'])) {
            $data['status'] = 'active';
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('cabinets', 'public');
            $data['logo'] = 'storage/' . $path;
        }

        $cabinet = Cabinet::create($data);

        if (!empty($divisionIds)) {
            $cabinet->divisions()->sync($divisionIds);
        }

        $cabinet->load('divisions');

        return response()->json([
            'success' => true,
            'message' => 'Cabinet created successfully',
            'data' => $cabinet
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cabinet $cabinet)
    {
        $cabinet->load('divisions');

        $transformedCabinet = [
            'id' => $cabinet->id,
            'name' => $cabinet->name,
            'description' => $cabinet->description,
            'logo' => $cabinet->logo,
            'theme_color' => $cabinet->theme_color,
            'year' => $cabinet->year,
            'status' => $cabinet->status,
            'divisions' => $cabinet->divisions->map(function ($division) {
                return [
                    'id' => $division->id,
                    'code' => $division->code,
                    'name' => $division->name,
                    'title' => $division->title,
                    'description' => $division->description,
                    'image' => $division->image,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $transformedCabinet
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cabinet $cabinet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCabinetRequest $request, Cabinet $cabinet)
    {
        $data = $request->validated();
        $divisionIds = $data['division_ids'] ?? null;
        unset($data['division_ids']);

        // Keep existing year/status if not provided
        if (!isset($data['year'])) {
            $data['year'] = $cabinet->year;
        }
        if (!isset($data['status'])) {
            $data['status'] = $cabinet->status;
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if (!empty($cabinet->logo) && str_starts_with($cabinet->logo, 'storage/')) {
                $oldPath = str_replace('storage/', '', $cabinet->logo);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('logo')->store('cabinets', 'public');
            $data['logo'] = 'storage/' . $path;
        }

        $cabinet->update($data);

        if ($divisionIds !== null) {
            $cabinet->divisions()->sync($divisionIds);
        }

        $cabinet->load('divisions');

        return response()->json([
            'success' => true,
            'message' => 'Cabinet updated successfully',
            'data' => $cabinet
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cabinet $cabinet)
    {
        $cabinet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cabinet deleted successfully'
        ]);
    }
}
