<?php

namespace Ludens\Http;

use Ludens\Http\Support\HttpMethod;

final class Request
{
    public function __construct(private HttpMethod $httpMethod, private string $path)
    {
    }

    public static function fromGlobals(): self
    {
        return new self(HttpMethod::from($_SERVER['REQUEST_METHOD']), $_SERVER['REQUEST_URI']);
    }

    public function getHttpMethod(): HttpMethod
    {
        return $this->httpMethod;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
