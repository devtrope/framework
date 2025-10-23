<?php

namespace Ludens\Routing;

class Route
{
    protected static array $routes = [];

    public static function get(string $path , array $handler): void
    {
        self::assignRoute('GET', $path, $handler);
    }

    public static function post(string $path , array $handler): void
    {
        self::assignRoute('POST', $path, $handler);
    }

    private static function assignRoute(string $method, string $path, array $handler): void
    {
        self::$routes[$method][$path] = $handler;
    }

    public static function list(string $requestMethod): array
    {
        if (! isset(self::$routes[$requestMethod])) {
            return [];
        }

        return self::$routes[$requestMethod];
    }
}
