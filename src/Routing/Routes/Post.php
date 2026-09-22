<?php

namespace Ludens\Routing\Routes;

use Attribute;
use Ludens\Contracts\HttpMethodAttributeInterface;
use Ludens\Http\Support\HttpMethod;

#[Attribute]
final class Post implements HttpMethodAttributeInterface
{
    /**
     * @param string $path
     * @param HttpMethod $httpMethod
     */
    public function __construct(private string $path, private HttpMethod $httpMethod = HttpMethod::POST)
    {}

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return HttpMethod
     */
    public function getHttpMethod(): HttpMethod
    {
        return $this->httpMethod;
    }
}
