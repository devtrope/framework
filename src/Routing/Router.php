<?php

namespace Ludens\Routing;

final class Router
{
    public static function run(array $routes, string $path): void
    {
        $routeResolver = new RouteResolver();
        $resolvedRoute = $routeResolver->resolve($routes, $path);
        
        echo \call_user_func_array($resolvedRoute->getHandler(), $resolvedRoute->getParameters());
    }
}
