<?php

namespace Ludens\Framework\View;

use Ludens\Core\Application;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

/**
 * View renderer using Twig template engine.
 * 
 * @package Ludens\Framework\View
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class ViewRenderer
{
    private static ?Environment $twig = null;

    public static function initialize(): void
    {
        if (self::$twig !== null) {
            return;
        }

        $app = Application::getInstance();
        $loader = new FilesystemLoader($app->path('templates'));

        self::$twig = new Environment($loader, [
            'cache' => $app->isProduction() ? $app->path('cache') . '/views' : false,
            'debug' => ! $app->isProduction(),
            'auto_reload' => ! $app->isProduction(),
            'strict_variables' => ! $app->isProduction()
        ]);

        if (! $app->isProduction()) {
            self::$twig->addExtension(new DebugExtension());
        }

        self::registerExtensions();
    }

    public static function getInstance(): Environment
    {
        if (self::$twig === null) {
            self::initialize();
        }
        
        return self::$twig;
    }

    public static function render(string $template, array $data = []): string
    {
        return self::getInstance()->render($template, $data);
    }

    private static function registerExtensions(): void
    {
        $twig = self::$twig;

        $twig->addFunction(new \Twig\TwigFunction('asset', function (string $path) {
            $app = Application::getInstance();
            $baseUrl = $app->config('app.url', '');
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
