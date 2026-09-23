<?php

namespace Ludens\Routing;

use Ludens\Http\Support\HttpMethod;
use Ludens\Routing\Support\Handler;

final class Route
{
    /**
     * @var array<string, Handler[]>
     */
    private static array $routes = [];

    /**
     * @param HttpMethod $method
     * @param string $uri
     * @param Handler $handler
     * @return void
     */
    public static function add(HttpMethod $method, string $uri, Handler $handler): void
    {
        if (false === isset(self::$routes[$method->value])) {
            self::$routes[$method->value] = [];
        }
        self::$routes[$method->value][$uri] = $handler;
    }

    /**
     * Return all the routes registered with the specified HTTP method.
     *
     * @param HttpMethod $method
     * @return Handler[]
     */
    public static function getAllByRequestMethod(HttpMethod $method): array
    {
        if (false === isset(self::$routes[$method->value])) {
            return [];
        }
        return self::$routes[$method->value];
    }

    /**
     * Reset the routes to an empty array.
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$routes = [];
    }
}
