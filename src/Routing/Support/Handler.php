<?php

namespace Ludens\Routing\Support;

use Ludens\Exceptions\InvalidControllerException;
use Ludens\Exceptions\InvalidMethodException;

final class Handler
{
    private string $controller;
    private string $method;

    public function __construct(string $controller, string $method)
    {
        $this->validate($controller, $method);
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

    private function validate(string $controller, string $method): void
    {
        if (false === class_exists($controller)) {
            throw new InvalidControllerException(
                "The controller {$controller} does not exist"
            );
        }

        if (false === method_exists($controller, $method)) {
            throw new InvalidMethodException(
                "The method {$method} does not exist in controller {$controller}"
            );
        }
    }
}
