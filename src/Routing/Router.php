<?php

namespace Ludens\Routing;

use Ludens\Http\Request;
use Ludens\Routing\Dispatching\Dispatcher;

/**
 * Main router class - facade for route dispatching.
 *
 * @package Ludens\Routing
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
class Router
{
    /**
     * Dispatch a request (static convenience method).
     *
     * @param Request $request
     * @return void
     */
    public static function dispatch(Request $request): void
    {
        $dispatcher = new Dispatcher();
        $dispatcher->dispatch($request);
    }
}
