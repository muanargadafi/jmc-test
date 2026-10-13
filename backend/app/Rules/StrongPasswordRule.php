<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class StrongPasswordRule implements ValidationRule
{
    private const PATTERN = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{8,}$/';

    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || !preg_match(self::PATTERN, $value)) {
            $fail('The :attribute must be at least 8 characters and contain at least one uppercase letter, one lowercase letter, one number, and one special character.');
        }
    }
}
