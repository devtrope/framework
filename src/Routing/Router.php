<?php

namespace Ludens\Routing;

use Exception;
use Ludens\Http\Request;
use Ludens\Http\Response;

class Router
{
    private array $parameters = [];
    private ?string $controller;
    private ?string $method;
    private object $controllerInstance;

    public function __construct(Request $request)
    {
        $this->findHandlerFor($request);
    }

    public static function dispatch(Request $request): void
    {
        $router = new self($request);
        $router->verifyHandler();

        $reflection = new \ReflectionMethod($router->controllerInstance, $router->method);
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

            $arguments[] = $router->parameters[$parameter->getName()] ?? null;
        }

        /** @var callable $callable */
        $callable = [$router->controllerInstance, $router->method];

        $response = call_user_func_array($callable, $arguments);

        if ($response instanceof Response) {
            $response->send();

            return;
        }

        /** @var string $response */
        echo $response;

        return;
    }

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

        return [];
    }

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

    private function extractParameters(array $routeSegments, array $uriSegments): void
    {
        foreach ($routeSegments as $index => $segment) {
            if (preg_match('/^{(\w+)}$/', $segment, $matches)) {
                $paramName = $matches[1];
                $this->parameters[$paramName] = $uriSegments[$index];
            }
        }
    }

    private function findHandlerFor(Request $request): void
    {
        $handler = $this->match(
            $request->uri(),
            Route::list($request->method())
        );

        if (empty($handler)) {
            $this->controller = null;
            $this->method = null;

            return;
        }

        $this->controller = $handler[0];
        $this->method = $handler[1];
    }

    private function verifyHandler(): void
    {
        if (! $this->controller || ! $this->method) {
            throw new \Ludens\Exceptions\NotFoundException("The page you are looking for could not be found.");
        }

        if (! class_exists($this->controller)) {
            throw new Exception("Controller class $this->controller does not exist");
        }

        $this->controllerInstance = new $this->controller();

        if (! method_exists($this->controllerInstance, $this->method)) {
            throw new Exception("Method $this->method does not exist in controller $this->controller");
        }
    }
}
