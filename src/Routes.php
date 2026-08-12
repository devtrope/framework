<?php

namespace Ludens;

final class Routes
{
    private static array $routes = [];

    public static function add(string $method, string $uri, callable|array $callback): void
    {
        if (false === isset(self::$routes[$method])) {
            self::$routes[$method] = [];
        }
        self::$routes[$method][$uri] = $callback;
    }

    public static function getAll(string $method)
    {
        if (false === isset(self::$routes[$method])) {
            return [];
        }
        return self::$routes[$method];
    }
}
