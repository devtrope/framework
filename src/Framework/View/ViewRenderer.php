<?php

namespace Ludens\Framework\View;

use Exception;
use Ludens\Core\Application;
use Ludens\Exceptions\ConfigurationException;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

/**
 * View renderer using Twig template engine.
 *
 * Handles both application views and framework error pages with fallback system.
 *
 * @package Ludens\Framework\View
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class ViewRenderer
{
    private static ?Environment $twig = null;

    /**
     * Initialize Twig environment with fallback system.
     *
     * @return void
     */
    public static function initialize(): void
    {
        if (self::$twig !== null) {
            return;
        }

        $app = Application::getInstance();

        $loaders = [];

        $appTemplatesPath = $app->path('templates');
        if (is_dir($appTemplatesPath)) {
            $loaders[] = new FilesystemLoader($appTemplatesPath);
        }

        $frameworkTemplatesPath = dirname(__DIR__) . '/View/templates';
        $loaders[] = new FilesystemLoader($frameworkTemplatesPath);

        $loader = count($loaders) > 1 ? new ChainLoader($loaders) : $loaders[0];

        $cache = $app->config('twig.cache');
        if ($cache) {
            $cache = $app->path('cache');
        }

        self::$twig = new Environment($loader, [
            'cache' => $cache,
            'debug' => $app->config('twig.debug'),
            'auto_reload' => $app->config('twig.auto_reload'),
            'strict_variables' => $app->config('twig.stric_variables')
        ]);

        if ($app->config('twig.debug')) {
            self::$twig->addExtension(new DebugExtension());
        }

        self::registerExtensions();
    }

    /**
     * Get the Twig environment instance.
     *
     * @return Environment
     */
    public static function getInstance(): Environment
    {
        if (self::$twig === null) {
            self::initialize();
        }

        /**
        * @var Environment
        */
        return self::$twig;
    }

    /**
     * Render a template.
     *
     * @param string $template Template name (e.g., 'users/index.twig')
     * @param array $data Data to pass to the template
     * @return string Rendered HTML
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public static function render(string $template, array $data = []): string
    {
        return self::getInstance()->render($template, $data);
    }

    /**
     * Register custom Twig extensions and functions.
     *
     * @return void
     */
    private static function registerExtensions(): void
    {
        /**
         * @var Environment
         */
        $twig = self::$twig;

        $twig->addFunction(new \Twig\TwigFunction('asset', function (string $path) {
            $app = Application::getInstance();
            $baseUrl = $app->config('app.url', '');
            if (! is_string($baseUrl)) {
                throw ConfigurationException::missingAppUrl();
            }

            return rtrim($baseUrl, '/') . '/assets/' . ltrim($path, '/');
        }));

        $twig->addFunction(new \Twig\TwigFunction('config', function (string $key, ?string $default = null) {
            return Application::getInstance()->config($key, $default);
        }));

        $twig->addFunction(new \Twig\TwigFunction('error', function (string $field) {
            return \error($field);
        }));

        $twig->addFunction(new \Twig\TwigFunction('old', function (string $field) {
            return \old($field);
        }));

        $twig->addFunction(new \Twig\TwigFunction('flash', function (string $key) {
            return \flash($key);
        }));
    }
}
