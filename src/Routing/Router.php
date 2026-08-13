<?php

namespace Ludens\Routing;

final class Router
{
    public static function run(array $routes, string $path): void
    {
        $routeResolver = new RouteResolver();
        [$handler, $arguments] = $routeResolver->resolve($routes, $path);
        
        $handler = (new self)->extract($handler);
        \call_user_func_array($handler, $arguments);
    }

    private function extract(array $handler): array
    {
        $class = $handler[0];
        $method = $handler[1];
        $instance = new $class();
        return [$instance, $method];
    }
}
