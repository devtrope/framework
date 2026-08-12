<?php

namespace Ludens;

use Attribute;

#[Attribute]
final class Post
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
