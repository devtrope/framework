<?php

require 'vendor/autoload.php';

use Ludens\Routes;

$request = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

Routes::add('GET', '/', [Ludens\Home::class, 'index']);

Routes::add('GET', '/test', function () {
    echo "Test Page\n";
});

Routes::add('GET', '/user/{username}', [Ludens\Home::class, 'user']);

Routes::add('POST', '/', function () {
    echo "Home Page but in POST\n";
});

function run(array $routes, string $request): void
{
    if (isset($routes[$request])) {
        $handler = $routes[$request];
        if (is_array($handler)) {
            $class = $handler[0];
            $method = $handler[1];
            $instance = new $class();
            $callback = [$instance, $method];
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
            $callback = [$instance, $method];
        }

        call_user_func_array($handler, $arguments);
        return;
    }

    call_user_func(function () {
        echo "404\n";
    });
}

run(Routes::getAll($requestMethod), $request);
