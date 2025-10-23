<?php

namespace Ludens\Routing;

use Exception;
use Ludens\Http\Request;
use Ludens\Http\Response;

class Router
{
    private static array $parameters = [];

    public static function dispatch(Request $request): void
    {
        $handler = self::match($request->uri(), Route::list($request->method()));
        $controller = $handler[0] ?? null;
        $method = $handler[1] ?? null;

        if ($controller && $method) {
            if (! class_exists($controller)) {
                throw new Exception("Controller class $controller does not exist");
            }

            $instance = new $controller();

            if (! method_exists($instance, $method)) {
                throw new Exception("Method $method does not exist in controller $controller");
            }

            $reflection = new \ReflectionMethod($instance, $method);
            $arguments = [];

            foreach ($reflection->getParameters() as $parameter) {
                if ($parameter->getType()) {
                    /** @var \ReflectionNamedType */
                    $parameterType = $parameter->getType();
                    if ($parameterType->getName() === Request::class) {
                        $arguments[] = $request;
                        continue;
                    }
                }

                $arguments[] = self::$parameters[$parameter->getName()] ?? null;
            }

            /** @var callable $callable */
            $callable = [$instance, $method];

            $response = call_user_func_array($callable, $arguments);

            if ($response instanceof Response) {
                $response->send();
                return;
            }

            /** @var string $response */
            echo $response;
            return;
        }

        http_response_code(404);
        $response = Response::render('errors/404');
        $response->send();
        return;
    }

    private static function match(string $uri, array $routes): array
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

            if (self::segmentsMatch($explodedRoute, $explodedUri)) {
                self::extractParameters($explodedRoute, $explodedUri);
                return $handler;
            }
        }

        return [];
    }

    private static function segmentsMatch(array $routeSegments, array $uriSegments): bool
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

    private static function extractParameters(array $routeSegments, array $uriSegments): void
    {
        foreach ($routeSegments as $index => $segment) {
            if (preg_match('/^{(\w+)}$/', $segment, $matches)) {
                $paramName = $matches[1];
                self::$parameters[$paramName] = $uriSegments[$index];
            }
        }
    }
}
