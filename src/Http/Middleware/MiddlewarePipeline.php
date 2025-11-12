<?php

namespace Ludens\Http\Middleware;

use Ludens\Http\Request;
use Ludens\Http\Response;

/**
 * Middleware pipeline executor.
 *
 * Executes a stack of middleware in order.
 */
class MiddlewarePipeline
{
    /**
     * @param array $middleware
     */
    public function __construct(private array $middleware = []) {}

    /**
     * Execute the middleware pipeline.
     *
     * @param Request $request
     * @param callable $destination Final destination (controller)
     * @return Response
     */
    public function handle(Request $request, callable $destination): Response
    {
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            fn($next, $middleware) => fn($request) => $this->executeMiddleware($middleware, $request, $next),
            $destination
        );

        return $pipeline($request);
    }

    /**
     * Execute a single middleware.
     *
     * @param string $middlewareClass
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function executeMiddleware(string $middlewareClass, Request $request, callable $next): Response
    {
        if (! class_exists($middlewareClass)) {
            throw new \InvalidArgumentException("Middleware class {$middlewareClass} not found");
        }

        $middleware = new $middlewareClass();

        if (! $middleware instanceof MiddlewareInterface) {
            throw new \InvalidArgumentException(
                "Middleware {$middlewareClass} must implement MiddlewareInterface"
            );
        }

        return $middleware->handle($request, $next);
    }
}
