<?php

namespace Ludens\Routing\Matching;

use Ludens\Routing\Support\Handler;

/**
 * Represents a resolved route with its handler and parameters.
 *
 * @package Ludens\Routing\Matching
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class ResolvedRoute
{
    /**
     * @param Handler $handler
     * @param array $parameters
     */
    public function __construct(
        private Handler $handler,
        private array $parameters = []
    ) {
    }

    /**
     * Get the route handler.
     *
     * @return Handler
     */
    public function handler(): Handler
    {
        return $this->handler;
    }

    /**
     * Get the extracted route parameters.
     *
     * @return array
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    /**
     * Get a specific route parameter by name.
     *
     * @param string $name The parameter name
     * @param mixed $default The default value if parameter not found
     * @return mixed
     */
    public function parameter(string $name, mixed $default): mixed
    {
        return $this->parameters[$name] ?? $default;
    }
}
