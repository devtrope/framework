<?php

namespace Ludens\Routing\Support;

use Ludens\Contracts\HttpMethodAttributeInterface;

final class MethodAttribute
{
    /**
     * @param string $classname
     * @param string $method
     * @param HttpMethodAttributeInterface $instance
     */
    public function __construct(
        private string $classname,
        private string $method,
        private HttpMethodAttributeInterface $instance
    ) {}

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->classname;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return HttpMethodAttributeInterface
     */
    public function getInstance(): HttpMethodAttributeInterface
    {
        return $this->instance;
    }
}
