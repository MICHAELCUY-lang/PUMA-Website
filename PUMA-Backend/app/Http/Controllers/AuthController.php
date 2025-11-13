<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        // Create token for the user
        $token = $user->createToken('auth-token')->plainTextToken;

        // Load member relationship with cabinet and division
        $user->load(['member.cabinet', 'member.division']);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'avatar' => $user->avatar,
                    'personal_description' => $user->personal_description,
                    'batch' => $user->batch,
                    'linkedin' => $user->linkedin,
                    'instagram' => $user->instagram,
                    'member' => $user->member ? [
                        'position' => $user->member->position,
                        'batch' => $user->member->batch,
                        'joined_date' => $user->member->joined_date,
                        'status' => $user->member->status,
                        'cabinet' => $user->member->cabinet ? [
                            'id' => $user->member->cabinet->id,
                            'name' => $user->member->cabinet->name,
                        ] : null,
                        'division' => $user->member->division ? [
                            'id' => $user->member->division->id,
                            'name' => $user->member->division->name,
                        ] : null,
                    ] : null,
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * Register new user
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        // Load member relationship if exists
        $user->load(['member.cabinet', 'member.division']);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'avatar' => $user->avatar,
                    'personal_description' => $user->personal_description,
                    'batch' => $user->batch,
                    'linkedin' => $user->linkedin,
                    'instagram' => $user->instagram,
                    'member' => $user->member ? [
                        'position' => $user->member->position,
                        'batch' => $user->member->batch,
                        'joined_date' => $user->member->joined_date,
                        'status' => $user->member->status,
                        'cabinet' => $user->member->cabinet ? [
                            'id' => $user->member->cabinet->id,
                            'name' => $user->member->cabinet->name,
                        ] : null,
                        'division' => $user->member->division ? [
                            'id' => $user->member->division->id,
                            'name' => $user->member->division->name,
                        ] : null,
                    ] : null,
                ],
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load(['member.cabinet', 'member.division']);

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'member' => $user->member ? [
                        'position' => $user->member->position,
                        'batch' => $user->member->batch,
                        'joined_date' => $user->member->joined_date,
                        'status' => $user->member->status,
                        'cabinet' => $user->member->cabinet ? [
                            'id' => $user->member->cabinet->id,
                            'name' => $user->member->cabinet->name,
                        ] : null,
                        'division' => $user->member->division ? [
                            'id' => $user->member->division->id,
                            'name' => $user->member->division->name,
                        ] : null,
                    ] : null,
                ],
            ],
        ]);
    }
}
