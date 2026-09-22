<?php

namespace Ludens\Routing\Support;

final class InstantiatedHandler
{
    /**
     * @param object $controller
     * @param string $method
     */
    public function __construct(
        private object $controller,
        private string $method
    )
    {}

    /**
     * @return object
     */
    public function getController(): object
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}
