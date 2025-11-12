<?php

namespace Ludens\Http\Middleware;

use Ludens\Http\Request;
use Ludens\Http\Response;

/**
 * Middleware interface.
 *
 * @package Ludens\Http\Middleware
 * @author Quentin SCHIFFERLE <dev.trope@gmail.com>
 * @version 1.0.0
 */
interface MiddlewareInterface
{
    /**
     * Handle an incoming Request.
     *
     * @param Request $request
     * @param callable $next Next middleware in the pipeline
     * @return Response
     */
    public function handle(Request $request, callable $next): Response;
}
