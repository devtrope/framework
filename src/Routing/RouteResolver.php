<?php

namespace Ludens\Routing;

use Ludens\Exceptions\RouteNotFoundException;
use Ludens\Routing\Support\Handler;

final class RouteResolver
{
    /**
     * @var array<string, mixed>
     */
    private array $parameters = [];

    /**
     * Return the resolved route from the match method with the parameters retrieved from the
     * controller attribute.
     *
     * @param array<string, Handler> $routes
     * @param string $path
     * @return ResolvedRoute
     */
    public function resolve(array $routes, string $path): ResolvedRoute
    {
        $handler = $this->match($routes, $path);
        return new ResolvedRoute($handler, $this->parameters);
    }

    /**
     * Return the route matching the path provided in the controller attribute.
     *
     * @param array<string, Handler> $routes
     * @param string $path
     * @throws RouteNotFoundException
     * @return Handler
     */
    private function match(array $routes, string $path): Handler
    {
        if (isset($routes[$path])) {
            return $routes[$path];
        }

        foreach ($routes as $route => $handler) {
            if (false === $this->hasAMatchingRoute($route, $path)) {
                continue;
            }
            return $handler;
        }

        throw new RouteNotFoundException(\sprintf('No route found for path %s', $path));
    }

    /**
     * Verify if a provided path is matching one of the registered routes or not.
     *
     * @param string $route
     * @param string $path
     * @return bool
     */
    private function hasAMatchingRoute(string $route, string $path): bool
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
