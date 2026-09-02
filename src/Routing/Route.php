<?php

namespace Ludens\Routing;

use Ludens\Http\Support\HttpMethod;
use Ludens\Routing\Support\Handler;

final class Route
{
    private static array $routes = [];

    public static function add(HttpMethod $method, string $uri, Handler $handler): void
    {
        if (false === isset(self::$routes[$method->value])) {
            self::$routes[$method->value] = [];
        }
        self::$routes[$method->value][$uri] = $handler;
    }

    public static function getAllByRequestMethod(HttpMethod $method)
    {
        if (false === isset(self::$routes[$method->value])) {
            return [];
        }
        return self::$routes[$method->value];
    }

    public static function reset(): void
    {
        self::$routes = [];
    }
}
