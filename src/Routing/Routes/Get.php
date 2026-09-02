<?php

namespace Ludens\Routing\Routes;

use Attribute;
use Ludens\Contracts\HttpMethodAttributeInterface;
use Ludens\Http\Support\HttpMethod;

#[Attribute]
final class Get implements HttpMethodAttributeInterface
{
    public function __construct(private string $path, private HttpMethod $httpMethod = HttpMethod::GET) {}

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHttpMethod(): HttpMethod
    {
        return $this->httpMethod;
    }
}
