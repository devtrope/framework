<?php

namespace Ludens\Http\Validation\Rules;

use Ludens\Http\Validation\ValidationRule;

class RequiredRule implements ValidationRule
{
    public function passes(string $field, mixed $value): bool
    {
        return $value !== null && $value !== '';
    }

    public function message(string $field): string
    {
        return "The {$field} field is required.";
    }
}
