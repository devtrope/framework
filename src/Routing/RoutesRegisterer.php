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

    public function register(): void
    {
        foreach ($this->retrieveControllersFiles() as $file) {
            $classname = $this->formatClassname($file);
            $reflectionClass = new ReflectionClass($classname);
            $methods = $reflectionClass->getMethods();
            foreach ($methods as $method) {
                $attributes = $method->getAttributes();
                foreach ($attributes as $attribute) {
                    $attributeInstance = $attribute->newInstance();
                    if (!$attributeInstance instanceof HttpMethodAttributeInterface) {
                        continue;
                    }
                    $handler = new Handler($classname, $method->getName());
                    Route::add($attributeInstance->getHttpMethod(), $attributeInstance->getPath(), $handler);
                }
            }
        }
    }

    private function retrieveControllersFiles(): array
    {
        // String interpolation doesn't want to work with 'self', so I've got to do this kind of useless bypass
        $controllerFolder = self::CONTROLLER_FOLDER;
        if (false === is_dir(self::CONTROLLER_FOLDER)) {
            throw new InvalidControllerFolderException(
                "The controller folder {$controllerFolder} does not exist"
            );
        }
        return glob("{$controllerFolder}*.php");
    }

    private function formatClassname(string $file): string
    {
        $classname = str_replace(self::CONTROLLER_FOLDER, '', $file);
        $classname = str_replace('.php', '', $classname);
        // Again with the string interpolation problem...
        $controllerNamespace = self::CONTROLLER_NAMESPACE;
        return "{$controllerNamespace}{$classname}";
    }
}
