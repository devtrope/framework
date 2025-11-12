<?php

namespace Ludens\Http\Middleware;

use Ludens\Http\Request;
use Ludens\Http\Response;
use Ludens\Http\Support\SessionBag;

/**
 * Guest middleware.
 * 
 * Ensures user is NOT authenticated (for login/register pages).
 */
class GuestMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $session = new SessionBag();

        if ($session->has('user_id')) {
            return Response::redirect('/')
                ->withFlash('info', 'You are already logged in');
        }

        return $next($request);
    }
}
