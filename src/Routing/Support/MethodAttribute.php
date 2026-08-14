<?php

namespace Ludens\Routing\Support;

use Ludens\Contracts\HttpMethodAttributeInterface;

final class MethodAttribute
{
    public function __construct(
        private string $classname,
        private string $method,
        private HttpMethodAttributeInterface $instance
    ) {}

    public function getClassName(): string
    {
        return $this->classname;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getInstance(): HttpMethodAttributeInterface
    {
        return $this->instance;
    }
}
