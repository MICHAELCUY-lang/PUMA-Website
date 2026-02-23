<?php

namespace App\Http\Controllers;

use App\Models\UIContent;
use Illuminate\Http\Request;

class UIContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = UIContent::query();
        
        // Filter by active status if requested
        if (request()->has('active_only') && request('active_only') == 'true') {
            $query->active();
        }
        
        $content = $query->ordered()->get();

        return response()->json([
            'success' => true,
            'data' => $content
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:ui_content,key',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'type' => 'required|in:text,html,image,video,section',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
            'metadata' => 'nullable|array',
        ]);

        $content = UIContent::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'UI content created successfully',
            'data' => $content
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(UIContent $uiContent)
    {
        return response()->json([
            'success' => true,
            'data' => $uiContent
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UIContent $uiContent)
    {
        $validated = $request->validate([
            'key' => 'sometimes|string|unique:ui_content,key,' . $uiContent->id,
            'title' => 'sometimes|string|max:255',
            'content' => 'nullable|string',
            'type' => 'sometimes|in:text,html,image,video,section',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
            'metadata' => 'nullable|array',
        ]);

        $uiContent->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'UI content updated successfully',
            'data' => $uiContent
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UIContent $uiContent)
    {
        $uiContent->delete();

        return response()->json([
            'success' => true,
            'message' => 'UI content deleted successfully'
        ]);
    }
}
