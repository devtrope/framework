<?php

namespace Ludens\Routing;

final class Route
{
    private static array $routes = [];

    public static function add(string $method, string $uri, array $handler): void
    {
        if (false === isset(self::$routes[$method])) {
            self::$routes[$method] = [];
        }
        self::$routes[$method][$uri] = $handler;
    }

    public static function getAllByRequestMethod(string $method)
    {
        if (false === isset(self::$routes[$method])) {
            return [];
        }
        return self::$routes[$method];
    }
}
