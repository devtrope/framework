<?php

namespace Ludens\DependencyInjection;

use Ludens\Exceptions\InvalidConfigurationFileProvided;
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

    public function load(string $filename): void
    {
        if (false === file_exists($filename)) {
            throw new InvalidConfigurationFileProvided(
                "The configuration file {$filename} does not exist"
            );
        }

        $configuration = require $filename;
        foreach ($configuration as $service) {
            if (isset($service['bind'])) {
                foreach ($service['bind'] as $key => $value) {
                    $this->bindings[$key] = $value;
                }
            }
        }
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
