<?php

namespace Ludens\Routing\Routes;

use Attribute;
use Ludens\Contracts\HttpMethodAttributeInterface;

#[Attribute]
final class Post implements HttpMethodAttributeInterface
{
    public function __construct(private string $path, private string $httpMethod = 'POST') {}

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }
}
