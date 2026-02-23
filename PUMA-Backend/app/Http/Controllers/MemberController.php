<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    /**
     * Get the full URL for an avatar path
     */
    protected function getAvatarUrl($member)
    {
        // If member has a photo_path, convert to full URL
        if ($member->photo_path) {
            // Check if it's already a full URL
            if (str_starts_with($member->photo_path, 'http://') || str_starts_with($member->photo_path, 'https://')) {
                return $member->photo_path;
            }
            // Convert storage path to URL
            return url('storage/' . $member->photo_path);
        }
        
        // Fall back to user's avatar
        if ($member->user && $member->user->avatar) {
            $avatar = $member->user->avatar;
            
            // Already a full URL
            if (str_starts_with($avatar, 'http://') || str_starts_with($avatar, 'https://')) {
                return $avatar;
            }
            
            // Legacy path like "/PUMA-Website/khairi.JPG" - these are in frontend's public folder
            // Vite has base: '/PUMA-Website/' so files are at http://localhost:5173/PUMA-Website/filename.jpg
            if (str_starts_with($avatar, '/PUMA-Website/') || str_starts_with($avatar, 'PUMA-Website/')) {
                $filename = basename($avatar);
                // For development, use Vite dev server with correct base path
                return 'http://localhost:5173/PUMA-Website/' . $filename;
            }
            
            // Path starting with / - might be a direct public path
            if (str_starts_with($avatar, '/')) {
                // Try frontend public folder with Vite base path
                $filename = basename($avatar);
                return 'http://localhost:5173/PUMA-Website/' . $filename;
            }
            
            // Regular storage path
            return url('storage/' . $avatar);
        }
        
        // Default avatar
        return 'https://i.pinimg.com/736x/f2/96/65/f296659f98543ad0ee11738a62e7652f.jpg';
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Member::with(['user', 'cabinet', 'division']);
        
        // Filter by visibility if requested (for user-facing pages)
        if (request()->has('visible_only') && request('visible_only') == 'true') {
            $query->where('is_visible', true);
        }
        
        // Apply custom ordering if display_order is set, otherwise default ordering
        $query->orderByRaw('COALESCE(display_order, 999999), id');
        
        $members = $query->get();

        $controller = $this; // Reference for closure
        $transformedMembers = $members->map(function ($member) use ($controller) {
            return [
                'id' => $member->id,
                'name' => $member->name ?? ($member->user->name ?? ''),
                'email' => $member->email ?? ($member->user->email ?? ''),
                'avatar' => $controller->getAvatarUrl($member),
                'position' => $member->position,
                'batch' => $member->batch ?? $member->user->batch ?? '',
                'birthdate' => $member->birthdate,
                'status' => $member->status ?? 'active',
                'division' => $member->division->name ?? '',
                'division_id' => $member->division_id,
                'cabinet' => $member->cabinet->name ?? '',
                'cabinet_id' => $member->cabinet_id,
                'user_id' => $member->user_id,
                'instagram' => $member->user->instagram ?? '',
                'linkedin' => $member->user->linkedin ?? '',
                'personal_description' => $member->user->personal_description ?? '',
                'display_order' => $member->display_order,
                'is_visible' => $member->is_visible ?? true,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $transformedMembers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        // Log raw request data
        \Log::info('=== MEMBER CREATION DEBUG ===');
        \Log::info('Raw request all:', $request->all());
        \Log::info('Has file avatar:', ['has' => $request->hasFile('avatar')]);
        
        // Get validated data
        $validatedData = $request->validated();
        \Log::info('Validated data (before file processing):', array_diff_key($validatedData, ['avatar' => '']));
        
        // Handle avatar file upload
        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            \Log::info('Avatar file details:', [
                'original_name' => $avatarFile->getClientOriginalName(),
                'size' => $avatarFile->getSize(),
                'mime' => $avatarFile->getMimeType()
            ]);
            
            // Store file in storage/app/public/avatars
            $avatarPath = $avatarFile->store('avatars', 'public');
            $validatedData['photo_path'] = $avatarPath;
            unset($validatedData['avatar']); // Remove file object from data
            
            \Log::info('Avatar stored at:', ['path' => $avatarPath]);
        }
        
        \Log::info('Final data for creation:', $validatedData);
        
        // Create member
        $member = Member::create($validatedData);
        \Log::info('Member created:', ['id' => $member->id, 'name' => $member->name, 'photo_path' => $member->photo_path]);
        
        // Only load relationships if they exist
        $relations = [];
        if ($member->user_id) $relations[] = 'user';
        if ($member->cabinet_id) $relations[] = 'cabinet';
        if ($member->division_id) $relations[] = 'division';
        
        if (!empty($relations)) {
            $member->load($relations);
        }

        return response()->json([
            'success' => true,
            'message' => 'Member created successfully',
            'data' => $member
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        // Only load relationships if they exist
        $relations = [];
        if ($member->user_id) $relations[] = 'user';
        if ($member->cabinet_id) $relations[] = 'cabinet';
        if ($member->division_id) $relations[] = 'division';
        
        if (!empty($relations)) {
            $member->load($relations);
        }

        return response()->json([
            'success' => true,
            'data' => $member
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMemberRequest $request, Member $member)
    {
        $validatedData = $request->validated();
        
        // Handle avatar file upload
        if ($request->hasFile('avatar')) {
            $avatarFile = $request->file('avatar');
            
            // Delete old avatar if exists
            if ($member->photo_path && \Storage::disk('public')->exists($member->photo_path)) {
                \Storage::disk('public')->delete($member->photo_path);
            }
            
            // Store new avatar
            $avatarPath = $avatarFile->store('avatars', 'public');
            $validatedData['photo_path'] = $avatarPath;
            unset($validatedData['avatar']);
        }
        
        $member->update($validatedData);
        
        // Only load relationships if they exist
        $relations = [];
        if ($member->user_id) $relations[] = 'user';
        if ($member->cabinet_id) $relations[] = 'cabinet';
        if ($member->division_id) $relations[] = 'division';
        
        if (!empty($relations)) {
            $member->load($relations);
        }

        return response()->json([
            'success' => true,
            'message' => 'Member updated successfully',
            'data' => $member
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Member deleted successfully'
        ]);
    }

    /**
     * Update display order for multiple members
     */
    public function updateOrder()
    {
        $memberOrders = request('members'); // Array of ['id' => 1, 'order' => 1]
        
        if (!is_array($memberOrders)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data format'
            ], 400);
        }

        foreach ($memberOrders as $memberOrder) {
            Member::where('id', $memberOrder['id'])
                ->update(['display_order' => $memberOrder['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Member order updated successfully'
        ]);
    }

    /**
     * Toggle member visibility
     */
    public function toggleVisibility(Member $member)
    {
        $member->update([
            'is_visible' => !$member->is_visible
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Member visibility updated successfully',
            'data' => [
                'id' => $member->id,
                'is_visible' => $member->is_visible
            ]
        ]);
    }

    /**
     * Upload member photo
     */
    public function uploadPhoto(Member $member)
    {
        request()->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if (request()->hasFile('photo')) {
            $file = request()->file('photo');
            $filename = time() . '_' . $member->id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('members/photos', $filename, 'public');

            $member->update([
                'photo_path' => '/storage/' . $path
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo uploaded successfully',
                'data' => [
                    'photo_path' => $member->photo_path
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No photo file provided'
        ], 400);
    }
}
