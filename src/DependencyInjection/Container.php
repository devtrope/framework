<?php

namespace Ludens\DependencyInjection;

use Ludens\Configuration\ParametersResolver;
use Ludens\Exceptions\ConfigurationException;
use Ludens\Exceptions\InvalidConfigurationFileProvided;
use Ludens\Exceptions\MissingBoundValueException;
use Ludens\Sphp\Sphp;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use RuntimeException;

final class Container
{
    /**
     * @var array<string, string>
     */
    private array $bindings = [];

    /**
     * @param Sphp $sphp
     * @param ParametersResolver $parametersResolver
     */
    public function __construct(
        private Sphp $sphp = new Sphp(),
        private ParametersResolver $parametersResolver = new ParametersResolver()
    )
    {}

    /**
     * @param string $identifier
     * @throws MissingBoundValueException
     * @return object
     */
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
                
                if (false === isset($this->bindings[$dependency->getName()])) {
                    throw new MissingBoundValueException(\sprintf(
                        'No value provided for %s',
                        $dependency->getName()
                    ));
                }
                $arguments[] = $this->bindings[$dependency->getName()];
                continue;
            }
            $arguments[] = $this->get($dependencyTypeName);
        }

        if (false === class_exists($identifier)) {
            throw new RuntimeException(\sprintf(
                'Class %s does not exist',
                $identifier
            ));
        }

        $reflectionClass = new ReflectionClass($identifier);
        return $reflectionClass->newInstance(...$arguments);
    }

    /**
     * @param string $filename
     * @throws InvalidConfigurationFileProvided
     * @throws ConfigurationException
     * @return void
     */
    public function load(string $filename): void
    {
        if (false === file_exists($filename)) {
            throw new InvalidConfigurationFileProvided(\sprintf(
                "The configuration file %s does not exist",
                $filename
            ));
        }

        $configuration = $this->sphp->parse($filename);
        if (false === isset($configuration['services'])) {
            throw new ConfigurationException(\sprintf(
                'No services defined in %s',
                $filename
            ));
        }

        $services = $configuration['services'];
        if (false === \is_array($services)) {
            throw new ConfigurationException(\sprintf(
                'The \'services\' key is supposed to be an array in %s',
                $filename
            ));
        }

        
        foreach ($services as $service) {
            if (false === \is_array($service)) {
                continue;
            }

            $bind = $service['bind'];
            if (false === \is_array($bind)) {
                throw new ConfigurationException(\sprintf(
                    'The \'bind\' key must be an array in %s',
                    $filename
                ));
            }

            foreach ($bind as $key => $value) {
                if (false === \is_string($key) || false === \is_string($value)) {
                    continue;
                }
                $this->bindings[$key] = $this->parametersResolver->resolve($value);
            }
        }
    }

    /**
     * Check if the provided class has dependencies in its constructor and return them
     * if that's the case.
     *
     * @param string $identifier
     * @return ReflectionParameter[]
     */
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
