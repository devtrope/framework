<?php

require 'vendor/autoload.php';

$request = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

$container = new Ludens\DependencyInjection\Container();
$container->load('src/config/services.php');

// Retrieve all the attributes from the controller to automatically construct the routes
$registerer = $container->get(Ludens\Routing\RoutesRegisterer::class);
$registerer->register();

try {
    Ludens\Routing\Router::run(
        Ludens\Routing\Route::getAllByRequestMethod($requestMethod),
        $request
    );
} catch (Ludens\Exceptions\RouteNotFoundException $e) {
    echo $e->getMessage() . "\n";
}
