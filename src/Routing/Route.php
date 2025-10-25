<?php

namespace Ludens\Routing;

use Exception;
use Ludens\Core\Application;
use Ludens\Http\HttpMethod;
use RuntimeException;

/**
 * Route registration and management class.
 * 
 * Provides a fluent interface for defining application routes with their HTTP methods
 * and handlers. Supports route caching for production environments.
 * 
 * @package Ludens\Routing
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 * 
 */
class Route
{
    protected static array $routes = [];
    private array $handler;
    private string $path;

    /**
     * Create a new Route instance.
     *
     * @param array $handler The route handler as [ControllerClass::class, 'methodName']
     */
    public function __construct(array $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Create a new route instance with the given handler.
     *
     * Static factory method to start the fluent route definition chain.
     *
     * @param array $handler The route handler as [ControllerClass::class, 'methodName']
     * @return Route
     */
    public static function call(array $handler): self
    {
        return new self($handler);
    }

    /**
     * Define the URL path for this route.
     *
     * @param string $path The URL path (e.g., '/users', '/api/posts/{id}')
     * @return Route
     */
    public function when(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Register this route for GET requests.
     *
     * @return Route
     */
    public function onGet(): self
    {
        $this->register(HttpMethod::GET);
        return $this;
    }

    /**
     * Register this route for POST requests.
     *
     * @return Route
     */
    public function onPost(): self
    {
        $this->register(HttpMethod::POST);
        return $this;
    }

    /**
     * Register this route for PUT requests.
     *
     * @return Route
     */
    public function onPut(): self
    {
        $this->register(HttpMethod::PUT);
        return $this;
    }

    /**
     * Register this route for PATCH requests.
     *
     * @return Route
     */
    public function onPatch(): self
    {
        $this->register(HttpMethod::PATCH);
        return $this;
    }

    /**
     * Register this route for DELETE requests.
     *
     * @return Route
     */
    public function onDelete(): self
    {
        $this->register(HttpMethod::DELETE);
        return $this;
    }

    /**
     * Register this route for a given HTTP method.
     *
     * Stores the route in the static routes array and prevents duplicate registrations.
     *
     * @param HttpMethod $method The HTTP method for this route
     * @return void
     * @throws Exception If a route with the same method and path already exists
     */
    private function register(HttpMethod $method): void
    {
        $methodValue = $method->value;

        if (isset(self::$routes[$methodValue][$this->path])) {
            throw new Exception("Route {$methodValue} {$this->path} is already registered");
        }

        self::$routes[$methodValue][$this->path] = $this->handler;
    }

    /**
     * Save all registered routes to a cache file.
     *
     * Exports the routes array to a PHP file for faster loading in production.
     * Should only be called in production environments after all routes are registered.
     *
     * @return void
     * @throws RuntimeException If the cache file cannot be written
     */
    public static function cache(): void
    {
        $export = var_export(self::$routes, true);
        $cacheFile = Application::cache() . '/routes.php';
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
        self::$routes = $routes;
    }

    /**
     * Get all routes registered for a specific HTTP method.
     *
     * Returns an associative array where keys are paths and values are handlers.
     *
     * @param string $requestMethod The HTTP method (GET, POST, PUT, PATCH, DELETE)
     * @return array Array of routes [path => handler] or empty array if none found
     */
    public static function list(string $requestMethod): array
    {
        if (! isset(self::$routes[$requestMethod])) {
            return [];
        }

        return self::$routes[$requestMethod];
    }
}
