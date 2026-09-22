<?php

namespace Ludens\Routing;

use Closure;
use Ludens\Routing\Support\Handler;
use Ludens\Routing\Support\InstantiatedHandler;
use ReflectionClass;
use RuntimeException;

final class ResolvedRoute
{
    /**
     * @var InstantiatedHandler
     */
    private InstantiatedHandler $handler;

    /**
     * @var array<string, mixed>
     */
    private array $parameters;

    /**
     * @param Handler $handler
     * @param array<string, mixed> $parameters
     */
    public function __construct(Handler $handler, array $parameters)
    {
        $this->handler = $this->instantiate($handler);
        $this->parameters = $parameters;
    }

    /**
     * @return InstantiatedHandler
     */
    public function getHandler(): InstantiatedHandler
    {
        return $this->handler;
    }

    /**
     * @return array<string, mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param Handler $handler
     * @return InstantiatedHandler
     */
    private function instantiate(Handler $handler): InstantiatedHandler
    {
        if (false === class_exists($handler->getController())) {
            throw new RuntimeException(sprintf(
                'Controller class %s does not exist',
                $handler->getController()
            ));
        }
        $reflectionClass = new ReflectionClass($handler->getController());

        $constructor = $reflectionClass->getConstructor();
        $arguments = [];
        if (null !== $constructor) {
            $arguments = $constructor->getParameters();
        }

        $instance = $reflectionClass->newInstance(...$arguments);
        return new InstantiatedHandler($instance, $handler->getMethod());
    }
}
