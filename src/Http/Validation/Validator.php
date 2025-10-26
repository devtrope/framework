<?php

namespace Ludens\Http\Validation;

/**
 * Validator class to validate data against defined rules.
 * 
 * @package Ludens\Http\Validation
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class Validator
{
    private array $errors = [];

    /**
     * Validate the data against the given rules.
     *
     * @param array $data
     * @param array $rules
     * @return bool
     */
    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            $fieldRules = $this->parseRules($ruleString);

            foreach ($fieldRules as $rule) {
                if (! $rule->passes($field, $value)) {
                    $this->errors[$field] = $rule->message($field);
                    break;
                };
            }
        }

        return empty($this->errors);
    }

    /**
     * Parse the rule string into ValidationRule instances.
     *
     * @param string $ruleString
     * @return ValidationRule[]
     */
    private function parseRules(string $ruleString): array
    {
        $rules = [];
        $ruleParts = explode('|', $ruleString);

        foreach ($ruleParts as $part) {
            $rule = $this->createRuleInstance($part);

            if ($rule) {
                $rules[] = $rule;
            }
        }

        return $rules;
    }

    /**
     * Create a ValidationRule instance from a rule string.
     *
     * @param string $ruleString
     * @return ValidationRule|null
     */
    private function createRuleInstance(string $ruleString): ?ValidationRule
    {
        if (str_contains($ruleString, ':')) {
            [$ruleName, $parameter] = explode(':', $ruleString, 2);

            return match ($ruleName) {
                'min' => new \Ludens\Http\Validation\Rules\MinRule((int) $parameter),
                'max' => new \Ludens\Http\Validation\Rules\MaxRule((int) $parameter),
                default => null,
            };

        }

        $ruleClass = "\\Ludens\\Http\\Validation\\Rules\\" . ucfirst($ruleString) . "Rule";

        if (class_exists($ruleClass)) {
            return new $ruleClass();
        }

        return null;
    }

    /**
     * Get the validation errors.
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Check if validation has failed.
     *
     * @return bool
     */
    public function failed(): bool
    {
        return ! empty($this->errors);
    }
}
