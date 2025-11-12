<?php

namespace Ludens\Routing\Matching;

use Ludens\Http\Request;
use Ludens\Routing\Support\Handler;
use Ludens\Routing\RouteCollection;
use Ludens\Exceptions\NotFoundException;

/**
 * Resolves HTTP requests to their corresponding route handlers.
 *
 * @package Ludens\Routing\Matching
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class RouteResolver
{
    private array $parameters = [];

    /**
     * Resolve a request to a matched route.
     *
     * @param Request $request
     * @return ResolvedRoute
     *
     * @throws NotFoundException If no matching route is found
     */
    public function resolve(Request $request): ResolvedRoute
    {
        $routes = RouteCollection::getRoutesForMethod($request->method());
        $result = $this->match($request->uri(), $routes);

        return new ResolvedRoute($result['handler'], $this->parameters, $result['middleware']);
    }

    /**
     * Find a matching handler for the given URI.
     *
     * @param string $uri The request URI
     * @param array $routes Available routes for the HTTP method
     * @return Handler
     *
     * @throws NotFoundException If no matching route is found
     */
    private function match(string $uri, array $routes): array
    {
        // Search for an exact match before continuing to more complex matching
        if (isset($routes[$uri])) {
            return $routes[$uri];
        }

        $explodedUri = explode('/', trim($uri, '/'));

        foreach ($routes as $route => $handler) {
            $explodedRoute = explode('/', trim($route, '/'));
            if (count($explodedUri) !== count($explodedRoute)) {
                continue;
            }

            if ($this->segmentsMatch($explodedRoute, $explodedUri)) {
                $this->extractParameters($explodedRoute, $explodedUri);
                return $handler;
            }
        }

        throw new NotFoundException(
            "The page you are looking for could not be found."
        );
    }

    /**
     * Check if route segments match URI segments.
     *
     * @param array $routeSegments
     * @param array $uriSegments
     * @return bool
     */
    private function segmentsMatch(array $routeSegments, array $uriSegments): bool
    {
        foreach ($routeSegments as $index => $segment) {
            // We don't care if it's a perfect match for dynamic segments like {slug} or {id}
            if (preg_match('/^{\w+}$/', $segment)) {
                continue;
            }

            if ($segment !== $uriSegments[$index]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Extract parameters from dynamic route segments.
     *
     * @param array $routeSegments
     * @param array $uriSegments
     * @return void
     */
    private function extractParameters(array $routeSegments, array $uriSegments): void
    {
        foreach ($routeSegments as $index => $segment) {
            if (preg_match('/^{(\w+)}$/', $segment, $matches)) {
                $paramName = $matches[1];
                $this->parameters[$paramName] = $uriSegments[$index];
            }
        }
    }
}
