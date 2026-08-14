<?php

namespace Ludens\Routing;

use Ludens\Contracts\HttpMethodAttributeInterface;
use Ludens\Routing\Support\MethodAttribute;
use ReflectionClass;
use ReflectionMethod;

final class MethodAttributesResolver
{
    public function getAllByClassName(string $classname): array
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
                $methodAttributes[] = new MethodAttribute($classname, $method->getName(), $attributeInstance);
            }
        }
        return $methodAttributes;
    }
}
