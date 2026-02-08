<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Honeypot implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // If the honeypot field has any value, it's likely a bot
        if (! empty($value)) {
            $fail('Spam detected.');
        }
    }
}
