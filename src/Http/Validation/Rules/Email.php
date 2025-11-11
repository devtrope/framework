<?php

namespace Ludens\Http\Validation\Rules;

use Ludens\Exceptions\ConfigurationException;
use Ludens\Http\Validation\ValidationRule;

/**
 * Rule to ensure that a field is a valid email.
 */
class Email implements ValidationRule
{
    public function passes(string $field, mixed $value): bool
    {
        if (! is_string($value)) {
            throw ConfigurationException::invalidValue($field, 'string', $value);
        }
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public function message(string $field): string
    {
        return "The {$field} is not in the correct format.";
    }
}
