<?php

namespace Ludens\Http\Validation\Rules;

use Ludens\Files\FileError;
use Ludens\Http\Validation\ValidationRule;

/**
 * Rule to ensure a field is present and not empty.
 */
class RequiredRule implements ValidationRule
{
    public function passes(string $field, mixed $value): bool
    {
        $noFileError = FileError::NO_FILE;

        // Handle files errors
        if (is_array($value) && $value['error'] === $noFileError->value) {
            return false;
        }
        return $value !== null && $value !== '';
    }

    public function message(string $field): string
    {
        return "The {$field} field is required.";
    }
}
