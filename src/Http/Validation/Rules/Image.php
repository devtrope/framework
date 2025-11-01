<?php

namespace Ludens\Http\Validation\Rules;

use Exception;
use Ludens\Exceptions\ConfigurationException;
use Ludens\Http\Validation\ValidationRule;

/**
 * Rule to ensure that a file is an image.
 */
class Image implements ValidationRule
{
    public function passes(string $field, mixed $value): bool
    {
        $supportedFilesTypes = ['image/png', 'image/jpeg', 'image/gif'];
        if (! is_array($value)) {
            throw ConfigurationException::invalidValue($field, 'array', $value);
        }
        return in_array($value['type'], $supportedFilesTypes);
    }

    public function message(string $field): string
    {
        return "The {$field} is not a valid image (only PNG, JPEG, JPG and GIF are supported).";
    }
}
