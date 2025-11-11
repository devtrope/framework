<?php

namespace Ludens\Http\Validation\Rules;

use Ludens\Exceptions\ConfigurationException;
use Ludens\Http\Validation\ValidationRule;

/**
 * Rule to ensure that a field is an integer.
 */
class Integer implements ValidationRule
{
    public function passes(string $field, mixed $value): bool
    {
        return is_int($value);
    }

    public function message(string $field): string
    {
        return "The {$field} should be an integer.";
    }
}
