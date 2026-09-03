<?php

require 'vendor/autoload.php';

$container = new Ludens\DependencyInjection\Container();
$container->load(__DIR__ . '/src/Routing/Configuration/services.sphp');

// Retrieve all the attributes from the controller to automatically construct the routes
$registerer = $container->get(Ludens\Routing\RoutesRegisterer::class);
$registerer->register();

try {
    $response = Ludens\Routing\Router::run(Ludens\Http\Request::fromGlobals());
    $response->send();
} catch (Ludens\Exceptions\RouteNotFoundException $e) {
    $response = new Ludens\Http\Response();
    $response->setBody($e->getMessage())->send();
}
