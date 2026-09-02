<?php

namespace Ludens\Routing;

use Ludens\Http\Request;

final class Router
{
    public static function run(Request $request): void
    {
        $routeResolver = new RouteResolver();
        $resolvedRoute = $routeResolver->resolve(Route::getAllByRequestMethod($request->getHttpMethod()), $request->getPath());
        
        echo \call_user_func_array($resolvedRoute->getHandler(), $resolvedRoute->getParameters());
    }
}
