<?php

namespace Ludens\Routing;

use Ludens\Core\Application;

class Route
{
    protected static array $routes = [];
    private array $handler;
    private string $path;
    private const GET = 'GET';
    private const POST = 'POST';

    public function __construct(array $handler)
    {
        $this->handler = $handler;
    }

    public static function call(array $handler): self
    {
        return new self($handler);
    }

    public function when(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function onGet(): self
    {
        $this->register(self::GET);
        return $this;
    }

    public function onPost(): self
    {
        $this->register(self::POST);
        return $this;
    }

    /**
     * Registers the route for a given request method.
     * @param string $method
     * @return void
     */
    private function register(string $method): void
    {
        if (isset(self::$routes[$method][$this->path])) {
            throw new \Exception("Route {$method} {$this->path} is already registered");
        }
        
        self::$routes[$method][$this->path] = $this->handler;
    }

    /**
     * Keeps the routes in a cache file (only in production).
     * @return void
     */
    public static function cache(): void
    {
        $export = var_export(self::$routes, true);
        $cacheFile = Application::cache() . '/routes.php';
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
