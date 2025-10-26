<?php

namespace Ludens\Http;

use Ludens\Http\Validation\Validator;

class Request
{
    private string $uri;
    private string $method;
    private array $parameters = [];
    private ?string $referer = null;
    private string $body;
    private array $headers = [];
    private bool $isJson = false;

    public function __construct()
    {
        $this->uri = strval($_SERVER['REQUEST_URI']);
        $this->method = strval($_SERVER['REQUEST_METHOD']);
        $this->parameters = $_REQUEST;

        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->referer = strval($_SERVER['HTTP_REFERER']);
        }

        $this->body = file_get_contents('php://input') ?: '';

        if (function_exists('getallheaders')) {
            $this->headers = getallheaders() ?: [];
            
            if (isset($this->headers['Content-Type']) && $this->headers['Content-Type'] === 'application/json') {
                $this->isJson = true;
            }
        }
    }

    public function uri(): string
    {
        return $this->uri;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function parameter(?string $key = null): array|string
    {
        if ($key === null) {
            return $this->parameters;
        }

        if (! isset($this->parameters[$key])) {
            throw new \InvalidArgumentException("Parameter $key does not exist in the request.");
        }

        return $this->parameters[$key];
    }

    public function json(?string $key = null): array|string
    {
        $jsonData = json_decode($this->body, true);

        if ($key === null) {
            /** @var array $jsonData */
            return $jsonData;
        }

        if (! isset($jsonData[$key])) {
            throw new \InvalidArgumentException("JSON key $key does not exist in the request body.");
        }

        /** @var array $jsonData */
        return $jsonData[$key];
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * Validate the request data against given rules.
     *
     * @param array $rules The validation rules
     * @return void
     */
    public function validate(array $rules): void
    {
        $validator = new Validator();

        $dataToValidate = $this->isJson ? $this->json() : $this->parameter();

        if (! $validator->validate($dataToValidate, $rules)) {
            $this->handleValidationFailure($validator->errors());
        }
    }

    /**
     * Handle validation failure by sending appropriate response.
     *
     * @param array $errors The validation errors
     * @return void
     */
    private function handleValidationFailure(array $errors): void
    {
        if ($this->isJson) {
            Response::json([
                'errors' => $errors
            ])->setCode(422)->send();
            exit;
        }

        Response::redirect($this->referer ?? '/')
            ->withErrors($errors)
            ->withOldData($this->parameters)
            ->send();
        exit;
    }
}
