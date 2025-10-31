<?php

namespace Ludens\Core;

use Whoops\Run;
use Dotenv\Dotenv;
use Ludens\Http\Request;
use Ludens\Routing\Route;
use Ludens\Routing\Router;
use Whoops\Handler\PrettyPageHandler;
use Ludens\Exceptions\NotFoundException;
use Ludens\Http\Responses\ErrorResponse;

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
    private static ?Application $instance = null;
    private array $paths = [];
    private array $config = [];

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct() {}

    /**
     * Prevent cloning of the instance.
     * 
     * @return void;
     */
    private function __clone(): void {}

    /**
     * Prevent unserializing of the instance.
     *
     * @return never
     *
     * @throws \Exception
     */
    public function __wakeup(): void
    {
        throw new \Exception("Cannot unserialize singleton.");
    }

    /**
     * Get the singleton instance.
     *
     * @return Application
     */
    public static function getInstance(): Application
    {
        if (self::$instance === null) {
            self::$instance = new Application();
        }

        return self::$instance;
    }

    /**
     * Set application paths from configuration.
     *
     * @param array $paths
     * @return Application
     */
    public function usePathsFrom(array $paths): self
    {
        $this->paths = $paths;
        return $this;
    }

    /**
     * Get a specific path.
     *
     * @param string $key
     * @return string
     *
     * @throws \InvalidArgumentException If the path does not exist
     */
    public function path(string $key): string
    {
        if (! isset($this->paths[$key])) {
            throw new \InvalidArgumentException(
                "Path [{$key}] not found."
            );
        }

        return $this->paths[$key];
    }

    /**
     * Load environment variables from .env file.
     *
     * @param string $path
     * @return Application
     *
     * @throws \Exception If the .env file does not exist
     */
    public function loadEnvironmentFrom(string $path): self
    {
        if (! file_exists($path)) {
            throw new \Exception(
                ".env file is missing at: {$path}"
            );
        }

        $dotenv = Dotenv::createImmutable(dirname($path));
        $dotenv->load();

        return $this;
    }

    /**
     * Load all configuration files from config directory.
     *
     * @return Application
     */
    public function loadConfiguration(): self
    {
        $configurationPath = $this->path('config');

        foreach (glob($configurationPath . '/*.php') as $file) {
            $key = basename($file, '.php');
            $this->config[$key] = require $file;
        }

        return $this;
    }

    /**
     * Get a configuration value using dot notation.
     *
     * @param string $key Configuration key (e.g., 'app.name')
     * @param mixed $default Default value if not found
     * @return string|null|bool|array
     */
    public function config(string $key, string|null|array $default = null): string|null|bool|array
    {
        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (! isset($value[$segment])) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * Configure exception and error handling (Whoops in dev).
     *
     * @return Application
     */
    public function configureExceptionHandling(): self
    {
        if (! $this->isProduction()) {
            $whoops = new Run();
            $whoops->pushHandler(new PrettyPageHandler());
            $whoops->register();
        }

        return $this;
    }

    /**
     * Load routes from the routes file.
     *
     * @return Application
     *
     * @throws \Exception If the routes file does not exist
     */
    public function loadRoutes(): self
    {
        $routesFile = $this->path('routes');

        if (!file_exists($routesFile)) {
            throw new \Exception("Routes file not found at: {$routesFile}");
        }

        require $routesFile;

        return $this;
    }

    /**
     * Loads the routes definitions from the cache file
     * (or create cache if missing).
     *
     * @return Application
     *
     * @throws \Exception
     */
    public function loadRoutesFromCache(): self
    {
        $cacheFile = $this->path('cache') . '/routes.php';

        if (! file_exists($cacheFile)) {
            $this->loadRoutes();
            Route::cache();
            return $this;
        }

        $routes = require $cacheFile;
        Route::load($routes);
        return $this;
    }

    /**
     * Load routes based on environment.
     * 
     * Uses cache in production, fresh routes in development.
     *
     * @return Application
     */
    public function bootRoutes(): self
    {
        if ($this->isProduction()) {
            return $this->loadRoutesFromCache();
        }

        return $this->loadRoutes();
    }

    /**
     * Boot all registered service providers.
     *
     * @return Application
     * 
     * @throws \Exception
    */
    public function bootProviders(): self
    {
        $providers = $this->config('providers.providers', []);

        foreach ($providers as $providerClass) {
            if (! class_exists($providerClass)) {
                throw new \Exception(
                    "Provider [{$providerClass}] not found."
                );
            }

            $provider = new $providerClass();

            if (! $provider instanceof \Ludens\Support\ServiceProvider) {
                throw new \Exception(
                    "Provider [$providerClass}] must implement ServiceProvider interface."
                );
            }

            $provider->boot();
        }

        return $this;
    }

    /**
     * Check if application is running in production.
     *
     * @return bool
     */
    public function isProduction(): bool
    {
        return $this->config('app.environment') === 'production';
    }

    /**
     * Initialize and handle the incoming request.
     *
     * @param Request $request
     * @return void
     */
    public function init(Request $request): void
    {
        try {
            Router::dispatch($request);
        } catch (NotFoundException $e) {
            $this->handleNotFoundException($e);
        }
    }

    /**
     * Handle 404 Not Found exceptions.
     *
     * @param \Ludens\Exceptions\NotFoundException $e
     * @return void
     */
    private function handleNotFoundException(NotFoundException $e): void
    {
        ErrorResponse::notFound($e->getMessage())->send();
    }
}
