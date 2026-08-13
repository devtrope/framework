<?php

require 'vendor/autoload.php';

use Ludens\Contracts\HttpMethodAttributeInterface;
use Ludens\Routing\Route;

$request = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Retrieve all the attributes from the controller to automatically construct the routes
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
            Route::add($attributeInstance->getHttpMethod(), $attributeInstance->getPath(), ['controller' => $class, 'method' => $method->getName()]);
        }
    }
}

try {
    Ludens\Routing\Router::run(
        Route::getAllByRequestMethod($requestMethod),
        $request
    );
} catch (Ludens\Exceptions\RouteNotFoundException $e) {
    echo $e->getMessage() . "\n";
}
