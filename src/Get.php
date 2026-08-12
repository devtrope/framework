<?php

namespace Ludens;

use Attribute;

#[Attribute]
final class Get
{
    public function __construct(private string $path, private string $httpMethod = 'GET') {}

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }
}
