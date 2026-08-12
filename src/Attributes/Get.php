<?php

namespace Ludens\Attributes;

use Attribute;
use Ludens\Contracts\HttpMethodAttributeInterface;

#[Attribute]
final class Get implements HttpMethodAttributeInterface
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
