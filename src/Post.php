<?php

namespace Ludens;

use Attribute;

#[Attribute]
final class Post implements HttpMethodInterface
{
    private string $httpMethod = 'POST';

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }
}
