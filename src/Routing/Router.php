<?php

namespace Ludens\Routing;

use Ludens\Http\Request;
use Ludens\Http\Response;

final class Router
{
    public static function run(Request $request): Response
    {
        $routeResolver = new RouteResolver();
        $resolvedRoute = $routeResolver->resolve(Route::getAllByRequestMethod($request->getHttpMethod()), $request->getPath());
        
        return \call_user_func_array($resolvedRoute->getHandler(), $resolvedRoute->getParameters());
    }
}
