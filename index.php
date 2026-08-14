<?php

require 'vendor/autoload.php';

$request = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Retrieve all the attributes from the controller to automatically construct the routes
$registerer = new Ludens\Routing\RoutesRegisterer();
$registerer->register();

try {
    Ludens\Routing\Router::run(
        Ludens\Routing\Route::getAllByRequestMethod($requestMethod),
        $request
    );
} catch (Ludens\Exceptions\RouteNotFoundException $e) {
    echo $e->getMessage() . "\n";
}
