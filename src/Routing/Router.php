<?php

namespace Ludens\Routing;

final class Router
{
    public static function run(array $routes, string $path): void
    {
        if (isset($routes[$path])) {
            $handler = $routes[$path];
            if (is_array($handler)) {
                $class = $handler[0];
                $method = $handler[1];
                $instance = new $class();
                $handler = [$instance, $method];
            }
            call_user_func($handler);
            return;
        }

        foreach ($routes as $key => $handler) {
            $explodedKey = explode('/', trim($key, '/'));
            $explodedRequest = explode('/', trim($path, '/'));
            $arguments = [];

            if (count($explodedRequest) !== count($explodedKey)) {
                continue;
            }

            for ($i = 0; $i < count($explodedKey); $i++) {
                if (str_starts_with($explodedKey[$i], '{')) {
                    $arguments[] = $explodedRequest[$i];
                    continue;
                }

                if ($explodedRequest[$i] !== $explodedKey[$i]) {
                    continue 2;
                }
            }

            if (is_array($handler)) {
                $class = $handler[0];
                $method = $handler[1];
                $instance = new $class();
                $handler = [$instance, $method];
            }

            call_user_func_array($handler, $arguments);
            return;
        }

        call_user_func(function () {
            echo "404\n";
        });
    }
}
