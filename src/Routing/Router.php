<?php

namespace Ludens\Routing;

use Exception;

final class Router
{
    public static function run(array $routes, string $path): void
    {
        if (isset($routes[$path])) {
            $handler = (new self)->extract($routes[$path]);
            \call_user_func($handler);
            return;
        }

        foreach ($routes as $key => $handler) {
            $explodedKey = explode('/', trim($key, '/'));
            $explodedRequest = explode('/', trim($path, '/'));
            $arguments = [];

            if (\count($explodedRequest) !== \count($explodedKey)) {
                continue;
            }

            for ($i = 0; $i < \count($explodedKey); $i++) {
                if (str_starts_with($explodedKey[$i], '{')) {
                    $arguments[] = $explodedRequest[$i];
                    continue;
                }

                if ($explodedRequest[$i] !== $explodedKey[$i]) {
                    continue 2;
                }
            }

            $handler = (new self)->extract($handler);
            \call_user_func_array($handler, $arguments);
            return;
        }

        throw new Exception("No route found for path {$path}");
    }

    private function extract(array $handler): array
    {
        $class = $handler[0];
        $method = $handler[1];
        $instance = new $class();
        return [$instance, $method];
    }
}
