<?php

namespace Ludens\Routing;

use Exception;
use ReflectionClass;

final class ResolvedRoute
{
    private array $handler;
    private array $parameters;

    public function __construct(array $handler, array $parameters)
    {
        $this->handler = $this->instantiate($handler);
        $this->parameters = $parameters;
    }

    public function getHandler(): array
    {
        return $this->handler;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    private function instantiate(array $handler): array
    {
        if (false === class_exists($handler['controller'])) {
            throw new Exception(
                "The controller {$handler['controller']} does not exist"
            );
        }

        $reflectionClass = new ReflectionClass($handler['controller']);
        if (false === $reflectionClass->hasMethod($handler['method'])) {
            throw new Exception(
                "The method {$handler['method']} does not exist in controller {$handler['controller']}"
            );
        }

        $constructor = $reflectionClass->getConstructor();
        $arguments = [];
        if (null !== $constructor) {
            $arguments = $constructor->getParameters();
        }

        $instance = $reflectionClass->newInstance(...$arguments);
        return [$instance, $handler['method']];
    }
}
