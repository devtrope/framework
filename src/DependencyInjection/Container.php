<?php

namespace Ludens\DependencyInjection;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

final class Container
{
    private array $bindings = [];

    public function get(string $identifier): mixed
    {
        $arguments = [];
        $dependencies = $this->resolveDependencies($identifier);
        /**
         * @var ReflectionParameter $dependency
         */
        foreach ($dependencies as $dependency) {
            /**
             * @var ReflectionNamedType
             */
            $dependencyType = $dependency->getType();
            $dependencyTypeName = $dependencyType->getName();
            if (false === class_exists($dependencyTypeName)) {
                if ($dependency->isDefaultValueAvailable()) {
                    $arguments[] = $dependency->getDefaultValue();
                    continue;
                }
                // TODO: Verify if the binding exists and add the class name in the identifier
                $arguments[] = $this->bindings[$dependency->getName()];
                continue;
            }
            $arguments[] = $this->get($dependencyTypeName);
        }
        $reflectionClass = new ReflectionClass($identifier);
        return $reflectionClass->newInstance(...$arguments);
    }

    public function bind(string $identifier, mixed $value): void
    {
        $this->bindings[$identifier] = $value;
    }

    private function resolveDependencies(string $identifier): array
    {
        if (false === class_exists($identifier)) {
            return [];
        }

        $reflectionClass = new ReflectionClass($identifier);
        $constructor = $reflectionClass->getConstructor();
        if (null === $constructor) {
            return [];
        }

        return $constructor->getParameters();
    }
}
