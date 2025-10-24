<?php

namespace Ludens\Core;

class Application
{
    public static function init(\Ludens\Http\Request $request)
    {
        if ($_ENV['APP_ENVIRONMENT'] === 'production') {
            $cacheFile = CACHE_PATH . '/routes.php';

            if (! file_exists($cacheFile)) {
                if (! file_exists(ROUTES_PATH)) {
                    throw new \Exception('Routes file not found at ' . ROUTES_PATH);
                }
                
                require ROUTES_PATH;

                \Ludens\Routing\Route::cache();
            }

            $routes = require $cacheFile;
            \Ludens\Routing\Route::load($routes);
        }

        \Ludens\Routing\Router::dispatch($request);
    }
}
