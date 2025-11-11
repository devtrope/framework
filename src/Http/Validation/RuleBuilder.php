<?php

namespace Ludens\Http\Validation;

use Ludens\Http\Validation\Rules;

/**
 * Fluent builder for validation rules.
 *
 * @package Ludens\Http\Validation
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class RuleBuilder
{
    private array $rules = [];

    /**
     * Mark field as required.
     *
     * @return RuleBuilder
     */
    public function required(): self
    {
        $this->rules[] = new Rules\RequiredRule();
        return $this;
    }

    /**
     * Set minimum length constraint.
     *
     * @param int $length
     * @return RuleBuilder
     */
    public function minLength(int $length): self
    {
        $this->rules[] = new Rules\MinRule($length);
        return $this;
    }

    /**
     * Set maximum length constraint.
     *
     * @param int $length
     * @return RuleBuilder
     */
    public function maxLength(int $length): self
    {
        $this->rules[] = new Rules\MaxRule($length);
        return $this;
    }

    /**
     * Verify if a field is an image.
     *
     * @return RuleBuilder
     */
    public function image(): self
    {
        $this->rules[] = new Rules\Image();
        return $this;
    }

    /**
     * Verify if a field is an email.
     *
     * @return RuleBuilder
     */
    public function email(): self
    {
        $this->rules[] = new Rules\Email();
        return $this;
    }

    /**
     * Verify if a field is an integer.
     *
     * @return RuleBuilder
     */
    public function integer(): self
    {
        $this->rules[] = new Rules\Integer();
        return $this;
    }

    /**
     * Get all configured rules.
     *
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }
}
