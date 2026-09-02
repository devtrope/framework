<?php

namespace Ludens\Routing\Routes;

use Attribute;
use Ludens\Contracts\HttpMethodAttributeInterface;
use Ludens\Http\Support\HttpMethod;

#[Attribute]
final class Post implements HttpMethodAttributeInterface
{
    public function __construct(private string $path, private HttpMethod $httpMethod = HttpMethod::POST) {}

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHttpMethod(): HttpMethod
    {
        return $this->httpMethod;
    }
}
