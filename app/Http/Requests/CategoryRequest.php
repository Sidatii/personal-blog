<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $categoryId = $this->route('category');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];

        // Add unique rule for slug, ignoring current category on update
        if ($categoryId) {
            $rules['slug'][] = Rule::unique('categories')->ignore($categoryId);
        } else {
            $rules['slug'][] = 'unique:categories,slug';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'slug.required' => 'The category slug is required.',
            'slug.unique' => 'This slug is already in use.',
        ];
    }
}
