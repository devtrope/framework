<?php

namespace Ludens\Routing\Support;

final class Handler
{
    private string $controller;
    private string $method;

    public function __construct(string $controller, string $method)
    {
        $this->controller = $controller;
        $this->method = $method;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public static function fromArray(array $handler): self
    {
        [$controller, $method] = $handler;
        return new self($controller, $method);
    }
}
