<?php

$request = $_SERVER['REQUEST_URI'];
$routes = [
    '/' => function () {
        echo "Home Page\n";
    },
    '/test' => function () {
        echo "Test Page\n";
    },
    '/user/{username}' => function (string $username) {
        echo "Welcome {$username}";
    }
];

if (isset($routes[$request])) {
    call_user_func($routes[$request]);
} else {
    $found = false;
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
        $found = true;
    }

    if (false === $found) {
        die('404');
    }
}
