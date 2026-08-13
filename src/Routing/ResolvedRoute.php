<?php

namespace Ludens\Routing;

use Ludens\Exceptions\InvalidControllerException;
use Ludens\Exceptions\InvalidMethodException;
use Ludens\Routing\Support\Handler;
use ReflectionClass;

final class ResolvedRoute
{
    private array $handler;
    private array $parameters;

    public function __construct(Handler $handler, array $parameters)
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

    private function instantiate(Handler $handler): array
    {
        if (false === class_exists($handler->getController())) {
            throw new InvalidControllerException(
                "The controller {$handler->getController()} does not exist"
            );
        }

        $reflectionClass = new ReflectionClass($handler->getController());
        if (false === $reflectionClass->hasMethod($handler->getMethod())) {
            throw new InvalidMethodException(
                "The method {$handler->getMethod()} does not exist in controller {$handler->getController()}"
            );
        }

        $constructor = $reflectionClass->getConstructor();
        $arguments = [];
        if (null !== $constructor) {
            $arguments = $constructor->getParameters();
        }

        $instance = $reflectionClass->newInstance(...$arguments);
        return [$instance, $handler->getMethod()];
    }
}
