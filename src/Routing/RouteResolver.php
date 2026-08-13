<?php

namespace Ludens\Routing;

use Exception;

final class RouteResolver
{
    private array $parameters = [];

    public function resolve(array $routes, string $path): array
    {
        $result = $this->match($routes, $path);
        return [$result, $this->parameters];
    }

    private function match(array $routes, string $path): array
    {
        if (isset($routes[$path])) {
            return $routes[$path];
        }

        foreach ($routes as $key => $handler) {
            $explodedKey = explode('/', trim($key, '/'));
            $explodedPath = explode('/', trim($path, '/'));

            if (\count($explodedPath) !== \count($explodedKey)) {
                continue;
            }

            for ($i = 0; $i < \count($explodedKey); $i++) {
                if (str_starts_with($explodedKey[$i], '{')) {
                    $parameterKey = trim($explodedKey[$i], '{}');
                    $this->parameters[$parameterKey] = $explodedPath[$i];
                    continue;
                }

                if ($explodedPath[$i] !== $explodedKey[$i]) {
                    continue 2;
                }
            }

            return $handler;
        }

        throw new Exception("No route found for path {$path}");
    }
}
