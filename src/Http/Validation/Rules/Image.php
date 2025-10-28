<?php

namespace Ludens\Http\Validation\Rules;

use Ludens\Http\Validation\ValidationRule;

class Image implements ValidationRule
{
    public function passes(string $field, mixed $value): bool
    {
        $supportedFilesTypes = ['image/png', 'image/jpeg', 'image/gif'];
        return in_array($value['type'], $supportedFilesTypes);
    }

    public function message(string $field): string
    {
        return "The {$field} is not a valid image (only PNG, JPEG, JPG and GIF are supported).";
    }
}
