<?php

namespace Ludens\Routing;

final class RouteResolver
{
    public function resolve(array $routes, string $path): array
    {
        if (isset($routes[$path])) {
            return [$routes[$path], []];
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

            return [$handler, $arguments];
        }

        throw new \Exception("No route found for path {$path}");
    }
}
