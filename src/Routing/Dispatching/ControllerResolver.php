<?php

namespace Ludens\Routing\Dispatching;

use ReflectionMethod;
use Ludens\Http\Request;
use Ludens\Exceptions\NotFoundException;
use Ludens\Routing\Matching\ResolvedRoute;

/**
 * Resolves and prepares controller method call with their arguments.
 * 
 * @package Ludens\Routing
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class ControllerResolver
{
    /**
     * Resolve the controller instance, method and arguments for a resolved route.
     *
     * @param ResolvedRoute $resolvedRoute The resolved route
     * @param Request $request The current HTTP request
     * @return array
     * @throws NotFoundException If the controller class cannot be instantiated
     */
    public function resolve(ResolvedRoute $resolvedRoute, Request $request): array
    {
        $handler = $resolvedRoute->handler();
        $controller = $handler->controller();
        $method = $handler->method();
        
        try {
            $controllerInstance = new $controller();
        } catch (\Error $e) {
            throw new NotFoundException(
                "The page you are looking for could not be found."
            );
        }

        $arguments = $this->resolveMethodArguments(
            $controllerInstance,
            $method,
            $request,
            $resolvedRoute->parameters()
        );

        return [$controllerInstance, $method, $arguments];
    }

    /**
     * Resolve the method arguments based on the controller method signature.
     *
     * @param object $controllerInstance The controller instance
     * @param string $method The method name
     * @param Request $request The current HTTP request
     * @param array $routeParameters The extracted route parameters
     * @return array
     */
    private function resolveMethodArguments(
        object $controllerInstance,
        string $method,
        Request $request,
        array $routeParameters
    ): array {
        $reflection = new ReflectionMethod($controllerInstance, $method);
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

            $arguments[] = $routeParameters[$parameter->getName()] ?? null;
        }

        return $arguments;
    }
}
