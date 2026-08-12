<?php

require 'vendor/autoload.php';

use Ludens\Routes;

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
            Routes::add($attributeInstance->getHttpMethod(), $attributeInstance->getPath(), [$class, $method->getName()]);
        }
    }
}

function run(array $routes, string $request): void
{
    if (isset($routes[$request])) {
        $handler = $routes[$request];
        if (is_array($handler)) {
            $class = $handler[0];
            $method = $handler[1];
            $instance = new $class();
            $handler = [$instance, $method];
        }
        call_user_func($handler);
        return;
    }

    foreach ($routes as $key => $handler) {
        $explodedKey = explode('/', trim($key, '/'));
        $explodedRequest = explode('/', trim($request, '/'));
        $arguments = [];

        if (count($explodedRequest) !== count($explodedKey)) {
            continue;
        }

        for ($i = 0; $i < count($explodedKey); $i++) {
            if (str_starts_with($explodedKey[$i], '{')) {
                $arguments[] = $explodedRequest[$i];
                continue;
            }

            if ($explodedRequest[$i] !== $explodedKey[$i]) {
                continue 2;
            }
        }

        if (is_array($handler)) {
            $class = $handler[0];
            $method = $handler[1];
            $instance = new $class();
            $handler = [$instance, $method];
        }

        call_user_func_array($handler, $arguments);
        return;
    }

    call_user_func(function () {
        echo "404\n";
    });
}

run(Routes::getAll($requestMethod), $request);
