<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use App\Http\Requests\StoreAspirationRequest;
use App\Http\Requests\UpdateAspirationRequest;

class AspirationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $aspirations = Aspiration::with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($aspiration) {
                    return [
                        'id' => $aspiration->id,
                        'name' => $aspiration->user->name,
                        'content' => $aspiration->content,
                        'type' => $aspiration->type,
                        'status' => $aspiration->status,
                        'response' => $aspiration->response,
                        'created_at' => $aspiration->created_at->format('M Y'),
                        'date' => $aspiration->created_at->format('M Y'),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $aspirations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch aspirations',
                'error' => $e->getMessage()
            ], 500);
        }
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
    public function store(StoreAspirationRequest $request)
    {
        try {
            // For now, use the test user or create anonymous user
            $user = \App\Models\User::firstOrCreate(
                ['email' => 'anonymous@puma.com'],
                [
                    'name' => 'Anonymous User',
                    'password' => bcrypt('password'),
                ]
            );

            $aspiration = Aspiration::create([
                'user_id' => $user->id,
                'content' => $request->input('content'),
                'type' => $request->input('type', 'aspiration'),
                'status' => 'new',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Aspiration submitted successfully',
                'data' => $aspiration
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit aspiration',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Aspiration $aspiration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aspiration $aspiration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAspirationRequest $request, Aspiration $aspiration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aspiration $aspiration)
    {
        //
    }
}
