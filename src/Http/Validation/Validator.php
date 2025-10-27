<?php

namespace Ludens\Http\Validation;

use Ludens\Http\Request;
use Ludens\Http\Response;

/**
 * Validator class to validate data against defined rules.
 * 
 * @package Ludens\Http\Validation
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class Validator
{
    /**
     * Start building validation rules.
     *
     * @return RuleBuilder
     */
    public function rule(): RuleBuilder
    {
        return new RuleBuilder();
    }

    /**
     * Validate specific fields from the request.
     *
     * @param Request $request
     * @param array $fields
     * @return array
     */
    public function fields(Request $request, array $fields): array
    {
        $data = $request->isJson() ? $request->json() : $request->all();
        $errors = [];

        foreach ($fields as $fieldName => $ruleBuilder) {
            $value = $data[$fieldName] ?? null;
            $rules = $ruleBuilder->getRules();

            foreach ($rules as $rule) {
                if (! $rule->passes($fieldName, $value)) {
                    $errors[$fieldName] = $rule->message($fieldName);
                    break;
                }
            }
        }

        if (! empty($errors)) {
            self::handleFailure($request, $errors);
        }

        return $data;
    }

    /**
     * Handle validation failure by sending appropriate response.
     *
     * @param Request $request
     * @param array $errors
     * @return void
     */
    private static function handleFailure(Request $request, array $errors): void
    {
        if ($request->wantsJson() || $request->isJson()) {
            Response::json(['errors' => $errors])->setCode(422)->send();
            exit;
        }

        Response::redirect($request->referer() ?? '/')
            ->withErrors($errors)
            //->withOldData($request->all())
            ->send();
        exit;
    }
}
