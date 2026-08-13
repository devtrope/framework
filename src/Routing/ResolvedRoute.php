<?php

namespace Ludens\Routing;

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
        $reflectionClass = new ReflectionClass($handler->getController());

        $constructor = $reflectionClass->getConstructor();
        $arguments = [];
        if (null !== $constructor) {
            $arguments = $constructor->getParameters();
        }

        $instance = $reflectionClass->newInstance(...$arguments);
        return [$instance, $handler->getMethod()];
    }
}
