<?php

namespace Ludens\Core;

/**
 * Main application class responsible for initializing the application,
 * loading routes, and managing global paths.
 * 
 * @package Ludens\Core
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class Application
{
    private static ?string $templatesPath = null;
    private static ?string $routesPath = null;
    private static ?string $cachePath = null;

    /**
     * Initialize the application by loading routes and dispatching the request.
     * @param \Ludens\Http\Request $request
     * @throws \Exception
     * @return void
     */
    public function init(\Ludens\Http\Request $request): void
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
     * Define the application global paths.
     * @param array $paths
     * @return Application
     */
    public function withPaths(string $templates, string $routes, string $cache): self
    {
        self::$templatesPath = $templates;
        self::$routesPath = $routes;
        self::$cachePath = $cache;

        return $this;
    }

    public static function templates(): string
    {
        return self::$templatesPath;
    }

    public static function routes(): string
    {
        return self::$routesPath;
    }

    public static function cache(): string
    {
        return self::$cachePath;
    }

    /**
     * Loads the routes definitions from the cache file.
     * @throws \Exception
     * @return void
     */
    private static function loadRoutesFromCache(): void
    {
        $cacheFile = self::cache() . '/routes.php';

        if (! file_exists($cacheFile)) {
            if (! file_exists(self::routes())) {
                throw new \Exception('Routes file not found at ' . self::routes());
            }
            
            require self::routes();

            \Ludens\Routing\Route::cache();
        }

        $routes = require $cacheFile;
        \Ludens\Routing\Route::load($routes);
    }
}
