<?php

namespace Ludens\Http\Validation;

class Validator
{
    private array $errors = [];

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

    public function errors(): array
    {
        return $this->errors;
    }

    public function failed(): bool
    {
        return ! empty($this->errors);
    }
}
