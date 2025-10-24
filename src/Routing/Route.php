<?php

namespace Ludens\Routing;

class Route
{
    protected static array $routes = [];

    /**
     * Registers the routes for the get requests.
     * @param string $path
     * @param array $handler
     * @return void
     */
    public static function get(string $path , array $handler): void
    {
        self::assignRoute('GET', $path, $handler);
    }

    /**
     * Registers the routes for the post requests.
     * @param string $path
     * @param array $handler
     * @return void
     */
    public static function post(string $path , array $handler): void
    {
        self::assignRoute('POST', $path, $handler);
    }

    /**
     * Registers the route for a given request method.
     * @param string $method
     * @param string $path
     * @param array $handler
     * @return void
     */
    private static function assignRoute(string $method, string $path, array $handler): void
    {
        self::$routes[$method][$path] = $handler;
    }

    /**
     * Keeps the routes in a cache file (only in production).
     * @return void
     */
    public static function cache(): void
    {
        $export = var_export(self::$routes, true);
        $cacheFile = CACHE_PATH . '/routes.php';
        $content = "<?php\n\nreturn " . $export . ";\n";
        file_put_contents($cacheFile, $content);
    }

    /**
     * Load the routes from a cached file (only in production).
     * @param array $routes
     * @return void
     */
    public static function load(array $routes): void
    {
        self::$routes = $routes;
    }

    /**
     * Returns the list of routes for a given request method.
     * @param string $requestMethod
     * @return array
     */
    public static function list(string $requestMethod): array
    {
        if (! isset(self::$routes[$requestMethod])) {
            return [];
        }

        return self::$routes[$requestMethod];
    }
}
