<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Session;
use Illuminate\Translation\PotentiallyTranslatedString;

class validRecipient implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!array_key_exists('name', $value) && !array_key_exists('email', $value)) {
            Session::push('code', ['invalid_recipient']);
        }
    }
}
