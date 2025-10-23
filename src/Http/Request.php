<?php

namespace Ludens\Http;

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

    public function parameter(?string $key): array|string
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

    public function validate(array $validationRules): void
    {
        $errors = [];

        $parameters = $this->parameters;

        if ($this->isJson) {
            /** @var array $parameters */
            $parameters = $this->json();
        }
        
        foreach ($parameters as $key => $value) {
            if (isset($validationRules[$key])) {
                $rules = explode('|', $validationRules[$key]);

                foreach ($rules as $rule) {
                    if ($rule === 'required' && (is_null($value) || $value === '')) {
                        $errors[$key] = 'This field is required.';
                        continue 2;
                    }

                    if (str_starts_with($rule, 'min:')) {
                        $minLength = (int) substr($rule, 4);
                        if (strlen($value) < $minLength) {
                            $errors[$key] = "This field must be at least $minLength characters long.";
                            continue 2;
                        }
                    }

                    if (str_starts_with($rule, 'max:')) {
                        $maxLength = (int) substr($rule, 4);
                        if (strlen($value) > $maxLength) {
                            $errors[$key] = "This field must be lower than $maxLength characters long.";
                            continue 2;
                        }
                    }
                }
            }
        }
        
        if (! empty($errors)) {
            if ($this->isJson) {
                Response::json(['errors' => $errors])->send();
                exit;
            }

            Response::redirect($this->referer)
                ->withErrors($errors)
                ->withOldData($this->parameters)
                ->send();
            exit;
        }

        return;
    }
}
