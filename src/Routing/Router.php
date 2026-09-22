<?php

namespace Ludens\Routing;

use Ludens\Http\Request;
use Ludens\Http\Response;
use UnexpectedValueException;

final class Router
{
    /**
     * @param Request $request
     * @return Response
     */
    public static function run(Request $request): Response
    {
        $routeResolver = new RouteResolver();
        $resolvedRoute = $routeResolver->resolve(Route::getAllByRequestMethod($request->getHttpMethod()), $request->getPath());
        
        $callback = [$resolvedRoute->getHandler()->getController(), $resolvedRoute->getHandler()->getMethod()];
        if (false === is_callable($callback)) {
            throw new UnexpectedValueException(\sprintf(
                'Method "%s" is not callable on %s',
                $resolvedRoute->getHandler()->getMethod(),
                get_debug_type($resolvedRoute->getHandler()->getController())
            ));
        }

        $response = \call_user_func_array($callback, $resolvedRoute->getParameters());
        if (!$response instanceof Response) {
            throw new UnexpectedValueException(\sprintf(
                'Expected controller action to return %s, got %s',
                Response::class,
                get_debug_type($response)
            ));
        }
        return $response;
    }
}
