<?php

namespace Ludens\Http\Validation\Rules;

use Ludens\Http\Validation\ValidationRule;

/**
 * Rule to ensure a field has a minimum length.
 */
class MinRule implements ValidationRule
{
    public function __construct(
        private int $minLength
    ) {
    }

    public function passes(string $field, mixed $value): bool
    {
        return strlen((string)$value) >= $this->minLength;
    }

    public function message(string $field): string
    {
        return "The {$field} field must be at least {$this->minLength} characters.";
    }
}
