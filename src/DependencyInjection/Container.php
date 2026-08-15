<?php

namespace Ludens\DependencyInjection;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

final class Container
{
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
                $arguments[] = $dependency->getDefaultValue();
                continue;
            }
            $arguments[] = $this->get($dependencyTypeName);
        }
        $reflectionClass = new ReflectionClass($identifier);
        return $reflectionClass->newInstance(...$arguments);
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
