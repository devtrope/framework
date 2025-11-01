<?php

namespace Ludens\Http\Validation\Rules;

use Ludens\Http\Validation\ValidationRule;

/**
 * Rule to ensure a field has a minimum length.
 */
class MaxRule implements ValidationRule
{
    public function __construct(
        private int $maxLength
    ) {
    }

    public function passes(string $field, mixed $value): bool
    {
        return strlen((string)$value) <= $this->maxLength;
    }

    public function message(string $field): string
    {
        return "The {$field} field must not exceed {$this->maxLength} characters.";
    }
}
