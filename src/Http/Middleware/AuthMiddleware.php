<?php

namespace Ludens\Http\Middleware;

use Ludens\Http\Request;
use Ludens\Http\Response;
use Ludens\Http\Support\SessionBag;

/**
 * Authentication middleware.
 * 
 * Ensures user is authenticated before accessing protected routes.
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $session = new SessionBag();

        if (! $session->has('user_id')) {
            return Response::redirect('/login')
                ->withFlash('error', 'You must be logged in to access this page');
        }

        return $next($request);
    }
}
