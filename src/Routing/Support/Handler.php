<?php

namespace Ludens\Routing\Support;

use InvalidArgumentException;

/**
 * Represents a route handler (controller and method pair).
 * 
 * Encapsulates and validates controller/method combinations for route handling.
 * 
 * @package Ludens\Routing
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class Handler
{
    private string $controller;
    private string $method;

    /**
     * Create a new Handler instance.
     * 
     * @param string $controller The fully qualified controller class name
     * @param string $method The method name to call on the controller
     * @throws InvalidArgumentException If the controller does not exist
     * @throws InvalidArgumentException If the method does not exist in the controller
     */
    public function __construct(string $controller, string $method)
    {
        $this->validate($controller, $method);

        $this->controller = $controller;
        $this->method = $method;
    }

    /**
     * Create a Handler from an array.
     *
     * @param array $handler Format [ControllerClass::class, 'methodName']
     * @return Handler
     * @throws InvalidArgumentException If the array format is invalid
     */
    public static function fromArray(array $handler): self
    {
        if (count($handler) !== 2) {
            throw new InvalidArgumentException(
                "Handler array must contain exactly 2 elements: [Controller::class, 'method']"
            );
        }

        [$controller, $method] = $handler;

        if (! is_string($controller) || ! is_string($method)) {
            throw new InvalidArgumentException(
                "Both controller and method must be strings"
            );
        }

        return new self($controller, $method);
    }

    /**
     * Validate the controller and method.
     *
     * @param mixed $controller
     * @param mixed $method
     * @return void
     * @throws InvalidArgumentException
     */
    private function validate(string $controller, string $method): void
    {
        if (! class_exists($controller)) {
            throw new InvalidArgumentException(
                "Controller class {$controller} does not exist"
            );
        }

        if (! method_exists($controller, $method)) {
            throw new InvalidArgumentException(
                "Method {$method} does not exist in controller {$controller}"
            );
        }
    }

    /**
     * Get the controller class name
     *
     * @return string
     */
    public function controller(): string
    {
        return $this->controller;
    }

    /**
     * Get the method name
     *
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * Convert the handler to an array.
     *
     * @return string[]
     */
    public function toArray(): array
    {
        return [$this->controller, $this->method];
    }

    /**
     * Get a string representation of the handler.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->controller . '@' . $this->method;
    }
}
