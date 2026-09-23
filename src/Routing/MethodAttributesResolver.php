<?php

namespace Ludens\Routing;

use Ludens\Contracts\HttpMethodAttributeInterface;
use Ludens\Routing\Support\MethodAttribute;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;

final class MethodAttributesResolver
{
    /**
     * Retrieve all the attributes from the provided controller class name and
     * returns only those matching a valid Http Method.
     *
     * @param string $classname
     * @return MethodAttribute[]
     */
    public function getAllByClassName(string $classname): array
    {
        if (false === class_exists($classname)) {
            throw new RuntimeException(sprintf(
                'Class %s does not exist',
                $classname
            ));
        }

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
