<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Http\Requests\StoreDivisionRequest;
use App\Http\Requests\UpdateDivisionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DivisionController extends Controller
{
    /**
     * Display a listing of all divisions.
     */
    public function index()
    {
        $divisions = Division::with(['members', 'cabinets'])->get();

        $transformedDivisions = $divisions->map(function ($division) {
            $firstCabinet = $division->cabinets->first();
            return [
                'id' => $division->id,
                'code' => $division->code,
                'name' => $division->name,
                'title' => $division->title,
                'description' => $division->description,
                'image' => $division->image,
                'members_count' => $division->members->count(),
                'cabinet_id' => $firstCabinet?->id,
                'cabinets' => $division->cabinets->map(fn($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                ]),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformedDivisions
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
    public function store(StoreDivisionRequest $request)
    {
        $data = $request->validated();
        $cabinetId = $data['cabinet_id'] ?? null;
        unset($data['cabinet_id']);

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('divisions', 'public');
            $data['image'] = 'storage/' . $path;
        }

        $division = Division::create($data);

        if ($cabinetId) {
            $division->cabinets()->sync([$cabinetId]);
        }

        $division->load('cabinets');

        return response()->json([
            'success' => true,
            'message' => 'Division created successfully',
            'data' => [
                'id' => $division->id,
                'code' => $division->code,
                'name' => $division->name,
                'title' => $division->title,
                'description' => $division->description,
                'image' => $division->image,
                'cabinet_id' => $division->cabinets->first()?->id,
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Division $division)
    {
        $division->load(['members', 'cabinets']);

        $transformedDivision = [
            'id' => $division->id,
            'code' => $division->code,
            'name' => $division->name,
            'title' => $division->title,
            'description' => $division->description,
            'image' => $division->image,
            'cabinet_id' => $division->cabinets->first()?->id,
            'cabinets' => $division->cabinets->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
            ]),
            'members' => $division->members,
        ];

        return response()->json([
            'success' => true,
            'data' => $transformedDivision
        ]);
    }

    /**
     * Get division by code.
     */
    public function getByCode($code)
    {
        $division = Division::where('code', $code)->with(['members', 'cabinets'])->first();

        if (!$division) {
            return response()->json([
                'success' => false,
                'message' => 'Division not found'
            ], 404);
        }

        $transformedDivision = [
            'id' => $division->id,
            'code' => $division->code,
            'name' => $division->name,
            'title' => $division->title,
            'description' => $division->description,
            'image' => $division->image,
            'cabinet_id' => $division->cabinets->first()?->id,
            'cabinets' => $division->cabinets->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
            ]),
            'members' => $division->members,
        ];

        return response()->json([
            'success' => true,
            'data' => $transformedDivision
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Division $division)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDivisionRequest $request, Division $division)
    {
        $data = $request->validated();
        $cabinetId = $data['cabinet_id'] ?? null;
        unset($data['cabinet_id']);

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            // Optionally delete old image if exists and is in storage
            if (!empty($division->image) && str_starts_with($division->image, 'storage/')) {
                $oldPath = str_replace('storage/', '', $division->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('image')->store('divisions', 'public');
            $data['image'] = 'storage/' . $path;
        }

        $division->update($data);

        if ($cabinetId) {
            $division->cabinets()->sync([$cabinetId]);
        }

        $division->load('cabinets');

        return response()->json([
            'success' => true,
            'message' => 'Division updated successfully',
            'data' => [
                'id' => $division->id,
                'code' => $division->code,
                'name' => $division->name,
                'title' => $division->title,
                'description' => $division->description,
                'image' => $division->image,
                'cabinet_id' => $division->cabinets->first()?->id,
            ]
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Division $division)
    {
        $division->delete();

        return response()->json([
            'success' => true,
            'message' => 'Division deleted successfully'
        ]);
    }
}
