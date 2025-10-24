<?php

namespace Ludens\Core;

class Application
{
    /**
     * Initialize the application by loading routes and dispatching the request.
     * @param \Ludens\Http\Request $request
     * @throws \Exception
     * @return void
     */
    public static function init(\Ludens\Http\Request $request)
    {
        if ($_ENV['APP_ENVIRONMENT'] === 'production') {
            self::loadRoutesFromCache();
        }

         try {
            \Ludens\Routing\Router::dispatch($request);
        } catch (\Ludens\Exceptions\NotFoundException $e) {
            $response = new \Ludens\Http\Response();
            
            $response::render('errors/404', [
                'message' => $e->getMessage()
            ])
            ->setHeader('Content-Type', 'text/html; charset=UTF-8')
            ->setCode($e->getCode())
            ->send();
        }
    }

    /**
     * Loads the routes definitions from the cache file.
     * @throws \Exception
     * @return void
     */
    private static function loadRoutesFromCache(): void
    {
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
}
