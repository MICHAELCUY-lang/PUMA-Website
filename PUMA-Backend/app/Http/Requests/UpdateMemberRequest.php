<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allow member updates
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'avatar' => 'nullable|file|image|max:2048', // Accept uploaded image files (max 2MB)
            'instagram_url' => 'nullable|string|max:500',
            'linkedin_url' => 'nullable|string|max:500',
            
            'user_id' => 'nullable|exists:users,id',
            'cabinet_id' => 'nullable|exists:cabinets,id',
            'division_id' => 'nullable|exists:divisions,id',
            'position' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'batch' => 'nullable|string|max:50',
            'birthdate' => 'nullable|date',
            'joined_date' => 'nullable|date',
            'left_date' => 'nullable|date',
            'display_order' => 'nullable|integer',
            'is_visible' => 'nullable|boolean',
            'photo_path' => 'nullable|string|max:500',
        ];
    }
}
