<?php

namespace Ludens\Routing\Dispatching;

use Ludens\Http\Middleware\MiddlewarePipeline;
use Ludens\Http\Request;
use Ludens\Http\Response;
use Ludens\Routing\Matching\RouteResolver;

/**
 * Dispatches HTTP requests to their appropriate controllers.
 *
 * Coordinates route resolution, controller instantiation, and response handling.
 *
 * @package Ludens\Routing\Dispatching
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class Dispatcher
{
    /**
     * @param RouteResolver $routeResolver
     * @param ControllerResolver $controllerResolver
     */
    public function __construct(
        private RouteResolver $routeResolver = new RouteResolver(),
        private ControllerResolver $controllerResolver = new ControllerResolver()
    ) {
    }

    /**
     * Dispatch a request to its controller and send the response.
     *
     * @param Request $request
     * @return void
     */
    public function dispatch(Request $request): void
    {
        $resolvedRoute = $this->routeResolver->resolve($request);

        [$controllerInstance, $method, $arguments] = $this->controllerResolver->resolve(
            $resolvedRoute,
            $request
        );

        $pipeline = new MiddlewarePipeline($resolvedRoute->middleware());

        $response = $pipeline->handle($request, function($request) use ($controllerInstance, $method, $arguments) {
            /**
             * @var callable
             */
            $callable = [$controllerInstance, $method];

            return call_user_func_array($callable, $arguments);
        });

        $this->sendResponse($response);
    }

     /**
     * Send the response to the client.
     *
     * @param mixed $response
     * @return void
     */
    private function sendResponse(mixed $response): void
    {
        if ($response instanceof Response) {
            $response->send();
            return;
        }

        /**
         * @var string $response
         */
        echo $response;
    }
}
