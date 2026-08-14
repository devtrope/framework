<?php

namespace Ludens\Routing;

use Ludens\Contracts\HttpMethodAttributeInterface;
use Ludens\Exceptions\InvalidControllerFolderException;
use Ludens\Routing\Support\Handler;
use ReflectionClass;
use ReflectionMethod;

final class RoutesRegisterer
{
    // TODO: add the folder into a configuration file instead
    private const CONTROLLER_FOLDER = 'src/Controller/';

    // TODO: add the controller namespace into a configuration file instead
    private const CONTROLLER_NAMESPACE = '\\Ludens\\Controller\\';

    public function __construct(
        private readonly string $controllerFolder = self::CONTROLLER_FOLDER,
        private readonly string $controllerNamespace = self::CONTROLLER_NAMESPACE
    )
    {}

    public function register(): void
    {
        foreach ($this->retrieveControllersFiles() as $file) {
            $attributes = $this->getAttributesByMethod($this->formatClassname($file));
            foreach ($attributes as $attribute) {
                $handler = new Handler($attribute['classname'], $attribute['method']);
                Route::add($attribute['instance']->getHttpMethod(), $attribute['instance']->getPath(), $handler);
            }
        }
    }

    private function getAttributesByMethod(string $classname): array
    {
        $methodAttributes = [];
        $reflectionClass = new ReflectionClass($classname);
        $methods = $reflectionClass->getMethods();
        foreach ($methods as $method) {
            /**
             * @var ReflectionMethod $method
             */
            $attributes = $method->getAttributes();
            foreach ($attributes as $attribute) {
                $attributeInstance = $attribute->newInstance();
                if (!$attributeInstance instanceof HttpMethodAttributeInterface) {
                    continue;
                }
                $methodAttributes[] = ['classname' => $classname, 'method' => $method->getName(), 'instance' => $attributeInstance];
            }
        }
        return $methodAttributes;
    }

    private function retrieveControllersFiles(): array
    {
        if (false === is_dir($this->controllerFolder)) {
            throw new InvalidControllerFolderException(
                "The controller folder {$this->controllerFolder} does not exist"
            );
        }
        return glob("{$this->controllerFolder}*.php");
    }

    private function formatClassname(string $file): string
    {
        $classname = str_replace($this->controllerFolder, '', $file);
        $classname = str_replace('.php', '', $classname);
        return "{$this->controllerNamespace}{$classname}";
    }
}
