<?php

require 'vendor/autoload.php';

use Ludens\Routes;

$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

$routes = new Routes();

$routes->add('GET', '/', function () {
    echo "Home Page\n";
});

$routes->add('GET', '/test', function () {
    echo "Test Page\n";
});

$routes->add('GET', '/user/{username}', function (string $username) {
    echo "Welcome {$username}\n";
});

$routes->add('POST', '/', function () {
    echo "Home Page but in POST\n";
});

function run(array $routes, string $request): void
{
    if (isset($routes[$request])) {
        call_user_func($routes[$request]);
        return;
    }

    foreach ($routes as $key => $callback) {
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

        call_user_func_array($callback, $arguments);
        return;
    }

    call_user_func(function () {
        echo "404\n";
    });
}

run($routes->getAll($method), $request);
