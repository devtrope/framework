<?php

namespace Ludens\Routing;

use Ludens\Contracts\HttpMethodAttributeInterface;
use Ludens\Routing\Support\Handler;
use ReflectionClass;

final class RoutesRegisterer
{
    public static function register(): void
    {
        $controllerFolder = 'src/Controller/';
        $files = glob($controllerFolder . '*.php');
        foreach ($files as $file) {
            $class = str_replace($controllerFolder, '', $file);
            $class = str_replace('.php', '', $class);
            $class = '\\Ludens\\Controller\\' . $class;
            $reflectionClass = new ReflectionClass($class);
            $methods = $reflectionClass->getMethods();
            foreach ($methods as $method) {
                $attributes = $method->getAttributes();
                foreach ($attributes as $attribute) {
                    $attributeInstance = $attribute->newInstance();
                    if (!$attributeInstance instanceof HttpMethodAttributeInterface) {
                        continue;
                    }
                    $handler = new Handler($class, $method->getName());
                    Route::add($attributeInstance->getHttpMethod(), $attributeInstance->getPath(), $handler);
                }
            }
        }
    }
}
