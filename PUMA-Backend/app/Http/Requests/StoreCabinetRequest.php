<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCabinetRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:cabinets,name',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'theme_color' => 'nullable|string',
            'year' => 'nullable|digits:4',
            'status' => 'nullable|string|in:active,inactive,archived',
            'division_ids' => 'nullable|array',
            'division_ids.*' => 'integer|exists:divisions,id',
        ];
    }
}
