<?php

namespace Ludens\Routing;

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
        $class = $handler[0];
        $method = $handler[1];
        $instance = new $class();
        return [$instance, $method];
    }
}
