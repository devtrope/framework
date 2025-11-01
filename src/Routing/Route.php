<?php

namespace Ludens\Routing;

use Exception;
use RuntimeException;
use Ludens\Http\HttpMethod;
use Ludens\Routing\Support\Handler;

/**
 * Route registration and management class.
 *
 * Provides a fluent interface for defining application routes with their HTTP methods
 * and handlers. Supports route caching for production environments.
 *
 * @package Ludens\Routing
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class Route
{
    protected static array $routes = [];
    private Handler $handler;
    private string $path;

    /**
     * Create a new Route instance.
     *
     * @param Handler $handler The route handler as [ControllerClass::class, 'methodName']
     */
    public function __construct(Handler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Create a new route instance with the given handler.
     *
     * Static factory method to start the fluent route definition chain.
     *
     * @param array|Handler $handler The route handler as [ControllerClass::class, 'methodName']
     * @return Route
     */
    public static function call(array|Handler $handler): self
    {
        if (is_array($handler)) {
            $handler = Handler::fromArray($handler);
        }

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
     *
     * @throws Exception If a route with the same method and path already exists
     */
    private function register(HttpMethod $method): void
    {
        RouteCollection::add($method->value, $this->path, $this->handler);
    }

    /**
     * Cache all registered routes.
     *
     * @return void
     *
     * @throws RuntimeException If the cache file cannot be written
     */
    public static function cache(): void
    {
        RouteCollection::cache();
    }

    /**
     * Load the routes from a cached routes array.
     *
     * @param array $routes The cached routes array
     * @return void
     */
    public static function load(array $routes): void
    {
        RouteCollection::load($routes);
    }
}
