<?php

namespace Ludens\Routing;

use Exception;

final class RouteResolver
{
    private array $parameters = [];

    public function resolve(array $routes, string $path): array
    {
        $result = $this->match($routes, $path);
        return [$result, $this->parameters];
    }

    private function match(array $routes, string $path): array
    {
        if (isset($routes[$path])) {
            return $routes[$path];
        }

        foreach ($routes as $route => $handler) {
            if (false === $this->checkPathAndRouteParameters($route, $path)) {
                continue;
            }

            return $handler;
        }

        throw new Exception("No route found for path {$path}");
    }

    private function checkPathAndRouteParameters(string $route, string $path): bool
    {
        // If the route doesn't contain arguments, there's no need to go further
        // because this checking is done for routes with arguments
        if (false === str_contains($route, '{')) {
            return false;
        }

        $routeParameters = explode('/', trim($route, '/'));
        $pathParameters = explode('/', trim($path, '/'));

        if (\count($routeParameters) !== \count($pathParameters)) {
            return false;
        }

        for ($i = 0; $i < \count($pathParameters); $i++) {
            if (str_starts_with($routeParameters[$i], '{')) {
                $parameterKey = trim($routeParameters[$i], '{}');
                $this->parameters[$parameterKey] = $pathParameters[$i];
                continue;
            }

            if ($pathParameters[$i] !== $routeParameters[$i]) {
                return false;
            }
        }

        return true;
    }
}
