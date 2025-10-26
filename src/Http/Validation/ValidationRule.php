<?php

namespace Ludens\Http\Validation;

interface ValidationRule
{
    /**
     * Validate the value.
     *
     * @param string $field
     * @param mixed $value
     * @return bool
     */
    public function passes(string $field, mixed $value): bool;

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string;
}