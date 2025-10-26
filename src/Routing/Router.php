<?php

namespace Ludens\Routing;

use Ludens\Http\Request;
use Ludens\Http\Response;
use Ludens\Exceptions\NotFoundException;

class Router
{
    private array $parameters = [];
    private ?string $controller;
    private ?string $method;

    public function __construct(Request $request)
    {
        $this->findHandlerFor($request);
    }

    public static function dispatch(Request $request): void
    {
        $router = new self($request);

        try {
            $controllerInstance = new $router->controller();
        } catch (\Error $e) {
            throw new NotFoundException(
                "The page you are looking for could not be found."
            );
        }

        $reflection = new \ReflectionMethod($controllerInstance, $router->method);
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
        $callable = [$controllerInstance, $router->method];

        $response = call_user_func_array($callable, $arguments);

        if ($response instanceof Response) {
            $response->send();
            return;
        }

        /** @var string $response */
        echo $response;
        return;
    }

    private function findHandlerFor(Request $request): void
    {
        $routeResolver = new RouteResolver();
        $resolvedRoute = $routeResolver->resolve($request);

        $this->controller = $resolvedRoute->handler()->controller();
        $this->method = $resolvedRoute->handler()->method();
    }
}
