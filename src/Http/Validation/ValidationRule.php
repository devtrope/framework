<?php

namespace Ludens\Http\Validation;

/**
 * Interface for validation rules.
 *
 * @package Ludens\Http\Validation
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
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
     * @param string $field
     * @return string
     */
    public function message(string $field): string;
}
