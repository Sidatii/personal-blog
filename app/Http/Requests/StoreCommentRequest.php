<?php

namespace App\Http\Requests;

use App\Rules\Honeypot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'content' => [
                'required',
                'string',
                'max:'.config('comments.max_length', 5000),
            ],
            'author_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'author_email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'author_website' => [
                'nullable',
                'url',
                'max:255',
            ],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('comments', 'id')->where(function ($query) {
                    // Ensure parent comment belongs to the same post
                    $query->where('post_id', $this->route('post')->id);
                }),
            ],
            // Honeypot field for spam detection (should be empty)
            'website' => [
                'nullable',
                'string',
                'max:255',
            ],
            // Actual honeypot field - bots often fill this
            'trap_field' => [
                'nullable',
                new Honeypot,
            ],
        ];
    }

    /**
     * Get custom error messages for validation.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'content.required' => 'Please enter a comment.',
            'content.max' => 'Comments cannot exceed :max characters.',
            'author_email.email' => 'Please enter a valid email address.',
            'author_website.url' => 'Please enter a valid URL.',
            'parent_id.exists' => 'The reply target is invalid or does not exist.',
            'trap_field' => 'Spam detected.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from content
        if ($this->has('content')) {
            $this->merge([
                'content' => trim($this->input('content')),
            ]);
        }

        // Trim whitespace from content
        if ($this->has('content')) {
            $this->merge([
                'content' => trim($this->input('content')),
            ]);
        }
    }
}
