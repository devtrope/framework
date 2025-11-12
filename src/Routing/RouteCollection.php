<?php

namespace Ludens\Routing;

use Exception;
use RuntimeException;
use Ludens\Core\Application;
use Ludens\Routing\Support\Handler;

/**
 * Collection of all registered routes in the application.
 *
 * Manages the storage, retrieval, caching and loading of route definitions.
 *
 * @package Ludens\Routing
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class RouteCollection
{
    private static array $routes = [];

    /**
     * Register a route for a specific HTTP method and path.
     *
     * @param string $method The HTTP method
     * @param string $path The route path
     * @param Handler $handler The route handler
     * @param array $middleware Middleware classes
     * @return void
     *
     * @throws Exception If the route is already registered
     */
    public static function add(string $method, string $path, Handler $handler, array $middleware): void
    {
        if (isset(self::$routes[$method][$path])) {
            throw new Exception("Route {$method} {$path} is already registered");
        }

        self::$routes[$method][$path] = [
            'handler' => $handler,
            'middleware' => $middleware
        ];
    }

    /**
     * Get all routes registered for a specific HTTP method.
     *
     * Returns an associative array where keys are paths and values are handlers.
     *
     * @param string $requestMethod The HTTP method (GET, POST, PUT, PATCH, DELETE)
     * @return array Array of routes [path => handler] or empty array if none found
     */
    public static function getRoutesForMethod(string $requestMethod): array
    {
        if (! isset(self::$routes[$requestMethod])) {
            return [];
        }

        return self::$routes[$requestMethod];
    }

    /**
     * Get all registered routes.
     *
     * @return array
     */
    public static function all(): array
    {
        return self::$routes;
    }

    /**
     * Check if a route exists for a given method and path.
     *
     * @param string $method
     * @param string $path
     * @return bool
     */
    public static function has(string $method, string $path): bool
    {
        return isset(self::$routes[$method][$path]);
    }

    /**
     * Get a specific route handler.
     *
     * @param string $method
     * @param string $path
     * @return Handler|null
     */
    public static function get(string $method, string $path): ?Handler
    {
        return self::$routes[$method][$path] ?? null;
    }

    /**
     * Clear all registered routes.
     *
     * Useful for testing purposes.
     *
     * @return void
     */
    public static function clear(): void
    {
        self::$routes = [];
    }

    /**
     * Count total number of routes.
     *
     * @return int
     */
    public static function count(): int
    {
        $count = 0;

        foreach (self::$routes as $routes) {
            $count += count($routes);
        }

        return $count;
    }

    /**
     * Save all registered routes to a cache file.
     *
     * Exports the routes array to a PHP file for faster loading in production.
     * Should only be called in production environments after all routes are registered.
     *
     * @return void
     *
     * @throws RuntimeException If the cache file cannot be written
     */
    public static function cache(): void
    {
        // Convert handlers as array for export
        $routesForExport = [];

        foreach (self::$routes as $method => $routes) {
            foreach ($routes as $path => $handler) {
                $routesForExport[$method][$path] = $handler->toArray();
            }
        }

        $export = var_export($routesForExport, true);
        $cacheFile = Application::getInstance()->path('cache') . '/routes.php';
        $content = "<?php\n\nreturn " . $export . ";\n";

        if (! file_put_contents($cacheFile, $content)) {
            throw new RuntimeException("Cannot write routes cache file");
        }
    }

    /**
     * Load the routes from a cached routes array.
     *
     * Replaces the current routes with pre-cached routes for improved performance.
     * Typically used in production to avoid re-registering routes on every request.
     *
     * @param array $routes The cached routes array
     * @return void
     */
    public static function load(array $routes): void
    {
        // Convert array to handlers
        foreach ($routes as $method => $methodRoutes) {
            foreach ($methodRoutes as $path => $handler) {
                self::$routes[$method][$path] = Handler::fromArray($handler);
            }
        }
    }
}
