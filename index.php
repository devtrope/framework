<?php

require 'vendor/autoload.php';

use Ludens\Sphp\Sphp;

$sphp = new Sphp();
var_dump($sphp->parse(__DIR__ . '/src/config/test.sphp'));
die;

$request = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

$container = new Ludens\DependencyInjection\Container();
$container->load(__DIR__ . '/src/config/services.php');

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
