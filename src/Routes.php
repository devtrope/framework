<?php

namespace Ludens;

final class Routes
{
    private array $routes = [];

    public function add(string $method, string $uri, callable $callback): void
    {
        if (false === isset($this->routes[$method])) {
            $this->routes[$method] = [];
        }
        $this->routes[$method][$uri] = $callback;
    }

    public function getAll(string $method)
    {
        if (false === isset($this->routes[$method])) {
            return [];
        }
        return $this->routes[$method];
    }
}
