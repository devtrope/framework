<?php

namespace Ludens\DependencyInjection;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

final class Container
{
    private array $instances = [];

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
            $dependencyClass = $dependencyType->getName();
            $arguments[] = $dependencyClass;
            if (false === isset($this->instances[$dependencyClass])) {
                $this->get($dependencyClass);
            }
        }
        $instances = $this->getInstancesByArguments($arguments);
        $reflectionClass = new ReflectionClass($identifier);
        $this->instances[$identifier] = $reflectionClass->newInstance(...$instances);
        return $this->instances[$identifier];
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

    private function getInstancesByArguments(array $arguments): array
    {
        $instances = [];
        foreach ($arguments as $argument) {
            $instances[] = $this->instances[$argument];
        }
        return $instances;
    }
}
