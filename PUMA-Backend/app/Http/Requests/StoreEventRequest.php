<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'event_date_end' => 'nullable|date|after_or_equal:event_date',
            'location' => 'nullable|string|max:255',
            'cabinet_id' => 'nullable|exists:cabinets,id',
            'status' => 'required|in:completed,upcoming,postponed,cancelled,planned',
            'content' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'string', // URLs only
            'image_files' => 'nullable|array',
            'image_files.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB per image
        ];
    }
}
