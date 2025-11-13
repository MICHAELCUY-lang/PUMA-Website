<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = Member::with(['user', 'cabinet', 'division'])->get();

        $transformedMembers = $members->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->user->name ?? '',
                'email' => $member->user->email ?? '',
                'avatar' => $member->user->avatar ?? 'https://i.pinimg.com/736x/f2/96/65/f296659f98543ad0ee11738a62e7652f.jpg',
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
        $member = Member::create($request->validated());
        $member->load(['user', 'cabinet', 'division']);

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
        $member->load(['user', 'cabinet', 'division']);

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
        $member->update($request->validated());
        $member->load(['user', 'cabinet', 'division']);

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
}
